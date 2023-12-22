<?php

namespace App\Http\Controllers\Client\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Repositories\BlogRepository as ItemRepository;
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
     * Перечень всех статей блога
     * 
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $items = Item::with(["parameter", "account"]);
        if ( isset($request->category) ) {
            $items = array();
            $items["raw"] = $this->itemRepository->findCategoryItems($request->category)->orderBy("created_at", "DESC");
            $items["get"] = $this->itemRepository->findCategoryItems($request->category)->orderBy("created_at", "DESC")->get();

            $url = "/blog/?category={$request->category}";
        }else{
            $items = $this->itemRepository->all();
            $url = "/blog/";
        }

        $response = array(
            "items" => array(
                "category" => $this->itemRepository->category(),
                "raw" => $items["raw"],
                "get" => $items["get"],
                "paginate" => $items["raw"]->orderBy("created_at", "DESC")->paginate(6),
            ),
            "template" => array(
                "items" => "",
                "subfilter" => "",
            ),
            "url" => $url,
            "category" => $request->category,
        );


        $response["template"]["items"] = view( "components/client/blog/item-component", $response)->render();
        $response["template"]["subfilter"] = view( "components/client/blog/subfilter-component", $response)->render();
        return view( "client/blog/index", $response );
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
        $url = !empty($slug) ? "/blog?category={$slug}" : "/blog/";

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
        $response["template"]["items"] = view( "components/client/blog/item-component", $response)->render();
        $response["template"]["subfilter"] = view( "components/client/blog/subfilter-component", $response)->render();
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
        $url = !empty($category) ? "/blog?category={$category}" : "/blog/";

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
        $response["template"]["items"] = view( "components/client/blog/item-component", $response)->render();
        $response["template"]["subfilter"] = view( "components/client/blog/subfilter-component", $response)->render();
        return $response;
    }
    /**
     * Карточка блога
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
            'meta_h1' => $item['title'],
        );
        $response = array(
            'item' => array(
                "raw" => $item,
                "similar" => $this->itemRepository->readmore($item->id),
            ),
            'meta' => $meta,
        );
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
