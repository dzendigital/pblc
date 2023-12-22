<?php

namespace App\Http\Controllers\Panel\Space;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Catalog\Lectures;

class SpaceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Lectures::latest()->with(['gallery'])->whereNull('created_at')->get();
        return view( config('app.project') . ".panel/space/index", [
            'items' => $items
        ]);
    }
}
