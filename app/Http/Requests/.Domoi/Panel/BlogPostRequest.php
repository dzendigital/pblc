<?php

namespace App\Http\Requests\Panel;

use Illuminate\Foundation\Http\FormRequest;

class BlogPostRequest extends FormRequest
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
            'body' => 'required',
            'value' => 'required',
            'visible_at'=>'required',
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
            'body.required' => "Поле 'Краткое описание' обязательно для заполнения.",
            'value.required' => "Поле 'Полное описание' обязательно для заполнения.",
            'visible_at.required' => "Поле 'Дата показа' обязательно для заполнения.",
        ];
    }
}
