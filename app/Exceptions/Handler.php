<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        $errorCode = $exception->getCode();
        if ($exception instanceof HttpException) {
            $errorCode = $exception->getStatusCode();
        }

        if (empty($errorCode) || !is_numeric($errorCode)) {
            $errorCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $response = [
            'status' => 'error',
            'message' => $exception->getMessage(),
            'code' => $errorCode,
        ];

        if ($exception instanceof QueryException) {
            // do not expose details to queries that will give away details about the DB structure
            $response['message'] = 'Query error, details available in the app logs (suppressed for security reasons)';
        }

        if ($exception instanceof ValidationException) {
            $errorCode = Response::HTTP_UNPROCESSABLE_ENTITY;
            $response['code'] = $errorCode;
            $response['errors'] = $exception->errors();
        }

        return response()->json($response, (int) $errorCode);
    }
}
