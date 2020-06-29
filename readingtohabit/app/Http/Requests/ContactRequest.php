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
        if ($this->path() == 'contact_check') {
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
            'contact'   => 'required|max:21845',
        ];
    }

    public function messages()
    {
        return [
            'email.required'     => '$B%a!<%k%"%I%l%9$OI,?\9`L\$G$9!#(B',
            'contact.required'   => '$B$*Ld$$9g$o$;FbMF$OI,?\9`L\$G$9!#(B',
            'contact.max'        => '$B$*Ld$$9g$o$;FbMF$O(B21845$BJ8;z0JFb$G$4F~NO$/$@$5$$!#(B',
        ];
    }
}
