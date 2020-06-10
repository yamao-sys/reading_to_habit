<?php

namespace App\Http\Requests;

use App\User;

use Illuminate\Foundation\Http\FormRequest;

class EditPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->path() === 'edit_password') {
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
            'current_password' => [
                'required',
                function ($attribute, $value, $fail) {
                    $user = User::where('id', session()->get('user_id'))->first();

                    if (!password_verify($value, $user['password'])) {
                        return $fail('現在のパスワードが間違っています。');
                    }
                },
            ],
            'new_password' => 'required|regex:/\A[0-9a-zA-Z]{6,12}\z/',
            'new_password_to_check' => 'required|same:new_password',
        ];
    }

    public function messages()
    {
        return [
            'current_password.required' => '現在のパスワードは必須項目です。',
            'new_password.required' => '新しいパスワードは必須項目です。',
            'new_password.regex' => '新しいパスワードは半角英数字6文字以上12文字以内でご登録ください。',
            'new_password_to_check.required' => '新しいパスワード確認用は必須項目です。',
            'new_password_to_check.same' => '新しいパスワードで入力したものと同じものをご入力ください。',
        ];
    }
}
