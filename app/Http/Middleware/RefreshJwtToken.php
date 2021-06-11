<?php // 无痛刷新验证jwt token

namespace App\Http\Middleware;

use Closure;
use Auth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class RefreshJwtToken extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param Closure $next
     * @param mixed ...$guards
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|mixed
     * @throws JWTException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        // 获取 guard 值
        $guard = $this->shouldUse($guards);
        // 检查此次请求中是否带有 TOKEN，如果没有则抛出异常。
        $this->checkForToken($request);
        try {
            // 检查此次请求中的角色是否属于正常权限
            $this->checkForClaim($guard);

            // 检测用户的登录状态，如果正常则通过
            if (Auth::guard($guard)->payload())  {
                // $this->auth->shouldUse($role);
               return $next($request);
           }

            if ($this->auth->parseToken()->authenticate()) {
                return $next($request);
            }

            throw new UnauthorizedHttpException('jwt-auth', '未登录');
        } catch (TokenExpiredException $exception) { // 捕捉 TOKEN 过期所抛出的 TokenExpiredException  异常
            // 刷新用户的 TOKEN 并将它添加到响应头中
            try {
                // 刷新用户的 token
                $token = $this->auth->refresh();

                // 使用一次性登录以保证此次请求的成功
                Auth::guard($guard)->onceUsingId($this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray()['sub']);
            } catch (JWTException $exception) {
                // 如果捕获到此异常，即代表 refresh 也过期了，用户无法刷新令牌，需要重新登录。
                throw new UnauthorizedHttpException('jwt-auth', '登录已过期，请重新登录');
            }
        }

        // 在响应头中返回新的 TOKEN
        return $this->setAuthenticationHeader($next($request), $token);
    }

    /**
     * set and get auth default driver name
     *
     * @param $guards
     * @return mixed
     */
    public function shouldUse($guards)
    {
        $name = array_get($guards, 0, 'web');
        $name = $name ?: Auth::getDefaultDriver();

        Auth::setDefaultDriver($name);

        return $name;
    }

    /**
     * 验证角色权限是否正常
     *
     * @param $guard
     * @throws JWTException
     */
    public function checkForClaim($guard)
    {
        if ($guard != $this->auth->parseToken()->getClaim('role')) {
            throw new UnauthorizedHttpException('jwt-auth', 'User role error');
        }
    }
}
