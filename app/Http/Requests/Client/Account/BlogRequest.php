<?php

namespace App\Http\Requests\Client\Account;

use Illuminate\Foundation\Http\FormRequest;

class BlogRequest extends FormRequest
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
            'title' => "required",
            'body_short' => "required",
            'body_long' => "",
            'category_id' => "",
            'account_id' => "",
            'is_visible' => "",
            'is_approve' => "",
            'is_slider' => "",
            'publish_at' => "required",
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
            'title.required' => "Поле 'заголовок' обязательно для заполнения.",
            'category_id.required' => "Выбор категории обязателен.",
            'body_short.required' => "Предисловие обязательно для заполнения.",
            'body_long.required' => "Полное описание обязательно для заполнения.",
            'publish_at.required' => "Дата публикации обязательно, запись будет на сайте не ранее этой даты.",
        ];
    }
}
