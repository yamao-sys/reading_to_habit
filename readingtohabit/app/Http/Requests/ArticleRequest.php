<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->is('add_article_do') || $this->is('edit_article_do/*')) {
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
            'learning' => 'required|max:21845',
            'action'   => 'required|max:21845',
        ];
    }

    public function messages() {
        return [
            'learning.required' => '学んだことは必須項目です。',
            'learning.max'      => '学んだことは21845文字以内でご入力ください。',
            'action.required'   => '学びをどのように行動に活かすかは必須項目です。',
            'action.max'        => '学びをどのように行動に活かすかは21845文字以内でご入力ください。',
        ];
    }
}
