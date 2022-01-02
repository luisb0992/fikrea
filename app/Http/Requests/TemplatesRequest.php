<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TemplatesRequest extends FormRequest
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
            'templatetype' => 'required',
            'comment' => 'required',
        ];
    }
	
    public function messages()
    {
        return [
            'required' => trans('app.validate.required'),
        ];
    }
}
