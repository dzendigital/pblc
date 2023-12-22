<?php

namespace App\Http\Controllers\Client\Domoi\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Domoi\Client\Catalog\Catalog;

class FilterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $offset = !is_null($request->input('start')) ? $request->input('start') : 0;
        $response = array(
            'items' => Catalog::latest()->with(['characteristics', 'category', 'gallery', 'lots'])->offset($offset)->limit(4)->get(),
            'count' => Catalog::latest()->count(),
        );
        return response()->json($response);
    }


}
