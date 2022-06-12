<?php

namespace App\Rules;

use App\Models\Otp;
use Illuminate\Contracts\Validation\Rule;

class OTPMatchRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $otp = Otp::where('code', $value)->first();
        return $otp !== null;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'OTP does not match';
    }
}
