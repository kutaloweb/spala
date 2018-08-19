<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserProfileRequest extends FormRequest
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
            'first_name' => 'sometimes|required',
            'last_name' => 'sometimes|required',
            'date_of_birth' => 'date_format:Y-m-d|nullable',
            'role_id' => 'sometimes|required',
            'country_id' => 'sometimes|required',
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
            'first_name' => trans('user.first_name'),
            'last_name' => trans('user.last_name'),
            'date_of_birth' => trans('user.date_of_birth'),
            'role_id' => trans('role.role'),
            'country_id' => trans('user.country'),
        ];
    }
}
