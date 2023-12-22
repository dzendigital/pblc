<?php

namespace App\Http\Controllers\Panel\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\Panel\Review\ItemRequest;
use App\Models\Review\Item;

class PayKeeperController extends Controller
{
 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response = array();
        # $response['items'] = Item::latest()->with($this->with)->get();

        return view("panel/payment/index", $response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        # Логин и пароль от личного кабинета PayKeeper
        $user = "admin";
        $password = "523bb7ad2f59"; 
         
        # Basic-авторизация передаётся как base64
        $base64 = base64_encode("$user:$password");         
        $headers = array(); 
        array_push($headers,'Content-Type: application/x-www-form-urlencoded');
        
        # Подготавливаем заголовок для авторизации
        array_push($headers,'Authorization: Basic '.$base64);

        # Укажите адрес ВАШЕГО сервера PayKeeper, адрес demo.paykeeper.ru - пример!
        $server_paykeeper = "https://yapodbor.server.paykeeper.ru";                  

        # Параметры платежа, сумма - обязательный параметр
        # Остальные параметры можно не задавать
        $payment_data = array (
            "pay_amount" => $request->input("sum"),
            "clientid" => "Яподбор (администратор)",
            "orderid" => "Заказ № " . time(),
            "client_email" => "info@yapodbor.ru",
            "service_name" => $request->input("paytitle"),
            "client_phone" => "8 (800) 201-02-88"
        );

        # Готовим первый запрос на получение токена безопасности
        $uri = "/info/settings/token/";
        
        # Для сетевых запросов в этом примере используется cURL
        $curl = curl_init(); 
         
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $server_paykeeper.$uri);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_HEADER, false);
         
        # Инициируем запрос к API
        $response = curl_exec($curl);                       
        $php_array = json_decode($response, true);
         
        # В ответе должно быть заполнено поле token, иначе - ошибка
        $token = (isset($php_array['token']) && !empty($php_array['token'])) ? $php_array['token'] : null;
        if ( is_null($token) ) 
        {    
            return array(
                'result' => array(
                    'status' => null,
                    'message' => array(
                        "Ошибка оплаты: пустой токен.",
                        "Обратитесь к администратору сайта."
                    )
                ),
            );
        }

        # Готовим запрос 3.4 JSON API на получение счёта
        $uri = "/change/invoice/preview/";
         
        # Формируем список POST параметров
        $request = http_build_query(array_merge($payment_data, array ('token' => $token)));
                        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $server_paykeeper.$uri);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
         
        $response = json_decode(curl_exec($curl), 1);

        # В ответе должно быть поле invoice_id, иначе - ошибка
        $invoice_id = isset($response['invoice_id']) ? isset($response['invoice_id']) : null;
        if (  is_null($invoice_id) ) 
        {
            return array(
                'result' => array(
                    'status' => null,
                    'message' => array(
                        "Ошибка оплаты: получен пустой invoice id от PayKeeper.",
                        "Обратитесь к администратору сайта."
                    )
                ),
            );
        }
         
        # В этой переменной прямая ссылка на оплату с заданными параметрами
        # $link = "https://$server_paykeeper/bill/$invoice_id/";
        # $link = "$server_paykeeper/bill/$invoice_id/";
        $link = $response['invoice_url'];
       
        return array(
            'result' => array(
                "status" => 1,
                "message" => array(
                    "Успешная оплата: ожидайте направления на PayKeeper для проведения оплаты.",
                ),
                "link" => $link,
            ),
        );
    }
}
