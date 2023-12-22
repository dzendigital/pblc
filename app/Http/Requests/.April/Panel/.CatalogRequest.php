<?php

namespace App\Http\Requests\Panel;

use Illuminate\Foundation\Http\FormRequest;

class CatalogRequest extends FormRequest
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
            'title' => 'required',
            'area' => '',
            'price' => 'required',
            'location' => 'required',
            'adress' => 'required',
            'adress_coords' => '',
            'complete_date' => 'required',
            'body' => 'required',
            'video_src' => '',
            'crm_id' => 'required',
            'flat_layout' => '',
            'category_id' => 'required',
            'is_active_characteristics' => "",
            "is_active_lots" => "",
            "is_active_flat" => "",
            "is_active_gallery" => "",
            "is_visible" => "",
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
            'title.required' => "Поле 'Наименование' обязательно для заполнения.",
            'price.required' => "Поле 'Цена' обязательно для заполнения.",
            'location.required' => "Поле 'Район объекта' обязательно для заполнения.",
            'adress.required' => "Поле 'Адрес' обязательно для заполнения.",
            'complete_date.required' => "Поле 'Срок сдачи объекта' обязательно для заполнения.",
            'body.required' => "Поле 'Описание объекта' обязательно для заполнения.",
            'video_src.required' => "Поле 'Видео объекта' обязательно для заполнения.",
            'crm_id.required' => "Поле 'id 1С-Битрикс' обязательно для заполнения.",
            'category_id.required' => "Поле 'Категория' обязательно для заполнения.",
        ];
    }
}
