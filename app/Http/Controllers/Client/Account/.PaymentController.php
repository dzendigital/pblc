<?php

namespace App\Http\Controllers\Client\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\Panel\Seller\ItemRequest;
use App\Models\Gallery\Gallery;

use App\Repositories\Client\AccountRepository as ItemRepository;


class PaymentController extends Controller
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
        $response = array(
            "item" => $this->itemRepository->payment()
        );

        return view( "client/account/payment/index", $response);
    }

    
}
