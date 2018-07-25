<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
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
            'title' => 'sometimes|required',
            'body' => 'sometimes|required',
            'category_id' => 'sometimes|required',
            'file' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
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
            'title' => trans('post.title'),
            'body' => trans('post.body'),
            'category_id' => trans('category.category'),
            'file' => trans('general.file')
        ];
    }
}
