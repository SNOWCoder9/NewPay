<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class PasswordRequest extends FormRequest
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
            'old_password'          => 'required',
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
            'old_password.required'    => '请输入旧密码',
            'password.required'        => '请输入新密码',
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
        return_bob($validator->errors()->first());
    }
}
