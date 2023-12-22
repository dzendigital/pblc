<?php

namespace App\Http\Requests\Panel;

use Illuminate\Foundation\Http\FormRequest;

class MenuRequest extends FormRequest
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
            'parent_id' => 'required',
            'title' => 'required',
            'slug' => '',
            'is_visible' => '',
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
            'title.required' => "Поле 'Наименование' обязательно для заполнения.",
            'title.unique' => "Поле 'Наименование' должно быть уникальным.",
            'menu_id.required' => "Необходимо указать родительский элемент меню.",
            'slug.unique' => "Поле 'URL' должно быть уникальным.",
        ];
    }

}
