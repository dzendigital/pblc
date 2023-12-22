<?php

namespace App\Http\Requests\Client\Form;

use Illuminate\Foundation\Http\FormRequest;

class ContactusFormRequest extends FormRequest
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
            'name' => "required",
            'phone' => "required",
            'region' => "",
            'budget' => "required",
            'brand' => "",
            'service' => "",
            'comment' => "",
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
            'name.required' => "Поле 'ваше имя' обязательно для заполнения.",
            'phone.required' => "Поле 'телефон' обязательно для заполнения.",
            'budget.required' => "Поле 'бюджет' обязательно для заполнения.",
        ];
    }
}
