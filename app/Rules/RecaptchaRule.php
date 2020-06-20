<?php

namespace App\Rules;

use GuzzleHttp\Client;
use Illuminate\Contracts\Validation\Rule;

class RecaptchaRule implements Rule
{

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $client = new Client();

        $response = $client->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'form_params' =>
                    [
                        'secret'   => env('GOOGLE_RECAPTCHA_SECRET'),
                        'response' => $value
                    ]
            ]
        );

        return json_decode((string)$response->getBody())->success;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
       return 'Please ensure that you are a human';
    }
}