<?php

namespace App\Http\Controllers\Panel\Directions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Models\Transport\Transport;
use App\Models\Directions\Directions;

class DirectionsTransportController extends Controller
{
    /**
     * 
     * В данном классе используется для получения варианта транспорта по id
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        # необходимые переменные
        $transport = null;
        $direction = null;
        $is_transport = null;
        $item = null;
        $messages = null;
        $status = null;

        # данные в запросе
        $transport = $request->input('transport');
        $direction = $request->input('item');

        # проверка, что транспорт можно добавить в направление
        $is_transport = Transport::where('id', $transport)->with(['gallery'])->first();
       
        if ( !is_null($is_transport) ) {
            # проверка: содержит ли текущая запись запрашиваемую привязку, если да - ошибка, доступна только одна привязка
            $item = Directions::where('id', $direction)->whereHas('transport', function ($query) use($transport) {
                $query->where('id', '=', $transport);
            })->first();
            if ( is_null($item) ) {
                # все ок.
                $status = 1;

            }else{
                $status = null;
                $messages[] = "К текущему направлению уже добавлен выбранный вами транспорт.";
                $messages[] = "Пожалуйста, добавьте другой вариант транспорта.";
            }
        }else{
            $status = null;
            $messages[] = "Выбранный вами транспорт не найден.";
            $messages[] = "Пожалуйста, добавьте нужный вам транспорт в соответстующем модуле.";
        }

        $response = array(
            'result' => array(
                'status' => $status,
                'messages' => $messages,
                'transport' => Transport::where('id', $transport)->with(['gallery'])->first(),
            ),
        );      
        return response()->json($response);   
    }
}
