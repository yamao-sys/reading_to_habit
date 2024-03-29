<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->is('contact_check')) {
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
            'email'     => 'required',
            'contact' => [
                        'required',
                        function ($attribute, $value, $fail) {
                            if (mb_strlen($value) > 3000) {
                                return $fail('お問い合わせ内容は3000文字以内でご入力ください。');
                            }
                        },
                       ],
        ];
    }

    public function messages() {
        return [
            'email.required'     => 'メールアドレスは必須項目です。',
            'contact.required'   => 'お問い合わせ内容は必須項目です。',
        ];
    }
}
