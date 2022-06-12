<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\{OTPMatchRule, OTPVerifiedRule};

class VerifyOTPFormRequest extends FormRequest
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
            'otp' => ['required', new OTPMatchRule(), new OTPVerifiedRule()]
        ];
    }

    public function messages()
    {
        return [
            'otp.required' => 'OTP is required'
        ];
    }
}
