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
    public function __construct($blastHistoryId, $phoneNumber)
    {
        $this->blastHistoryId = $blastHistoryId;
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $blastHistory = BlastHistory::find($this->blastHistoryId);

        if (!$blastHistory) {
            return;
        }

        $apiKey = config('services.watzap.api_key');
        $numberKey = config('services.watzap.number_key');

        if (empty($apiKey) || empty($numberKey)) {
            Log::warning('WatsApp API credentials not configured');
            $blastHistory->increment('failed_count');
            return;
        }

        try {
            $phoneNumber = $this->phoneNumber;
            $phoneNumber = preg_replace('/^0/', '62', $phoneNumber);
            $response = Http::post('https://api.watzap.id/v1/send_message', [
                'api_key' => $apiKey,
                'number_key' => $numberKey,
                'phone_no' => $phoneNumber,
                'message' => $blastHistory->message_content,
                'wait_until_send' => '0',
            ]);

            if ($response->successful()) {
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
            $blastHistory->update(['status' => 'completed']);
        } else {
            $blastHistory->update(['status' => 'sending']);
        }
    }
}
