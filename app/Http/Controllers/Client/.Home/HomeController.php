<?php

namespace App\Http\Controllers\Client\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Directions\Directions;
use App\Models\IndexPage\IndexPage;

class HomeController extends Controller
{
    public function index()
    {
        $directions = Directions::latest()->with(['departure', 'arrival'])->get();
        $departures = $directions->pluck('departure')->unique();
        $arrivals = $directions->pluck('arrival')->unique();
        $page = IndexPage::latest()->with(['presets'])->first(); 

        $response = array(
            "departures" => $departures,
            "arrivals" => $arrivals,
            "page" => $page,
        );

        // dd(__METHOD__, $response);

        return view( "/client/index/index" , $response);
         
    }


}
