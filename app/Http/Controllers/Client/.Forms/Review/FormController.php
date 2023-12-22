<?php

namespace App\Http\Controllers\Client\Forms\Review;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

# events
use App\Events\Form\ContactusEvent;

# request
use App\Http\Requests\Client\Form\CallbackFormRequest;

# model
use App\Models\Setting\Item as Setting;
use App\Models\Review\Item as Review;
use App\Models\Specialist\Executor;

# события: уведомление
use App\Events\Notification\IndexEvent as NotificationEvent;

class FormController extends Controller
{
    /**
     * Submit current form
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        # поиск отклика по задаче
        $executor = Executor::where("id", $request->input('executor_id'))->first();
        
        # уведомление
        NotificationEvent::dispatch(["type" => "newreview", "text" => "Пользователь оставил отзыв на задание.", "task_id" =>  $executor->task_id, "specialist_id" => $executor->specialist_id]);

        # нужные для отзыва поля
        $validated = $request->only(["text", "is_recomended", "is_open", "star"]);
       
        # создание и сохранение отзыва 
        $review = new Review($validated);

        # добавляем заголовок к отзыву
        $review->title = "Отзыв по задаче '{$executor->task->title}'";

        # привязка отзыва к исполнителю
        $executor->review()->save($review);

        # пометка, что задача выполнена
        $executor->is_done = 1;
        $executor->save();


        # NewUserEvent::dispatch( $request->validated() );
        
        $response = array(
            'result' => array(
                'status' => 1,
            ),
            'messages' => array(
                "Ваш отзыв принят.",
            ),
        );    
        return $response;
    }
}
