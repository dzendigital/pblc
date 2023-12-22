<?php

namespace App\Listeners\Form;

use App\Events\Form\CalculatorEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\Mail;
use App\Mail\Form\CalculatorForm;

class BitrixListener
{

    private $url = 'https://b24-ee5jr1.bitrix24.ru/rest/1/h0lvxglutjbjyn07/crm.lead.add.json';
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  $event
     * @return void
     */
    public function handle($event)
    {
        # bitrix 24
        $data = $event->data;
        // dd(__METHOD__, $data);
        switch ($data['method']) {
            case 'calculator':
                $birtix_data = $this->calculator($data);
            break;
            case 'contactus':
                $birtix_data = $this->contactus($data);
            break;
            case 'callback':
                $birtix_data = $this->callback($data);
            break;
            
            default:
                $birtix_data = null;
                break;
        }

        if ( is_null($birtix_data) ) 
        {
            return;
        }
        
        # запрос
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $this->url,
            CURLOPT_POSTFIELDS => $birtix_data,
        ));

        $result = curl_exec($curl);
    }

    private function calculator($data)
    {
        $birtix_data = http_build_query(
            array('fields' => array(
                "TITLE" => "Заявка с формы 'Выберите автомобиль' (калькулятор)", # Заголовок лида
                "NAME" => $data['name'], # Имя
                "PHONE" => array(
                    "VALUE" => $data['phone'],
                    "VALUE_TYPE" => "WORK",
                ),
                "EMAIL" => array(
                    "VALUE" => "(поле почта отсутствует в форме)",
                    "VALUE_TYPE" => "WORK",
                ),
                "COMMENTS" => "Заявка с формы 'Выберите автомобиль, который вы хотели бы приобрести' (калькулятор)",
                "SOURCE_DESCRIPTION" => "
                    Заполненные поля.
                    Марка: {$data['brand']};
                    Модель: {$data['model']};
                    Поколение: {$data['generation']};
                    Год: {$data['year']};
                    Бюджет: {$data['budget']};

                ",
                # "SOURCE_ID" => "",
            ))
        );
        return $birtix_data;
    }
    private function contactus($data)
    {
        $birtix_data = http_build_query(
            array('fields' => array(
                "TITLE" => "Заявка с формы 'Свяжитесь с нами'", # Заголовок лида
                "NAME" => $data['name'], # Имя
                "PHONE" => array(
                    "VALUE" => $data['phone'],
                    "VALUE_TYPE" => "WORK",
                ),
                "EMAIL" => array(
                    "VALUE" => "(поле почта отсутствует в форме)",
                    "VALUE_TYPE" => "WORK",
                ),
                "COMMENTS" => "Заявка с формы 'Свяжитесь с нами'",
                "SOURCE_DESCRIPTION" => "
                    Заполненные поля.
                    Регион: {$data['region']};
                    Бюджет на покупку: {$data['budget']};
                    Какие модели и бренды рассматривают: {$data['brand']};
                    Услуга: {$data['service']};
                ",
                # "SOURCE_ID" => "",
            ))
        );
        return $birtix_data;
    }
    private function callback($data)
    {
        $birtix_data = http_build_query(
            array('fields' => array(
                "TITLE" => "Заявка с формы 'Перезвоните мне'", # Заголовок лида
                "NAME" => "(поле имя отсутствует в форме)", # Имя
                "PHONE" => array(
                    "VALUE" => $data['phone'],
                    "VALUE_TYPE" => "WORK",
                ),
                "EMAIL" => array(
                    "VALUE" => "(поле почта отсутствует в форме)",
                    "VALUE_TYPE" => "WORK",
                ),
                "COMMENTS" => "Заявка с формы 'Перезвоните мне'",
                "SOURCE_DESCRIPTION" => "
                    Заполненные поля.
                    Телефон: {$data['phone']}
                ",
                # "SOURCE_ID" => "",
            ))
        );
        return $birtix_data;
    }
}
