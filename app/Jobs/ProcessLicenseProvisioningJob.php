<?php

namespace App\Jobs;

use App\Models\License;
use App\Services\LicenseService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessLicenseProvisioningJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public License $license,
        public int $numberOfKeys = 1
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(LicenseService $licenseService): void
    {
        Log::info('Processing license provisioning', [
            'license_id' => $this->license->id,
            'number_of_keys' => $this->numberOfKeys,
        ]);

        for ($i = 0; $i < $this->numberOfKeys; $i++) {
            $licenseService->generateLicenseKey($this->license);
        }

        Log::info('License provisioning completed', [
            'license_id' => $this->license->id,
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('License provisioning failed', [
            'license_id' => $this->license->id,
             'exception' => $exception->getMessage(),
        ]);
    }
}
