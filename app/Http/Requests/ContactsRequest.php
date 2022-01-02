<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactsRequest extends FormRequest
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
	
    public function rules()
    {
        return [
            'name' => 'required',
            'contacttype' => 'required',
            'comment' => 'required',
            'email' => 'required',
        ];
    }
	
    public function messages()
    {
        return [
            'required' => trans('app.validate.required'),
        ];
    }
}
