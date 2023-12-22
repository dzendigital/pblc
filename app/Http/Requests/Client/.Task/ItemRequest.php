<?php

namespace App\Http\Requests\Client\Task;

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
            'title' => "required",
            'brand' => "required",
            'model' => "required",
            'year_from' => "required",
            'year_to' => "",
            'fuel_type' => "required",
            'transmission' => "required",
            'budget' => "required",
            'wishes' => "",
            'taskdate_from' => "",
            'taskdate_to' => "",
            'gallery' => "",
            'service_price_from' => "",
            'service_price_to' => "",
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
            'title.required' => "Поле 'Название задачи' обязательно для заполнения.",
            'brand.required' => "Поле 'Марка автомобиля' обязательно для заполнения.",
            'model.required' => "Поле 'Бренд автомобиля' обязательно для заполнения.",
            'year_from.required' => "Поле 'Год выпуска' обязательно для заполнения.",
            'fuel_type.required' => "Поле 'Тип двигателя' обязательно для заполнения.",
            'transmission.required' => "Поле 'Тип коробки передач' обязательно для заполнения.",
            'budget.required' => "Поле 'Бюджет' обязательно для заполнения.",
        ];
    }
}
