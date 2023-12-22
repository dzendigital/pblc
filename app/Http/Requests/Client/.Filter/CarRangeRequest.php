<?php 

namespace App\Http\Requests\Client\Filter;

use Illuminate\Foundation\Http\FormRequest;

class CarRangeRequest extends FormRequest
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
            "price_from" => "",
            "price_to" => "",
            "mileage_from" => "",
            "mileage_to" => "",
            "year_from" => "",
            "year_to" => "",
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
