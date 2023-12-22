<?php
namespace App\Http\Controllers\Client\Specialist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Repositories\TaskRepository;
use App\Repositories\Client\SpecialistTaskRepository;

use App\Models\Baseauto\Item as Item;
use App\Models\Specialist\Item as Specialist;
use App\Models\Specialist\Executor;

# события: уведомление
use App\Events\Notification\IndexEvent as NotificationEvent;


class CatalogTaskController extends Controller
{

    private TaskRepository $itemRepository;
    private SpecialistTaskRepository $specialistTaskRepository;

    public function __construct(TaskRepository $itemRepository, SpecialistTaskRepository $specialistTaskRepository)
    {
        $this->itemRepository = $itemRepository;
        $this->specialistTaskRepository = $specialistTaskRepository;
    }
    /**
     * Display a listing of the resource.
     * @param string $slug
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ( !is_null($request->input('search')) ) {
            $where = array(); 
            $where[] = array("title", "LIKE", "%{$request->input('search')}%"); 
            $items = $this->itemRepository->paginateWhere($where);
        }else{
            $items = $this->itemRepository->paginate();
        }
        $response = array(
            "items" => $items,
        );  


        $response["template"]["render"] = view( "client/task/catalog/paginated", $response)->render();

        return view( "client/task/catalog/index", $response );
    }

    /**
     * Return result of filter /task.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function filter(Request $request)
    {
        $where_task = array(); 
        $where_raw = array(); 
        if ( $request->filled('search') ) {
            $where_task[] = array("title", "LIKE", "%{$request->input('search')}%"); 
        }
        if ( $request->filled('price') ) {
            $where_task[] = array("service_price_from", "<=", $request->input('price')); 
            $where_task[] = array("service_price_to", ">=", $request->input('price')); 
            # SELECT * FROM `task` WHERE `service_price_from` <= 6000 AND `service_price_to` >= 6000
            # $where_raw[] = "service_price_from >= " . $request->input('price') . " OR service_prive_to <= " . $request->input('price'); 
        }
        $where_user = array(); 
        if ( $request->filled('region') ) {
            $where_user[] = array("region", "=", $request->input('region')); 
        }
        // if ( $request->filled('city') ) {
        //     $where_specialist[] = array("city", "=", $request->input('city')); 
        // }


        $response = array(
        );  
        
        $items = array("items" => $this->itemRepository->paginateWhere($where_task, $where_user));

        $response["template"]["render"] = view( "client/task/catalog/paginated", $items)->render();

        return $response;
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
            'executorselected' => $this->specialistTaskRepository->specialistSelected($item->id),
            'meta' => $meta,
        );

        return view('client/task/catalog/show', $response);
    }
    /**
     * Accept task to login specialist.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function accept(Request $request)
    {
        $is_valid = $request->validate([
            'slug' => 'required',
            'text' => 'required',
        ]);
        $item = $this->itemRepository->find($request->input("slug"));


        $specialist = Specialist::where("user_id", auth()->user()->id)->first();

        # если ранее специалист уже откликался, добавлять не будем
        $is_executor_exist = Executor::where("specialist_id", $specialist->id)->where("task_id", $item->id)->get();

        if ( count($is_executor_exist) != 0 ) {
            return redirect("/specialist/task/{$item->slug}"); 
        }
        

        $model = array(
            "user_id" => $item->user_id,
            "specialist_id" => $specialist->id,
            "task_id" => $item->id,
            "price" => str_replace(' ', '', $request->input("price")),
            "text" => $request->input("text"),
            "is_selected" => null,
        );

        $executor = new Executor($model);
        $is_saved = $executor->save();

        # уведомление
        NotificationEvent::dispatch(["type" => "newexecutor", "text" => $request->input('text'), "task_id" =>  $item->id, "user_id" => $item->user_id]);
        
        return redirect("/specialist/task/{$item->slug}"); 
    }
    /**
     * Reject task to login specialist.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function reject($slug)
    {
        $item = $this->itemRepository->find($slug);
        $specialist = Specialist::where("user_id", auth()->user()->id)->first();
        $executor = $this->specialistTaskRepository->specialistAccept($item->id, $specialist->id);

        $executor->delete();

        return redirect("/specialist/task"); 
    }

}
