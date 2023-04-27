<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ForgetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'             => 'required|email',
            'password'          => 'required|string|max:16|min:6|confirmed',
        ];
    }

    /**
     * Get rule messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.required'           => '请输入邮箱',
            'email.email'              => '邮箱格式不正确',
            'password.required'        => '请输入密码',
            'password.max'             => '密码长度为最大为16位',
            'password.min'             => '密码长度为最小为6位',
            'password.confirmed'       => '两次密码输入不一致',
        ];
    }

    /**
     * @param Validator $validator
     * @throws \App\Exceptions\BobException
     * @author Bob <bob@bobcoder.cc>
     */
    protected function failedValidation(Validator $validator)
    {
        return_bob($validator->errors()->first(), 2, 200);
    }
}
