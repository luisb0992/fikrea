<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LanguagesRequest extends FormRequest
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
	
    public function messages()
    {
        return [
            'required' => trans('app.validate.required'),
            'min' => str_replace('{0}', ':min', trans('app.validate.minlength')),
            'max' => str_replace('{0}', ':max', trans('app.validate.maxlength')),
            'unique' => trans('app.validate.unique'),
        ];
    }
}

class LanguagesCreateRequest extends LanguagesRequest
{
    public function rules()
    {
        return [
            'name' => 'required',
            'code' => 'required|min:2|max:5|unique:languages',
            'flagpath' => 'required',
        ];
    }
}

class LanguagesUpdateRequest extends LanguagesRequest
{
    public function rules()
    {
        return [
            'name' => 'required',
            'code' => 'required|min:2|max:5',
            'flagpath' => 'required',
        ];
    }
}
