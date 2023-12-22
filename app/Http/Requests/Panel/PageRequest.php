<?php

namespace App\Http\Requests\Panel;

use Illuminate\Foundation\Http\FormRequest;

class PageRequest extends FormRequest
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
            'menu_id' => 'required',
            'is_visible' => '',
            'body' => 'required',
            'meta_title' => '',
            'meta_description' => '',
            'meta_keywords' => '',
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
            'menu_id.required' => "Необходимо указать элемент меню.",
            'body.required' => "Поле 'Содержание страницы' обязательно для заполнения.",
            'meta_title.required' => "Поле 'meta-title' обязательно для заполнения.",
            'meta_description.required' => "Поле 'meta-description' обязательно для заполнения.",
            'meta_keywords.required' => "Поле 'meta-keywords' обязательно для заполнения.",
        ];
    }

}
