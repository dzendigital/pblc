<?php

namespace App\Http\Controllers\Client\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Repositories\AutoRepository;
use App\Models\Baseauto\Item as Item;

class ItemController extends Controller
{

    private AutoRepository $autoRepository;

    public function __construct(AutoRepository $autoRepository)
    {
        $this->autoRepository = $autoRepository;
    }
    /**
     * Display a listing of the resource.
     * @param string $slug
     * @return \Illuminate\Http\Response
     */
    public function index($slug = null)
    {
        dd(__METHOD__, $slug);
        $items = Item::with(["parameter", "account"]);
        $response = array(
            "get" => $items->get(),
            "paginate" => array(
                "raw" => $items->paginate(9),
                "items" => $items->paginate(9),
            ),
        );
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
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $slug)
    {
        $item = $this->autoRepository->find($slug);
        
        $meta = array(
            'meta_title' => $item['meta_title'],
            'meta_description' => $item['meta_description'],
            'meta_keywords' => $item['meta_keywords'],
            'meta_keywords' => $item['meta_keywords'],
        );
        $response = array(
            'item' => array(
                "raw" => $item,
            ),
            'meta' => $meta,
        );
        // dd(__METHOD__, $response);
        return view('client/catalog/show', $response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        dd(__METHOD__, $id);
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
