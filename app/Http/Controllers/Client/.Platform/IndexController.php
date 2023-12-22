<?php

namespace App\Http\Controllers\Client\Platform;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Repositories\SpecialistRepository;
use App\Models\Baseauto\Item as Item;

class IndexController extends Controller
{

    private SpecialistRepository $itemRepository;

    public function __construct(SpecialistRepository $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }
    /**
     * Display a listing of the resource.
     * @param string $slug
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response = array();
        return view( "client/platform/index", $response );
    }
}
