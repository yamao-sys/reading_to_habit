<?php

namespace App\Http\Requests;

use App\User;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->path() == 'register_user_check')
        {
            return true;
        }
        else
        {
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
            'name' => 'required|regex:/\A[0-9a-zA-Z]{1,20}\z/',
            'email' => [
                        'required',
                        function ($attribute, $value, $fail) {
                            $user = User::where('email', $value)->first();

                            if (!empty($user)) {
                                return $fail('既に登録済みのメールアドレスです。他のメールアドレスでご登録ください。');
                            }
                        },
                       ],
            'password' => 'required|regex:/\A[0-9a-zA-Z]{6,12}\z/',
            'password_to_check' => 'required|same:password',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'ユーザー名は必須項目です。',
            'name.regex' => 'ユーザー名は半角英数字20文字以内でご登録ください。',
            'email.required' => 'メールアドレスは必須項目です。',
            'password.required' => 'パスワードは必須項目です。',
            'password.regex' => 'パスワードは半角英数字6文字以上12文字以内でご登録ください。',
            'password_to_check.required' => 'パスワード確認用は必須項目です。',
            'password_to_check.same' => 'パスワードで入力したものと同じものをご入力ください。',
        ];
    }
}
