<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Illuminate\Database\QueryException;


class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        // TokenExpiredException::class
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
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
        $this->reportable(function (Throwable $e) {
            //
        });
    }

     /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Exception $exception
     * @return mixed|void
     * @throws Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {

       // 参数验证错误的异常，我们需要返回 400 的 http code 和一句错误信息
        if ($exception instanceof ValidationException) {
            return response(['error' => array_first(array_collapse($exception->errors()))], 400);
        }
        if ($exception instanceof UnauthorizedHttpException) {
            $preException = $exception->getPrevious();
            if ($preException instanceof TokenExpiredException) {
                return response()->json(['error' => 'TOKEN已过期！','code' => 406]);
            } else if ($preException instanceof TokenInvalidException) {
                return response()->json(['error' => 'TOKEN无效！','code' => 406]);
            } else if ($preException instanceof TokenBlacklistedException) {
                return response()->json(['error' => 'TOKEN已退出！','code' => 406]);
            }
            if ($exception->getMessage() === 'Token not provided') {
                return response()->json(['error' => 'Token为空！','code' => 406]);
            }
        }

        if ($exception instanceof TokenExpiredException) {
            return response()->json(['error' => 'TOKEN已过期！','code' => 406]);
        } else if ($exception instanceof TokenInvalidException) {
            return response()->json(['error' => 'TOKEN无效！','code' => 406]);
        } else if ($exception instanceof TokenBlacklistedException) {
            return response()->json(['error' => 'TOKEN已退出！','code' => 406]);
        }
        if ($exception->getMessage() === 'Token not provided') {
            return response()->json(['error' => 'Token为空！','code' => 406]);
        }
        if ($exception->getMessage() === 'Unauthenticated.') {
            return response()->json(['error' => 'TOKEN失效！','code' => 406]);
        }

    }
}
