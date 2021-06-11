<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemAdmin;
use JWTAuth;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public $loginAfterSignUp = true;

    public function addUsers(Request $request)
    {

        $user = new SystemAdmin();
        $user->real_name = $request->real_name;
        $user->account = $request->account;
        $user->pwd = bcrypt($request->password);
        $user->save();

        if ($this->loginAfterSignUp) {
            return $this->login($request);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    public function login(Request $request)
    {
        $input = $request->only('account', 'password');
        $jwt_token = null;
        
        if (!$jwt_token = $this->guard()->attempt($input)) {
            return response()->json([
                'code' => 201,
                'message' => '账号或密码错误,请重新输入',
            ], 401);
        }
        
        return $this->respondWithToken($jwt_token);

    }

    // 重写退出，清除token
    public function logout()
    {
        $this->guard()->logout();

        return response()->json([
            'code'      =>  200,
            'message'   =>  '退出成功'
        ]);
    }

    /**
     * Refresh a token.
     * 刷新token，如果开启黑名单，以前的token便会失效。
     * 值得注意的是用上面的getToken再获取一次Token并不算做刷新，两次获得的Token是并行的，即两个都可用。
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh(),'刷新成功');
    }

    // 组合返回的数据
    protected function respondWithToken($token, $message = '登录成功')
    {
        $user=$this->guard()->user();
        // 写入基本信息
        $data = $user->only('id', 'account','real_name','roles', 'level','status');

        return response()->json([
            'code'      =>  200,
            'message'   =>  $message,
            'data'      =>  $data,
            'token'     =>  $token,
        ]);
    }

    // 重写验证过程中使用的身份信息
    protected function guard()
    {
        return Auth::guard('admin');
    }
}

