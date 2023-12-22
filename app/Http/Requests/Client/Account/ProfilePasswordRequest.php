<?php

namespace App\Http\Requests\Client\Account;

use Illuminate\Foundation\Http\FormRequest;

class ProfilePasswordRequest extends FormRequest
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
            'password' => "required|min:6",
            'password_repeat' => "required|same:password",
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
            'password.min' => "Пароль должен содержать не менее 6 символов.",
            'password_repeat.same' => "Пароли не совпадают.",
            'password.required' => "Поле 'Пароль' обязательно для заполнения.",
            'password_repeat.required' => "Поле 'Повторите пароль' обязательно для заполнения.",
        ];
    }
}
