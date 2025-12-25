<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Exception;

class HealthController extends Controller
{
    /**
     * Health check endpoint for monitoring and observability.
     *
     * Checks:
     * - Application status
     * - Database connectivity
     * - Redis connectivity
     * - Queue connectivity
     *
     * @return JsonResponse
     */
    public function check(): JsonResponse
    {
        $checks = [
            'app' => $this->checkApp(),
            'database' => $this->checkDatabase(),
            'redis' => $this->checkRedis(),
            'queue' => $this->checkQueue(),
        ];

        $allHealthy = collect($checks)->every(fn($check) => $check['status'] === 'healthy');

        return response()->json([
            'status' => $allHealthy ? 'healthy' : 'unhealthy',
            'timestamp' => now()->toIso8601String(),
            'checks' => $checks,
        ], $allHealthy ? 200 : 503);
    }

    /**
     * Check application status.
     *
     * @return array<string, mixed>
     */
    private function checkApp(): array
    {
        return [
            'status' => 'healthy',
            'version' => config('app.version', '1.0.0'),
            'environment' => config('app.env'),
        ];
    }

    /**
     * Check database connectivity.
     *
     * @return array<string, mixed>
     */
    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            return [
                'status' => 'healthy',
                'connection' => config('database.default'),
            ];
        } catch (Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => 'Database connection failed',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check Redis connectivity.
     *
     * @return array<string, mixed>
     */
    private function checkRedis(): array
    {
        try {
            Redis::ping();
            return [
                'status' => 'healthy',
            ];
        } catch (Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => 'Redis connection failed',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check queue connectivity.
     *
     * @return array<string, mixed>
     */
    private function checkQueue(): array
    {
        try {
            $connection = config('queue.default');
            return [
                'status' => 'healthy',
                'connection' => $connection,
            ];
        } catch (Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => 'Queue check failed',
                'message' => $e->getMessage(),
            ];
        }
    }
}
