<?php
namespace App\Exceptions;

use App\Exceptions\TaskException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Class Handler
 *
 * This class is responsible for handling exceptions in the application.
 * It provides custom handling for specific exceptions, such as `TaskException`,
 * `NotFoundHttpException`, and `ValidationException`, ensuring consistent
 * error responses for API requests.
 */
class Handler extends ExceptionHandler
{
    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        /**
         * Handle TaskException.
         *
         * This handler returns a JSON response with a 400 status code
         * and the error message from the exception.
         *
         * @param TaskException $e The exception instance.
         * @param Request $request The incoming HTTP request.
         * @return \Illuminate\Http\JsonResponse The JSON response.
         */
        $this->renderable(function (TaskException $e, $request) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        });

        /**
         * Handle NotFoundHttpException for API routes.
         *
         * This handler checks if the request is for an API route (`api/*`)
         * and returns a JSON response with a 404 status code and a "Resource not found" message.
         *
         * @param NotFoundHttpException $e The exception instance.
         * @param Request $request The incoming HTTP request.
         * @return \Illuminate\Http\JsonResponse|null The JSON response, or null if not an API route.
         */
        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Resource not found.',
                ], 404);
            }
        });

        /**
         * Handle ValidationException.
         *
         * This handler returns a JSON response with a 422 status code,
         * a "Validation failed" message, and the validation errors.
         *
         * @param ValidationException $e The exception instance.
         * @param Request $request The incoming HTTP request.
         * @return \Illuminate\Http\JsonResponse The JSON response.
         */
        $this->renderable(function (ValidationException $e, $request) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        });
    
        /**
         * Handle all other exceptions.
         *
         * This handler returns a JSON response with a 500 status code
         * and a generic error message for unexpected errors.
         *
         * @param Throwable $e The exception instance.
         * @param Request $request The incoming HTTP request.
         * @return \Illuminate\Http\JsonResponse|null The JSON response, or null if not an API route.
         */
        $this->renderable(function (Throwable $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'An unexpected error occurred.',
                ], 500);
            }
        });

    }
    

 
}