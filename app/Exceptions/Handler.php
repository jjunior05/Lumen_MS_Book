<?php

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use Illuminate\Http\Response as httpResponse;

class Handler extends ExceptionHandler
{
    use ApiResponse;

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
        // http error
        if ($exception instanceof HttpException) {
            $code = $exception->getStatusCode();
            $message = httpResponse::$statusTexts[$code];

            return $this->errorResponse($message, $code);
        }

        // instance not found
        if ($exception instanceof ModelNotFoundException) {
            $model = strtolower(basename($exception->getModel()));
            return $this->errorResponse('Instance not found for ' . $model, httpResponse::HTTP_NOT_FOUND);
        }

        // No autentication
        if ($exception instanceof AuthenticationException) {

            return $this->errorResponse($exception->getMessage(), httpResponse::HTTP_UNAUTHORIZED);
        }

        // validations
        if ($exception instanceof ValidationException) {
            $erros = $exception->validator->errors()->getMessages();

            return $this->errorResponse($erros, httpResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (env('APP_DEBUG')) {
            return parent::render($request, $exception);
        }

        return $this->errorResponse('Unexpected error. Try later', httpResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}
