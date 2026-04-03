<?php

namespace App\Jobs;

use App\Models\BlastHistory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendBlastMessage implements ShouldQueue
{
    use Queueable;

    protected $blastHistoryId;
    protected $phoneNumber;
    protected $affiliateName;
    protected $campaignName;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = 5;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct($blastHistoryId, $phoneNumber, $affiliateName = null, $campaignName = null)
    {
        $this->blastHistoryId = $blastHistoryId;
        $this->phoneNumber = $phoneNumber;
        $this->affiliateName = $affiliateName;
        $this->campaignName = $campaignName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Sending blast message for history ID: ' . $this->blastHistoryId);
        $blastHistory = BlastHistory::find($this->blastHistoryId);

        if (!$blastHistory) {
            Log::warning('Blast history not found');
            return;
        }

        $apiKey = config('services.watzap.api_key');
        $numberKey = config('services.watzap.number_key');
        $numberKey2 = config('services.watzap.number_key_2');
        $numberKey3 = config('services.watzap.number_key_3');
        $changeNumber = config('services.watzap.change_number', 50);

        if (empty($apiKey) || empty($numberKey)) {
            Log::warning('WatsApp API credentials not configured');
            $blastHistory->increment('failed_count');
            return;
        }

        try {
            $phoneNumber = $this->phoneNumber;
            $phoneNumber = preg_replace('/^0/', '62', $phoneNumber);

            // Replace placeholders with actual values
            $message = $blastHistory->message_content;

            if ($this->campaignName) {
                $message = str_replace('{campaign}', $this->campaignName, $message);
            }
            if ($this->affiliateName) {
                $message = str_replace('{user}', $this->affiliateName, $message);
            }

            // Alternate number key based on change_number config
            $messageCount = $blastHistory->success_count + $blastHistory->failed_count;
            $keyIndex = intval($messageCount / $changeNumber);

            $numberKeys = array_filter([
                $numberKey,
                $numberKey2,
                $numberKey3,
            ]);
            $numberKeys = array_values($numberKeys); // Reindex array

            $selectedKey = $numberKeys[$keyIndex % count($numberKeys)];

            $response = Http::post('https://api.watzap.id/v1/send_message', [
                'api_key' => $apiKey,
                'number_key' => $selectedKey,
                'phone_no' => $phoneNumber,
                'message' => $message,
                'wait_until_send' => '0',
            ]);

            if ($response->successful()) {
                Log::info('Successfully sent message to ' . $this->phoneNumber);
                $blastHistory->increment('success_count');
            } else {
                Log::error('Failed to send message to ' . $this->phoneNumber . ': ' . $response->body());
                $blastHistory->increment('failed_count');
            }
        } catch (\Exception $e) {
            Log::error('Error sending blast message: ' . $e->getMessage());
            $blastHistory->increment('failed_count');
        }

        // Update status if all messages processed
        if (($blastHistory->success_count + $blastHistory->failed_count) >= $blastHistory->total_affiliate) {
            Log::info('Blast message completed for history ID: ' . $this->blastHistoryId);
            $blastHistory->update(['status' => 'completed']);
        } else {
            $blastHistory->update(['status' => 'sending']);
        }
    }
}
