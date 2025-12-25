<?php

namespace App\Services;

use App\Models\License;
use App\Models\LicenseEvent;

class LicenseEventService
{
    /**
     * Log a license event.
     *
     * @param array<string, mixed>|null $eventData
     */
    public function logEvent(
        License $license,
        string $eventType,
        string $description,
        ?array $eventData = null
    ): LicenseEvent {
        return LicenseEvent::create([
            'license_id' => $license->id,
            'event_type' => $eventType,
            'description' => $description,
            'event_data' => $eventData,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get events for a license.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEventsForLicense(int $licenseId)
    {
        return LicenseEvent::where('license_id', $licenseId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get events by type.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEventsByType(string $eventType)
    {
        return LicenseEvent::byType($eventType)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
