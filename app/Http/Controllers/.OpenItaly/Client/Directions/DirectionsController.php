<?php

namespace App\Http\Controllers\Client\Directions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Directions\Directions;
# use App\Models\Client\Catalog\Catalog;
# use App\Models\Client\Catalog\Category;

class DirectionsController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param string $slug
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {        
        # необходимые данные
        $directions = Directions::latest()->with(['departure', 'arrival'])->get();

        $departures = $directions->pluck('departure')->unique();
        $arrivals = $directions->pluck('arrival')->unique();
        
        $response = array(
            'items' => array(),
            'directions' => array(),
            "departures" => $departures,
            "arrivals" => $arrivals,
            "transport" => array(),
            "form" => array(
                "departure" => $request->input('departure'),
                "arrival" => $request->input('arrival'),
                "departure_date" => $request->input('departure_date'),
            ),      
        );   
        return view( "client/destinations/search", $response);
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
     * In this context method store use for search with post request as index method
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        # поиск в БД по заданным параметрам: departure_city, arrival_city, (not departure_date)
        # $items = Directions::latest()->with(['departure', 'arrival', 'transport'])->get();

        # заполняем $where
        $where = array();
        $where['departure'][] = array('title', '=', $request->input('departure'));
        $where['arrival'][] = array('title', '=', $request->input('arrival'));


        # поисковой запрос
        $search_results = Directions::whereHas('departure', function ($query) use ($where) {
            return $query->where($where['departure']);
        })->whereHas('arrival', function ($query) use ($where) {
            return $query->where($where['arrival']);
        })->latest()->has('transport')->with(['departure', 'arrival', 'transport', 'transport.gallery'])->get();

        if ( is_null($search_results->first()) ) {
            return redirect("/destinations-search");
        }else{
            $response = array(
                'departure' => $request->input('departure'),
                'arrival' => $request->input('arrival'),
            );
            return redirect("/destinations-search/{$search_results->first()->slug}");
        }

        # необходимые данные
        $directions = Directions::latest()->with(['departure', 'arrival'])->get();
        $departures = $directions->pluck('departure')->unique();
        $arrivals = $directions->pluck('arrival')->unique();
        
        $transport = $search_results->pluck('transport');

        $response = array(
            'items' => $search_results,
            'directions' => $search_results->first(),
            "departures" => $departures,
            "arrivals" => $arrivals,
            "transport" => $transport,
            "form" => array(
                "departure" => $request->input('departure'),
                "arrival" => $request->input('arrival'),
                "departure_date" => $request->input('departure_date'),
            ),      
        );   
        return view( "client/destinations/search", $response);
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

        # поиск в БД по заданным параметрам: slug

        # поисковой запрос
        // $search_results = Directions::latest()->where("slug", "=", $slug)->with(['departure', 'arrival', 'transport', 'transport.gallery'])->get();
        $search_results = Directions::latest()->where("slug", "=", $slug)->with(['departure', 'arrival', 'transport', 'transport.gallery'])->firstOrFail();

        # необходимые данные
        $directions = Directions::latest()->with(['departure', 'arrival'])->get();
        $departures = $directions->pluck('departure')->unique();
        $arrivals = $directions->pluck('arrival')->unique();
        
        
        // $transport = $search_results->pluck('transport');
        $transport = $search_results->transport;
        // dd(__METHOD__, $transport);
        // dd(__METHOD__, $search_results, $transport);

        $response = array(
            'items' => $search_results,
            'directions' => $search_results->first(),
            "departures" => $departures,
            "arrivals" => $arrivals,
            "transport" => $transport,
            "form" => array(
                "departure" => $search_results->departure()->first()->title,
                "arrival" => $search_results->arrival()->first()->title,
                "departure_date" => $request->input('departure_date'),
            ),      
        );   

        return view( "client/destinations/search", $response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

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
