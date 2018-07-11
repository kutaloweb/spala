<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfigurationRequest extends FormRequest
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
            'company_name' => 'sometimes|required',
            'config_type' => 'required',
            'lock_screen_timeout' => 'sometimes|required_if:lock_screen,1|integer|min:1|max:120',
            'reset_password_token_lifetime' => 'sometimes|integer|min:5|max:300'
        ];
    }

    /**
     * Translate fields with user friendly name.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'company_name' => trans('configuration.company_name'),
            'lock_screen_timeout' => trans('auth.lock_screen_timeout'),
            'reset_password_token_lifetime' => trans('auth.reset_password_token_lifetime'),
        ];
    }
}
