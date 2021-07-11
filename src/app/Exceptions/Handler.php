<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (Exception $e, $request) {
            if ($request->wantsJson()) {
                return $this->handleApiException($request, $e);
            }
        });
    }

    protected function handleApiException($request, $exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            return $this->modelNotFound($exception);
        }

        $exception = $this->prepareException($exception);

        if ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        }

        if ($exception instanceof AccessDeniedHttpException) {
            return $this->unauthorized($request, $exception);
        }

        if ($exception instanceof NotFoundHttpException) {
            return $this->notfound($request, $exception);
        }
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json(['message' => $exception->getMessage()], 401);
    }

    protected function unauthorized($request, AccessDeniedHttpException $exception)
    {
        $message =  __('message.unauthorized');
        return response()->json(['message' => $message], 403);
    }

    protected function notfound($request, NotFoundHttpException $exception)
    {
        return response()->json(['message' => $exception->getMessage() ?? __('message.not_found')], 404);
    }

    protected function modelNotFound(ModelNotFoundException $exception)
    {
        return response()->json(['message' => $exception->getMessage()], 404);
    }
}
