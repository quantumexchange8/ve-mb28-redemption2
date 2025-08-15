<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Code;
use Illuminate\Console\Command;
use App\Mail\CodeExpiryNotification;
use Illuminate\Support\Facades\Mail;

class CheckCodeExpiry extends Command
{
    protected $signature = 'codes:check-expiry';

    protected $description = 'Check codes expiring in 7 days and notify owners';

    public function handle()
    {
        $targetDate = Carbon::now()->addDays(7)->toDateString();

        Code::with('user:id,email')
            ->whereDate('expired_date', $targetDate)
            ->chunk(500, function ($codes) {
                foreach ($codes as $code) {
                    if (!$code->user) {
                        continue;
                    }
                    Mail::to($code->user->email)->send(new CodeExpiryNotification('supporteam@simmigoh.info', $code));
                    // $this->info("Notification sent to user {$code->user->email} for code ID {$code->id}");
                }
            });
    }
}
