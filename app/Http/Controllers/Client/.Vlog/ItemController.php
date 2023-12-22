<?php

namespace App\Http\Controllers\Client\Vlog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Repositories\VlogRepository as ItemRepository;
use App\Models\Blog\Item as Item;

class ItemController extends Controller
{
    private ItemRepository $itemRepository;

    public function __construct(ItemRepository $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }
    /**
     * 
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $items = Item::with(["parameter", "account"]);
        $items = $this->itemRepository->all();
        $response = array(
            "items" => array(
                "category" => $this->itemRepository->category(),
                "raw" => $items["raw"],
                "get" => $items["get"],
                "paginate" => $items["raw"]->paginate(6),
            )
        );
        return view( "client/vlog/index", $response );
    }

    /**
     * Возвращает разметку списка статей в зависимости от категории
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        if ( isset($request->field) && isset($request->value) ) {
            $response = $this->sort($request);
        }else{
            $response = $this->category($request);
        }

        return $response;
    }
    /**
     * Возвращает сортированную разметку списка статей
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function category(Request $request)
    {   
        # валидация входящих полей
        $slug = $request->category;

        # запрос получения блогов категории 
        if ( !empty($slug) ) {
            $item = $this->itemRepository->findCategoryItems($slug);
        }else{
            $item = $this->itemRepository->all();
            $item = $item['raw'];
        }

        # формирование url
        $url = !empty($slug) ? "/vlog?category={$slug}" : "/vlog/";

        # формирование разметки
        $response = array(
            "items" => array(
                "paginate" => $item->orderBy("created_at", "DESC")->paginate(6),
            ),
            "template" => array(
                "items" => "",
                "subfilter" => "",
            ),
            "url" => $url,
            "category" => $slug,
        );
        $response["template"]["items"] = view( "components/client/vlog/item-component", $response)->render();
        return $response;
    }
    /**
     * Возвращает сортированную разметку списка статей
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sort(Request $request)
    {   
        
        # валидация входящих полей
        $field = $request->field;
        $value = $request->value;
        $category = $request->category;

        # запрос получения блогов категории 
        if ( !empty($category) ) {
            $item = $this->itemRepository->findCategoryItems($category);
        }else{
            $item = $this->itemRepository->all();
            $item = $item['raw'];
        }

        $item->orderBy($field, $value);
        
        # формирование url
        $url = !empty($category) ? "/vlog?category={$category}" : "/vlog/";

        # формирование разметки
        $response = array(
            "items" => array(
                "paginate" => $item->paginate(6),
            ),
            "template" => array(
                "items" => "",
                "subfilter" => "",
            ),
            "url" => $url,
            "created_at" => $value,
            "category" => $category,
        );
        $response["template"]["items"] = view( "components/client/vlog/item-component", $response)->render();
        
        return $response;
    }
    /**
     * Display the search results
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $slug)
    {
        $item = $this->itemRepository->find($slug);

        $meta = array(
            'meta_title' => $item['meta_title'],
            'meta_description' => $item['meta_description'],
            'meta_keywords' => $item['meta_keywords'],
            'meta_canonical' => $item['meta_canonical'],
            'meta_h1' => $item['meta_h1'],
        );
        $response = array(
            'item' => array(
                "raw" => $item,
                "similar" => $this->itemRepository->readmore($item->id),
            ),
            'meta' => $meta,
        );
        // dd(__METHOD__, $response);
        return view('client/blog/show', $response);
    }
    /**
     * Display the search results
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function blogpost($slug)
    {
        $pages_plucked = array();
        $pages = Menu::where('slug', $slug)->with(['pages'])->firstOrFail();
        
        # если элемент является подменю, то найдем родительский элемент
        $parent = Menu::where('id', $pages->parent_id)->first();

        # назначим $slug с учетом родительского элемента
        $slug = is_null($parent) ? $slug : $parent->slug; 


        $pages = $pages->toArray();

        return view("/client/blog/show", [
            "pages" => $pages,
            "is_active_menu_slug" => $slug,
        ]);
    }
}
