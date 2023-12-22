<?php

namespace App\Http\Requests\Client\Specialist;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'phone' => "",
            'email' => "",
            'region' => "",
            'experience' => "",
            'is_email' => "",
            'is_sms' => "",
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
            'title.required' => "Поле 'имя' обязательно для заполнения.",
            'email.required' => "Поле 'почта' обязательно для заполнения.",
            'phone.required' => "Поле 'телефон' обязательно для заполнения.",
        ];
    }
}
