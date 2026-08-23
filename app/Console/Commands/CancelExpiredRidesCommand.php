<?php

namespace App\Console\Commands;

use App\Services\RideAutoCancelService;
use Illuminate\Console\Command;

class CancelExpiredRidesCommand extends Command
{
    protected $signature = 'rides:cancel-expired {--minutes=5 : Minutes to wait before auto-canceling}';

    protected $description = 'Cancel rides that were not accepted within the timeout window';

    public function handle(RideAutoCancelService $service): int
    {
        $minutes = max(1, (int) $this->option('minutes'));
        $cancelled = $service->cancelExpiredRides($minutes);

        $this->info("Auto-canceled {$cancelled} ride(s) older than {$minutes} minute(s) without acceptance.");

        return self::SUCCESS;
    }
}
