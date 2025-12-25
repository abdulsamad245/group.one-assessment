<?php

namespace App\Http\Middleware;

use App\Jobs\LogApiRequestJob;
use App\Support\Logging\Sanitizer;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequests
{
    /**
     * Maximum response body size to log (10KB).
     */
    private const MAX_RESPONSE_SIZE = 10000;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        // Generate or retrieve correlation ID
        $correlationId = $request->header('X-Correlation-ID', Str::uuid()->toString());
        $request->headers->set('X-Correlation-ID', $correlationId);

        // Build initial log data
        $fullPath = $request->path();
        $logData = [
            'correlation_id' => $correlationId,
            'method' => $request->method(),
            'path' => strlen($fullPath) > 255 ? substr($fullPath, 0, 255) : $fullPath,
            'full_path' => strlen($fullPath) > 255 ? $fullPath : null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'request_headers' => json_encode(Sanitizer::sanitizeHeaders($request->headers->all())),
            'requested_at' => now(),
            'referer' => $request->headers->get('referer'),
            'request_body' => $this->sanitizeRequest($request),
            'content_type' => $request->headers->get('content-type'),
            'user_id' => null,
            'brand_id' => null,
        ];

        // Process the request
        $response = $next($request);

        // Capture user and brand after request processing
        $user = $request->user();
        if ($user) {
            $logData['user_id'] = $user->id;
            $logData['brand_id'] = $user->brand_id ?? null;
        }

        // Update log with response data
        $logData['status_code'] = $response->getStatusCode();
        $logData['response_headers'] = json_encode(Sanitizer::sanitizeHeaders($response->headers->all()));
        $logData['responded_at'] = now();
        $logData['duration_ms'] = (microtime(true) - $startTime) * 1000;

        // Store response body if appropriate
        if ($this->shouldLogResponse($response)) {
            $logData['response_body'] = $this->sanitizeResponse($response);
        }

        // Add correlation ID to response headers
        $response->headers->set('X-Correlation-ID', $correlationId);

        // Dispatch to queue
        LogApiRequestJob::dispatch($logData);

        return $response;
    }

    /**
     * Sanitize request body for logging.
     */
    protected function sanitizeRequest(Request $request): ?string
    {
        $input = $request->all();

        if (empty($input)) {
            return null;
        }

        $sanitized = Sanitizer::sanitizePayload($input);
        return json_encode($sanitized) ?: null;
    }

    /**
     * Sanitize response body for logging.
     */
    protected function sanitizeResponse(Response $response): ?string
    {
        $content = $response->getContent();

        if (!$content || strlen($content) > self::MAX_RESPONSE_SIZE) {
            return null;
        }

        // Try to decode and sanitize JSON responses
        $decoded = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return json_encode(Sanitizer::sanitizePayload($decoded));
        }

        return $content;
    }

    /**
     * Determine if response should be logged.
     */
    protected function shouldLogResponse(Response $response): bool
    {
        $contentType = $response->headers->get('Content-Type');

        $loggableTypes = [
            'application/json',
            'application/xml',
            'text/plain',
            'text/html',
        ];

        foreach ($loggableTypes as $type) {
            if ($contentType && str_contains($contentType, $type)) {
                return $response->getStatusCode() < 500;
            }
        }

        return false;
    }
}
