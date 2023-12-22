<?php

namespace App\Http\Controllers\Client\Feedback;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Client\Feedback\FeedbackRequest;

use App\Models\Feedback\Feedback;
use App\Events\Feedback\NewFeedbackEvent;

class FeedbackController extends Controller
{
    public function index()
    {
        return view("/client/feedback/index");
    }

    /**
     * 
     * Get feedback form request
     * @param  App\Http\Requests\Client\Feedback\FeedbackRequest  $request
     * @return \Illuminate\Http\Response
     * 
     */
    public function store(FeedbackRequest $request)
    {
        # добавляем в базу: создаем бронирование
        $validated_array = $request->validated();
       
        # бронирование: форма
        $response = array(
            'form' => $validated_array,
        );

        # добавление в базу
        $feedback_model = array(
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'body' => $request->input('message'),
            'is_visible' => 1,
        );

        # создадим объект 
        $item = new Feedback($feedback_model);
    
        # сохраним
        $item->save();

        # отправка письма
        NewFeedbackEvent::dispatch($response);

        return view("/client/feedback/success");
    }

}
