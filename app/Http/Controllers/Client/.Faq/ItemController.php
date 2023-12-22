<?php

namespace App\Http\Controllers\Client\Faq;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

# use App\Repositories\BlogRepository as ItemRepository;
# use App\Models\Blog\Item as Item;

class ItemController extends Controller
{
    # private ItemRepository $itemRepository;

    public function __construct()
    {
        # $this->itemRepository = $itemRepository;
    }
    /**
     * 
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        return view( "client/faq/index" );
    }
}
