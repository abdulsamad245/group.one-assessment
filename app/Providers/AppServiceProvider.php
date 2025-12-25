<?php

namespace App\Providers;

use App\Events\LicenseActivated;
use App\Events\LicenseCreated;
use App\Events\LicenseDeactivated;
use App\Events\LicenseKeyGenerated;
use App\Events\LicenseReactivated;
use App\Events\LicenseRenewed;
use App\Events\LicenseSuspended;
use App\Events\LicenseUpdated;
use App\Listeners\LogLicenseEvent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register license event listeners
        Event::listen(
            LicenseActivated::class,
            [LogLicenseEvent::class, 'handleLicenseActivated']
        );

        Event::listen(
            LicenseDeactivated::class,
            [LogLicenseEvent::class, 'handleLicenseDeactivated']
        );

        Event::listen(
            LicenseCreated::class,
            [LogLicenseEvent::class, 'handleLicenseCreated']
        );

        Event::listen(
            LicenseUpdated::class,
            [LogLicenseEvent::class, 'handleLicenseUpdated']
        );

        Event::listen(
            LicenseKeyGenerated::class,
            [LogLicenseEvent::class, 'handleLicenseKeyGenerated']
        );

        Event::listen(
            LicenseSuspended::class,
            [LogLicenseEvent::class, 'handleLicenseSuspended']
        );

        Event::listen(
            LicenseReactivated::class,
            [LogLicenseEvent::class, 'handleLicenseReactivated']
        );

        Event::listen(
            LicenseRenewed::class,
            [LogLicenseEvent::class, 'handleLicenseRenewed']
        );
    }
}
