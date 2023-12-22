<?php

namespace App\Http\Requests\Panel\Docs;

use Illuminate\Foundation\Http\FormRequest;

class ServeyRequest extends FormRequest
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
            'orgcomname' => "required",
            'orgadress' => "required",
            'orgperson' => "required",
            'inn' => "required",
            'kpp' => "",
            'ogrnip' => "required",
            'bankname' => "required",
            'bankbik' => "required",
            'bankcor' => "required",
            'bankcheck' => "required",
            'firstname' => "required",
            'lastname' => "required",
            'secondname' => "",
            'firstname_genetive' => "",
            'lastname_genetive' => "",
            'secondname_genetive' => "",
            'signatoryposition' => "required",
            'signatoryreason' => "required",
            'email' => "required",
            'phone' => "required",
            'product' => "required",
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
            "orgname.required" => "Поле 'Юридическое название организации' обязательно",
            "orgcomname.required" => "Поле 'Коммерческое название организации' обязательно",
            "orgadress.required" => "Поле 'Адрес' обязательно",
            "orgperson.required" => "Поле 'Контактное лицо' обязательно",
            "inn.required" => "Поле 'ИНН' обязательно",
            "kpp.required" => "Поле 'КПП' обязательно",
            "ogrnip.required" => "Поле 'ОГРНИП' обязательно",
            "bankname.required" => "Поле 'Название банка' обязательно",
            "bankbik.required" => "Поле 'БИК банка' обязательно",
            "bankcor.required" => "Поле 'К./счет' обязательно",
            "bankcheck.required" => "Поле 'Р./счет' обязательно",
            "firstname.required" => "Поле 'Имя' обязательно",
            "lastname.required" => "Поле 'Фамилия' обязательно",
            "secondname.required" => "Поле 'Отчество' обязательно",
            "signatoryposition.required" => "Поле 'Должность подписанта' обязательно",
            "signatoryreason.required" => "Поле 'Основание' обязательно",
            "email.required" => "Поле 'Электронный адрес контактного лица' обязательно",
            "phone.required" => "Поле 'Телефон контактного лица' обязательно",
            "product.required" => "Поле 'Объект рекламирования' обязательно"
            
        ];
    }
}
