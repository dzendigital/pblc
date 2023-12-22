<?php

namespace App\Http\Controllers\Panel\Settings;

use Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Map\City;



class CityController extends Controller
{

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        # определение списка городов
        $city = City::where("id", $id)->first();
        if ( Session::has('settings.platformcity') ) {
            if ( Session::get('settings.platformcity') != $city["title"] ) {
                Session::put('settings.platformcity', $city["title"]);
            }
        }else{
            Session::put('settings.platformcity', $city["title"]);
        }
        # return Redirect::to("/platform", 301);
        return redirect()->back();
    }

}
