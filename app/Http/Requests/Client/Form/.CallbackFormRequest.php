<?php

namespace App\Http\Requests\Client\Form;

use Illuminate\Foundation\Http\FormRequest;

class CallbackFormRequest extends FormRequest
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
            'phone' => "required|min:16",
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
            'phone.required' => 'Укажите ваш телефон для связи.',
            'phone.min' => 'Укажите телефон в верном формате.',
        ];
    }
}
