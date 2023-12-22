<?php

namespace App\Http\Controllers\Client\Notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Repositories\SpecialistRepository;
use App\Models\Baseauto\Item as Item;

use App\Repositories\Client\NotificationPlatformRepository;

class IndexController extends Controller
{

    private NotificationPlatformRepository $itemRepository;

    public function __construct(NotificationPlatformRepository $itemRepository)
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

    }
    /**
     * Update auth user is_recieved notification
     */
    public function show()
    {
        switch (auth()->user()->roles->first()->slug) {
            case 'user':
                $is_recieved = $this->itemRepository->recieveAll("user");
            break;

            case 'specialist':
                $is_recieved = $this->itemRepository->recieveAll("specialist");    
            break;
            
            default:
                $is_recieved = null;
            break;
        }

        return $is_recieved;
    }
}
