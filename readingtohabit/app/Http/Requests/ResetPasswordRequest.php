<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->path() == 'reset_password_do') {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password' => 'required|regex:/\A[0-9a-zA-Z]{6,12}\z/',
            'password_to_check' => 'required|same:password',
        ];
    }

    public function messages()
    {
        return [
            'password.required' => '新しいパスワードは必須項目です。',
            'password.regex' => '新しいパスワードは半角英数字6文字以上12文字以内でご登録ください。',
            'password_to_check.required' => 'パスワード確認用は必須項目です。',
            'password_to_check.same' => '新しいパスワードで入力したものと同じものをご入力ください。',
        ];
    }
}
