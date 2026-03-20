<?php

namespace App\Console\Commands;

use App\Models\BlastSchedule;
use App\Models\BlastHistory;
use App\Jobs\SendBlastMessage;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Carbon\Carbon;

#[Signature('app:process-blast-schedules')]
#[Description('Process scheduled blast messages that are due to run')]
class ProcessBlastSchedules extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $schedules = BlastSchedule::where('is_active', true)
            ->where('next_run_at', '<=', now())
            ->get();

        if ($schedules->isEmpty()) {
            $this->info('No scheduled blasts to process.');
            return Command::SUCCESS;
        }

        $count = 0;
        foreach ($schedules as $schedule) {
            $campaign = $schedule->campaign;

            if (!$campaign || $campaign->affiliates()->count() === 0) {
                $this->warn("Schedule ID {$schedule->id} has no valid campaign or affiliates. Skipping.");
                continue;
            }

            // Create a blast history record
            $blastHistory = BlastHistory::create([
                'campaign_id' => $schedule->campaign_id,
                'message_type' => $schedule->message_type,
                'message_content' => $schedule->message_content,
                'total_affiliate' => $campaign->affiliates()->count(),
                'status' => 'pending',
            ]);

            // Queue messages for all affiliates
            $delaySeconds = 0;
            foreach ($campaign->affiliates as $affiliate) {
                SendBlastMessage::dispatch($blastHistory->id, $affiliate->phone, $affiliate->name, $campaign->name)
                    ->delay(now()->addSeconds($delaySeconds));
                $delaySeconds += 15;
            }

            // Calculate next run time based on frequency
            $nextRun = $this->calculateNextRun($schedule);
            $schedule->update([
                'next_run_at' => $nextRun,
                'last_run_at' => now(),
            ]);

            $this->info("Processed schedule ID {$schedule->id} - Campaign: {$campaign->name}");
            $count++;
        }

        $this->info("Total scheduled blasts processed: {$count}");
        return Command::SUCCESS;
    }

    /**
     * Calculate next run time based on frequency
     */
    private function calculateNextRun(BlastSchedule $schedule): Carbon
    {
        $baseTime = $schedule->next_run_at ?? now();
        $time = $schedule->schedule_time;

        switch ($schedule->frequency) {
            case 'hourly':
                return $baseTime->addHour();

            case 'daily':
                if ($time) {
                    $next = $baseTime->addDay()->setTimeFromTimeString($time);
                } else {
                    $next = $baseTime->addDay();
                }
                return $next;

            case 'weekly':
                $next = $baseTime->addWeek();
                if ($time) {
                    $next->setTimeFromTimeString($time);
                }
                if ($schedule->schedule_day) {
                    $next->setDay((int)$schedule->schedule_day);
                }
                return $next;

            case 'monthly':
                $next = $baseTime->addMonth();
                if ($time) {
                    $next->setTimeFromTimeString($time);
                }
                if ($schedule->schedule_day) {
                    $next->setDay((int)$schedule->schedule_day);
                }
                return $next;

            default:
                return $baseTime->addDay();
        }
    }
}
