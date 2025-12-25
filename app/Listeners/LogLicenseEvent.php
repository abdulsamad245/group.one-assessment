<?php

namespace App\Listeners;

use App\Enums\LicenseEventType;
use App\Events\LicenseActivated;
use App\Events\LicenseCreated;
use App\Events\LicenseDeactivated;
use App\Events\LicenseKeyGenerated;
use App\Events\LicenseReactivated;
use App\Events\LicenseRenewed;
use App\Events\LicenseSuspended;
use App\Events\LicenseUpdated;
use App\Models\LicenseEvent;

class LogLicenseEvent
{
    /**
     * Handle license activated event.
     */
    public function handleLicenseActivated(LicenseActivated $event): void
    {
        LicenseEvent::create([
            'license_id' => $event->license->id,
            'event_type' => LicenseEventType::ACTIVATED->value,
            'description' => $event->description,
            'event_data' => $event->eventData,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle license deactivated event.
     */
    public function handleLicenseDeactivated(LicenseDeactivated $event): void
    {
        LicenseEvent::create([
            'license_id' => $event->license->id,
            'event_type' => LicenseEventType::DEACTIVATED->value,
            'description' => $event->description,
            'event_data' => null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle license created event.
     */
    public function handleLicenseCreated(LicenseCreated $event): void
    {
        LicenseEvent::create([
            'license_id' => $event->license->id,
            'event_type' => LicenseEventType::CREATED->value,
            'description' => $event->description,
            'event_data' => $event->eventData,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle license updated event.
     */
    public function handleLicenseUpdated(LicenseUpdated $event): void
    {
        LicenseEvent::create([
            'license_id' => $event->license->id,
            'event_type' => LicenseEventType::UPDATED->value,
            'description' => $event->description,
            'event_data' => $event->eventData,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle license key generated event.
     */
    public function handleLicenseKeyGenerated(LicenseKeyGenerated $event): void
    {
        LicenseEvent::create([
            'license_id' => $event->license->id,
            'event_type' => LicenseEventType::KEY_GENERATED->value,
            'description' => $event->description,
            'event_data' => null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle license suspended event.
     */
    public function handleLicenseSuspended(LicenseSuspended $event): void
    {
        LicenseEvent::create([
            'license_id' => $event->license->id,
            'event_type' => LicenseEventType::SUSPENDED->value,
            'description' => $event->description,
            'event_data' => null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle license reactivated event.
     */
    public function handleLicenseReactivated(LicenseReactivated $event): void
    {
        LicenseEvent::create([
            'license_id' => $event->license->id,
            'event_type' => LicenseEventType::REACTIVATED->value,
            'description' => $event->description,
            'event_data' => null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle license renewed event.
     */
    public function handleLicenseRenewed(LicenseRenewed $event): void
    {
        LicenseEvent::create([
            'license_id' => $event->license->id,
            'event_type' => LicenseEventType::RENEWED->value,
            'description' => $event->description,
            'event_data' => null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
