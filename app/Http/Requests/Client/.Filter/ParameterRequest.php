<?php

namespace App\Http\Requests\Client\Filter;

use Illuminate\Foundation\Http\FormRequest;

class ParameterRequest extends FormRequest
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
            "color" => "",
            "generation" => "",
            "body_style" => "",
            "pts_owners" => "",
            "transmission" => "",
            "fuel_type" => "",
            "wheel_drive" => "",
            "airbags" => "",
            "support_systems" => "",
            "isofix" => "",
            "headlight" => "",
            "heating" => "",
            "pendant" => "",
            "conditioning" => "",
            "camera" => "",
            "power_windows" => "",
            "power_steering" => "",
            "steering_wheel" => "",
            "parking_assist" => "",
            "cruise" => "",
            "disk_type" => "",
            "disk_size" => "",
            "alarm" => "",
            "inter_color" => "",
            "power_seats" => "",
            "seat_vent" => "",
            "interior_material" => "",
            "seat_memory" => "",
            "seat_number" => "",
            "seat_height" => "",
            "seat_heating" => "",
            "audio_system" => "",
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
