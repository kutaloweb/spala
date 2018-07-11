<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
        $rules = [
            'current_password' => 'sometimes|required',
            'new_password' => 'required|confirmed|min:6',
            'new_password_confirmation' => 'required|same:new_password'
        ];

        if ($this->path() === 'api/change-password') {
            $rules['new_password'] .= '|different:current_password';
        }

        return $rules;
    }

    /**
     * Translate fields with user friendly name.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'current_password' => trans('auth.current_password'),
            'new_password' => trans('auth.new_password'),
            'new_password_confirmation' => trans('auth.new_password_confirmation'),
        ];
    }
}
