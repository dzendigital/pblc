<?php

namespace App\Http\Requests\Panel;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'phone' => 'required|unique:users',
            'email' => 'required|unique:users',
            'password' => 'required',
            'role' => 'required',
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
            'phone.required' => "Поле 'phone' обязательно для заполнения.",
            'phone.unique' => "Поле 'phone' должно быть уникальным.",
            'email.required' => "Поле 'email' обязательно для заполнения.",
            'email.unique' => "Поле 'email' должно быть уникальным.",
            'password.required' => "Поле 'пароль' обязательно для заполнения.",
            'role.required' => "Роль пользователя обязательна для заполнения.",
        ];
    }

}
