<?php

namespace App\Http\Requests\Client\Form;

use Illuminate\Foundation\Http\FormRequest;

class SurveyFormRequest extends FormRequest
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
            'orgname' => "required",
            'orgcomname' => "",
            'orgadress' => "required",
            'orgperson' => "required",
            'inn' => "required",
            'kpp' => "",
            'ogrnip' => "",
            'bankname' => "required",
            'bankbik' => "required",
            'bankcor' => "required",
            'bankcheck' => "required",
            'firstname' => "required",
            'lastname' => "required",
            'secondname' => "",
            'signatoryposition' => "required",
            'signatoryreason' => "required",
            'email' => "required",
            'phone' => "required",
            'product' => "required",
            'adresmfc' => "required",
            'policy' => "accepted"
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
            "orgname.required" => "Название организации обязательно"
        ];
    }
}
