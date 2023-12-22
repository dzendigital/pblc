<?php

namespace App\Http\Requests\Client\Baseauto;

use Illuminate\Foundation\Http\FormRequest;

class FilterCarRequest extends FormRequest
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
            "brand" => "",
            "model" => "",
            "generation" => "",
            "body_style" => "",
            "pts_owners" => "",
            "transmission" => "",
            "fuel_type" => "",
            "wheel_drive" => "",
            "price_from" => "",
            "price_to" => "",
            "mileage_from" => "",
            "mileage_to" => "",
            "year_from" => "",
            "year_to" => "",
            "engine_size_from" => "",
            "engine_size_to" => "",
            "ride_height_from" => "",
            "ride_height_to" => "",
            "horse_power_from" => "",
            "horse_power_to" => "",
            "acceleration_from" => "",
            "acceleration_to" => "",
            "consumption_from" => "",
            "consumption_to" => "",
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
            'phone.min' => "Укажите телефон в верном формате.",
        ];
    }
}
