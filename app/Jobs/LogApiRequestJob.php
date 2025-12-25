<?php

namespace App\Jobs;

use App\Models\ApiLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LogApiRequestJob implements ShouldQueue
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
     * Create a new job instance.
     */
    public function __construct(
        protected array $logData
    ) {
        // Ensure all array/object elements are properly serialized
        $this->logData = array_map(function ($item) {
            // Handle Carbon/DateTime objects - convert to ISO string
            if ($item instanceof \DateTimeInterface) {
                return $item->format('Y-m-d H:i:s');
            }
            // Handle arrays and other objects - JSON encode
            if (is_array($item) || is_object($item)) {
                return json_encode($item);
            }
            return $item;
        }, $logData);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            ApiLog::create([
                'id' => Str::uuid()->toString(),
                'correlation_id' => $this->logData['correlation_id'] ?? Str::uuid()->toString(),
                'method' => $this->logData['method'] ?? null,
                'path' => $this->logData['path'] ?? null,
                'full_path' => $this->logData['full_path'] ?? null,
                'ip_address' => $this->logData['ip_address'] ?? null,
                'user_agent' => $this->logData['user_agent'] ?? null,
                'request_headers' => $this->logData['request_headers'] ?? null,
                'request_body' => $this->logData['request_body'] ?? null,
                'response_headers' => $this->logData['response_headers'] ?? null,
                'response_body' => $this->logData['response_body'] ?? null,
                'status_code' => $this->logData['status_code'] ?? null,
                'content_type' => $this->logData['content_type'] ?? null,
                'referer' => $this->logData['referer'] ?? null,
                'requested_at' => $this->logData['requested_at'] ?? null,
                'responded_at' => $this->logData['responded_at'] ?? null,
                'duration_ms' => $this->logData['duration_ms'] ?? null,
                'user_id' => $this->logData['user_id'] ?? null,
                'brand_id' => $this->logData['brand_id'] ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::error('LogApiRequestJob.handle (error)', [
                  'exception' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('LogApiRequestJob.failed', [
            'exception' => $exception->getMessage(),
        ]);
    }
}
