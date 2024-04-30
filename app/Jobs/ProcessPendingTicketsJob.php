<?php

namespace App\Jobs;

use App\Models\VeXe;
use Illuminate\Bus\Queue\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessPendingTicketsJob implements ShouldQueue
{
    use SerializesModels, InteractsWithQueue;

    public function handle()
    {
        // Xử lý logic hủy giữ chỗ cho vé xe khách quá hạn
        $pendingTickets = VeXe::where('status', 'pending')->get();

        foreach ($pendingTickets as $ticket) {
            $elapsedTime = time() - $ticket->created_at->timestamp;
            $expirationTime = 60 * 15; // 15 phút

            if ($elapsedTime > $expirationTime) {
                $ticket->status = 'no';
                $ticket->save();
            }
        }
    }
}
