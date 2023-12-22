<?php

namespace App\Http\Requests\Panel\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class MoviesRequest extends FormRequest
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
            'title' => 'required',
            'subtitle' => 'required',

            'is_visible' => '',
            'prebody' => '',
            'body' => '',

            'is_active_gallery' => '',
            'catalog_gallery_id' => '',

            'is_active_video' => '',
            'catalog_video_id' => '',

            'sort' => '',
            
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [

            'title.required' => "Поле 'Заголовок' обязательно для заполнения.",
            'subtitle.required' => "Поле 'Подзаголовок' обязательно для заполнения.",

        ];
    }
}
