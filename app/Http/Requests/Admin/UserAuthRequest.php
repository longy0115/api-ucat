<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserAuthRequest extends FormRequest
{
    /**
     * 确定是否授权用户发出此请求
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     *获取应用于请求的验证规则
     *
     * @return array
     */
    public function rules()
    {
        return [
            'real_name' => 'required|string',
            'account' => 'required',
            'pwd' => 'required|string|min:6|max:10'
        ];
    }
}
