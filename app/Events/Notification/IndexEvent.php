<?php

namespace App\Events\Notification;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\Notification\Item as NotificationModel;
use App\Models\Specialist\Item as Specialist;
use App\Models\User\Item as User;

class IndexEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        # тип уведомления:
        # executorselected - Вас выбрали исполнителем
        # newexecutor - 1 новый отклик на задание
        # newreview  - Исполнитель оставил отзыв на задание
        $notification = new NotificationModel($data);
        $notification->save();

        if ( isset($data['user_id']) ) {
            $account = User::where("id", $data['user_id'])->first();
            $account->notification()->save($notification);
        }
        if ( isset($data['specialist_id']) ) {
            $account = Specialist::where("id", $data['specialist_id'])->first();
            $account->notification()->save($notification);

        }
    }

}
