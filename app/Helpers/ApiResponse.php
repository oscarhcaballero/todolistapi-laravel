<?php
namespace App\Helpers;

/**
 * Class ApiResponse
 *
 * This helper class provides a standardized way to format API responses.
 * It includes methods for success and error responses, ensuring consistency
 * across the application.
 */
class ApiResponse
{
    /**
     * Generate a standardized success response.
     *
     * @param mixed $data The data to include in the response.
     * @param string $message A message describing the success (optional).
     * @param int $status The HTTP status code (default: 200).
     * @return \Illuminate\Http\JsonResponse The formatted JSON response.
     */
    public static function success($data, $message = '', $status = 200)
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
            'status' => $status,
        ], $status);
    }

    /**
     * Generate a standardized error response.
     *
     * @param string $message A message describing the error.
     * @param int $status The HTTP status code (default: 400).
     * @param array $errors An array of errors (optional).
     * @return \Illuminate\Http\JsonResponse The formatted JSON response.
     */
    public static function error($message, $status = 400, $errors = [])
    {
        return response()->json([
            'message' => $message,
            'errors' => $errors,
            'status' => $status,
        ], $status);
    }
}