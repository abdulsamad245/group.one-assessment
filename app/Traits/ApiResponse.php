<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    /**
     * Success response.
     *
     * @param int $code
     * @param string $message
     * @param mixed $data
     * @param array<string, mixed> $meta
     * @return JsonResponse
     */
    protected function response(
        int $code = Response::HTTP_OK,
        string $message = '',
        mixed $data = null,
        array $meta = []
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $code);
    }

    /**
     * Error response.
     *
     * @param int $code
     * @param string $message
     * @param mixed $errors
     * @return JsonResponse
     */
    protected function error(
        int $code = Response::HTTP_BAD_REQUEST,
        string $message = '',
        mixed $errors = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Paginated response.
     *
     * @param mixed $paginator
     * @param string $message
     * @return JsonResponse
     */
    protected function paginatedResponse(
        mixed $paginator,
        string $message = ''
    ): JsonResponse {
        return $this->response(
            Response::HTTP_OK,
            $message,
            $paginator->items(),
            [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ]
        );
    }

    /**
     * Created response.
     *
     * @param string $message
     * @param mixed $data
     * @return JsonResponse
     */
    protected function created(string $message, mixed $data = null): JsonResponse
    {
        return $this->response(Response::HTTP_CREATED, $message, $data);
    }

    /**
     * No content response.
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function noContent(string $message = ''): JsonResponse
    {
        return $this->response(Response::HTTP_NO_CONTENT, $message);
    }

    /**
     * Not found response.
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function notFound(string $message = ''): JsonResponse
    {
        return $this->error(Response::HTTP_NOT_FOUND, $message);
    }

    /**
     * Validation error response.
     *
     * @param string $message
     * @param mixed $errors
     * @return JsonResponse
     */
    protected function validationError(string $message, mixed $errors = null): JsonResponse
    {
        return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $message, $errors);
    }

    /**
     * Unauthorized response.
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function unauthorized(string $message = ''): JsonResponse
    {
        return $this->error(Response::HTTP_UNAUTHORIZED, $message);
    }

    /**
     * Forbidden response.
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function forbidden(string $message = ''): JsonResponse
    {
        return $this->error(Response::HTTP_FORBIDDEN, $message);
    }
}
