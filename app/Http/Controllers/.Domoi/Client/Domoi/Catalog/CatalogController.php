<?php

namespace App\Http\Controllers\Client\Domoi\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Domoi\Client\Catalog\Catalog;
use App\Models\Domoi\Client\Catalog\Category;

class CatalogController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param string $slug
     * @return \Illuminate\Http\Response
     */
    public function index($slug = null)
    {
        $category = Category::where('slug', $slug)->first();
        if( !is_null($slug) && is_null($category) ){
            abort(404);
        }
        $catalog = is_null($category) ? Catalog::latest()->with(['characteristics', 'category', 'gallery', 'lots'])->get() : Catalog::latest()->where('category_id', $category->id)->with(['characteristics', 'category', 'gallery', 'lots'])->get();

        $response = array(
            'catalog' => $catalog,
            'filter' => array(
                'count' => Catalog::latest()->count(),
                'price' => array(
                    'min' => 0,
                    'max' => $catalog->max('price'),
                ), # цена
                'area' => array(
                    'min' => 0,
                    'max' => $catalog->max('area'),
                ), # площадь
                'location' => Catalog::distinct('location')->get('location')->toArray(), 
                'complete_date' => null, 
            ),
        );
        return view('client/domoi/catalog/index', $response);
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
