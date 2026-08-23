<?php

namespace Tests\Unit;

use App\Models\Ride;
use App\Services\RideAutoCancelService;
use Tests\TestCase;

class RideAutoCancelTest extends TestCase
{
    public function test_pending_statuses_include_requested_and_in_progress(): void
    {
        $this->assertContains('requested', RideAutoCancelService::PENDING_STATUSES);
        $this->assertContains('in_progress', RideAutoCancelService::PENDING_STATUSES);
    }

    public function test_cancel_ride_skips_non_pending_status(): void
    {
        $ride = new Ride([
            'id' => 1,
            'user_id' => 10,
            'status' => 'accepted',
        ]);

        $service = app(RideAutoCancelService::class);

        $this->assertFalse($service->cancelRide($ride));
    }

    public function test_timeout_default_is_five_minutes(): void
    {
        $this->assertSame(5, RideAutoCancelService::TIMEOUT_MINUTES);
    }
}
