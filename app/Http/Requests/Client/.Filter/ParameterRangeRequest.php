<?php

namespace App\Http\Requests\Client\Filter;

use Illuminate\Foundation\Http\FormRequest;

class ParameterRangeRequest extends FormRequest
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
            "trunk_from" => "",
            "trunk_to" => "",
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
        ];
    }
}
