<?php

namespace App\Http\Controllers\Client\Portfolio;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Repositories\PortfolioRepository as ItemRepository;
use App\Models\Portfolio\Item as Item;

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
        $items = $this->itemRepository->all();

        $response = array(
            "items" => array(
                "raw" => $items["raw"],
                "get" => $items["get"],
                "paginate" => $items["raw"]->paginate(6),
            )
        );
        return view( "client/portfolio/index", $response );
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
        return view('client/portfolio/show', $response);
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
