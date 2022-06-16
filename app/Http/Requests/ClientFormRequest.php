<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientFormRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required|max:10|min:10'
        ];
    }

    public function messages(){
        return [
            'name.required' => 'Full name is required',
            'email.required' => 'Email is required',
            'phone.required' => 'Mobile number is required'
        ];
    }
}