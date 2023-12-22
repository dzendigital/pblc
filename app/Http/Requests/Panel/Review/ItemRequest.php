<?php

namespace App\Http\Requests\Panel\Review;

use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
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

            "title" => "",
            
            "rating" => "",
            "text" => "",
            
            "is_open" => "",
            "is_recomended" => "",
            "is_visible" => "",
            "sort" => "",

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
            'name.required' => "Поле 'Имя' обязательно для заполнения.",
            'rating.required' => "Поле 'Рейтинг' обязательно для заполнения.",
        ];
    }
}
