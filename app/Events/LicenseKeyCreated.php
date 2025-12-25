<?php

namespace App\Events;

use App\Models\LicenseKey;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event dispatched when a new license key is created for a customer.
 */
class LicenseKeyCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public LicenseKey $licenseKey,
        public string $description = 'License key created'
    ) {
    }
}
