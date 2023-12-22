<?php

namespace App\Http\Requests\Client\Filter;

use Illuminate\Foundation\Http\FormRequest;

class ParameterCheckboxRequest extends FormRequest
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
            "is_pressure_sensor" => "",
            "is_abs" => "",
            "is_esp" => "",
            "is_armor" => "",
            "is_glonass" => "",
            "is_doorlocks" => "",
            "is_fog_lights" => "",
            "is_adaptive_lights" => "",
            "is_high_beam" => "",
            "is_rain_sensor" => "",
            "is_headlight_control" => "",
            "is_light_sensor" => "",
            "is_headlight_washer" => "",
            "is_crankcase" => "",
            "is_towbar" => "",
            "is_program_preheater" => "",
            "is_gearshift_paddles" => "",
            "is_power_mirrors" => "",
            "is_multifunctional_steering" => "",
            "is_trunk_open" => "",
            "is_projection_display" => "",
            "is_driving_selection_system" => "",
            "is_remote_engine_start" => "",
            "is_on_board_computer" => "",
            "is_electro_dashboard" => "",
            "is_keyless_entry_system" => "",
            "is_projection_display" => "",
            "is_power_trunk_lid" => "",
            "is_engine_button_start" => "",
            "is_electro_folding_mirror" => "",
            "is_pedal_assembly" => "",
            "is_start_stop_system" => "",
            "is_body_kit" => "",
            "is_roof_rails" => "",
            "is_airbush" => "",
            "is_immobilizer" => "",
            "is_center_lock" => "",
            "is_interior_sensor" => "",
            "is_fold_backseat" => "",
            "is_sport_frontseat" => "",
            "is_luke" => "",
            "is_tinted" => "",
            "is_leather_wheel" => "",
            "is_leather_gearstick" => "",
            "is_third_row" => "",
            "is_armrest" => "",
            "is_panoramic_roof" => "",
            "is_heat_wheel" => "",
            "is_usb" => "",
            "is_navigator" => "",
            "is_voice_control" => "",
            "is_carplay" => "",
            "is_yandex_auto" => "",
            "is_aux" => "",
            "is_12v" => "",
            "is_220v" => "",
            "is_lcd" => "",
            "is_android_auto" => "",
            "is_bluetooth" => "",
            "is_lcd_rear" => "",
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
