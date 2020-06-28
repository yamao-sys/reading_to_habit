<?php

namespace App\Http\Requests;

use App\User;

use Illuminate\Foundation\Http\FormRequest;

class EditProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->path() === 'edit_profile') {
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
            'profile_img' => 'file|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name'  => 'required|regex:/\A[0-9a-zA-Z]{1,20}\z/',
            'email' => [
                        'required',
                        function ($attribute, $value, $fail) {
                            $user = User::where('id', '!=', session()->get('user_id'))
                                        ->where('email', $value)
                                        ->first();

                            if (!empty($user)) {
                                return $fail('既に登録済みのメールアドレスです。他のメールアドレスでご登録ください。');
                            }
                        },
                       ],
        ];
    }

    public function messages()
    {
        return [
            'profile_img.file' => 'ファイルをご選択ください。',
            'profile_img.image' => '画像ファイルをご選択ください。',
            'profile_img.mimes' => 'JPEG,PNG,JPG,GIFの画像をご選択ください。',
            'profile_img.max' => '2MB以内のサイズの画像をご選択ください。',
            'name.required' => 'ユーザー名は必須項目です。',
            'name.regex' => 'ユーザー名は半角英数字20文字以内でご登録ください。',
            'email.required' => 'メールアドレスは必須項目です。',
        ];
    }
}
