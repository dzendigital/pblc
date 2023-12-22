<?php

namespace App\Http\Controllers\Client\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Specialist\Item as Specialist;
use App\Models\Gallery\Gallery;

use App\Repositories\Client\UserTaskRepository;
use App\Models\Specialist\Executor;

# события: уведомление
use App\Events\Notification\IndexEvent as NotificationEvent;

class TaskController extends Controller
{

    private UserTaskRepository $itemRepository;

    public function __construct(UserTaskRepository $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    /**
     * Display a listing of the resource.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param string $slug
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        # здесь запрашивать только задачи специалиста
        $response = array(
            # "get" => $this->itemRepository->all(),
            "items" => $this->itemRepository->paginate(),
        );  

        $response["template"]["render"] = view( "client/user/task/paginated", $response)->render();

        return view( "client/user/task/index", $response);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $slug)
    {
        $item = $this->itemRepository->find($slug);
        $meta = array(
            'meta_title' => $item['meta_title'],
            'meta_description' => $item['meta_description'],
            'meta_keywords' => $item['meta_keywords'],
            'meta_h1' => $item['meta_h1'],
        );
        $response = array(
            'item' => $item,
            'executorlist' => $this->itemRepository->specialistAcceptList($item->id),
            'executorselected' => $this->itemRepository->specialistAccept($item->id),
            'meta' => $meta,
        );
        # dd(__METHOD__, $response);

        return view('client/user/task/show', $response);
    }

    /**
     * Accept task to login specialist.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function accept($task_slug, $specialist_id)
    {

        $item = $this->itemRepository->find($task_slug);


        $specialist = Specialist::where("id", $specialist_id)->first();


        # если ранее специалист уже откликался, добавлять не будем
        $is_executor_exist = Executor::where("specialist_id", $specialist->id)->where("task_id", $item->id)->first();

        # выбор исполнителя
        $is_executor_exist->is_selected = 1;
        $is_executor_exist->save();

        # уведомление
        NotificationEvent::dispatch(["type" => "executorselected", "text" => "Специалист выбран в качестве исполнителя.", "task_id" =>  $item->id, "specialist_id" => $specialist_id]);
        
        return redirect("/user/task/{$item->slug}"); 
    }

}
