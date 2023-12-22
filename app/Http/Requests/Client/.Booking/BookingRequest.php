<?php

namespace App\Http\Requests\Client\Booking;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
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
            "name" => "required",
            "number" => "required",
            "email" => "required",
            "passenger" => "required",
            "date" => "",
            "child-seat" => "",
            "oneway" => "",
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
            'name.required' => "The 'Name' field is required.",
            'email.required' => "The 'Email' field is required.",
            'number.required' => "The 'Number' field is required.",
            'passenger.required' => "The 'Passenger' field is required.",
        ];
    }
}
