<?php

namespace App\Http\Controllers\Client\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Baseauto\Item as Item;


use App\Repositories\Client\BaseautoRepository as ItemRepository;
use App\Repositories\Panel\ParameterRepository as ParameterRepository;
use App\Repositories\Panel\ParameterPossibleRepository as ParameterPossibleRepository;

class CatalogController extends Controller
{

    private ItemRepository $itemRepository;
    private ParameterRepository $parameterRepository;

    public function __construct(ItemRepository $itemRepository, ParameterRepository $parameterRepository, ParameterPossibleRepository $parameterPossibleRepository)
    {
        $this->itemRepository = $itemRepository;
        $this->parameterRepository = $parameterRepository;
        $this->parameterPossibleRepository = $parameterPossibleRepository;
    }  


    /**
     * Display a listing of the resource.
     * @param string $slug
     * @return \Illuminate\Http\Response
     */
    public function index($slug = null)
    {
        $response = array();
        $response["items"] = $this->itemRepository->all(true);
        $response["paginate"] = array(
            "raw" => $response['items'],
            "items" => $response['items']->paginate(9),
        );

        $response['filter'] = $this->parameterRepository->collect();
        $response['filter']['count'] = $response['items']->count();
        $response['filter']['brand'] = $this->parameterPossibleRepository->brand();

        return view( "client/catalog/index", $response );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // zilaya-nedvizimost
        // kommerceskaya-nedvizimost
        $item = Catalog::where('id', $id)->with(['characteristics', 'category', 'gallery', 'lots'])->first();
        $items = Catalog::latest()->with(['characteristics', 'category', 'gallery', 'lots'])->get();
        $meta = array(
            'title' => $item['title'],
            'description' => $item['title'],
            'kaywords' => $item['title'],
            'h1' => "",
        );
        $response = array(
            'item' => $item,
            'items' => $items,
            'meta' => $meta,
        );
        // dd(__METHOD__, $response);
        return view('client/domoi/catalog/show', $response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
