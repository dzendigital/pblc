<?php

namespace App\Http\Controllers\Client\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Gallery\Gallery;


use App\Repositories\Client\UserPlatformRepository as ItemRepository;

class SecurityController extends Controller
{

    private ItemRepository $itemRepository;

    public function __construct(ItemRepository $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    } 
    
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
        $response = array(
            "item" => $this->itemRepository->account()
        );

        return view( "client/user/security/index", $response);
    }

}
