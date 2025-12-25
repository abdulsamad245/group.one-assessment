<?php

namespace App\Jobs;

use App\Mail\ActivationNotification;
use App\Models\Activation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendActivationNotificationJob implements ShouldQueue
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
    public $backoff = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Activation $activation
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $licenseKey = $this->activation->licenseKey;

        Log::info('Sending activation notification', [
            'activation_id' => $this->activation->id,
            'license_key_id' => $licenseKey->id,
            'customer_email' => $licenseKey->customer_email,
        ]);

        // Send activation notification email
        Mail::to($licenseKey->customer_email)->send(new ActivationNotification($this->activation));

        Log::info('Activation notification sent', [
            'activation_id' => $this->activation->id,
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Activation notification failed', [
            'activation_id' => $this->activation->id,
            'exception' => $exception->getMessage(),
        ]);
    }
}
