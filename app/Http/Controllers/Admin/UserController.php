<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemAdmin;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Admin\UserAuthRequest;
class UserController extends Controller
{
    public $loginAfterSignUp = true;

    public function getUserInfo(Request $request)
    {

        $user = $this->guard()->user();

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    
    // 重写验证过程中使用的身份信息
    protected function guard()
    {
        return Auth::guard('admin');
    }
}
