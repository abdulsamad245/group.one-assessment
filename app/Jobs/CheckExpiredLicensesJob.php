<?php

namespace App\Jobs;

use App\Repositories\LicenseRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckExpiredLicensesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(LicenseRepository $licenseRepository): void
    {
        Log::info('Checking for expired licenses');

        $expiredLicenses = $licenseRepository->getExpired();

        foreach ($expiredLicenses as $license) {
            $license->update(['status' => 'expired']);

            Log::info('License marked as expired', [
                'license_id' => $license->id,
                'customer_email' => $license->customer_email,
            ]);
        }

        Log::info('Expired licenses check completed', [
            'count' => $expiredLicenses->count(),
        ]);
    }
}
