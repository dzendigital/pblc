<?php
namespace App\Http\Controllers\Client\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Repositories\TaskRepository;
use App\Models\Baseauto\Item as Item;

class CatalogTaskController extends Controller
{

    private TaskRepository $itemRepository;

    public function __construct(TaskRepository $itemRepository)
    {
        $this->itemRepository = $itemRepository;
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
        if ( $request->filled('search') ) {
            $where_task[] = array("title", "LIKE", "%{$request->input('search')}%"); 
        }
        if ( $request->filled('price') ) {
            $where_task[] = array("price", ">=", $request->input('price')); 
        }
        $where_specialist = array(); 
        if ( $request->filled('region') ) {
            $where_specialist[] = array("region", "=", $request->input('region')); 
        }
        if ( $request->filled('city') ) {
            $where_specialist[] = array("city", "=", $request->input('city')); 
        }


        $response = array(
        );  
        
        $items = array("items" => $this->itemRepository->paginateWhere($where_task, $where_specialist));

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
            'item' => array(
                "raw" => $item,
            ),
            'meta' => $meta,
        );
        return view('client/task/catalog/show', $response);
    }

}
