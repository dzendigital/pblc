<?php

namespace App\Http\Controllers\Client\Forms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

# events
use App\Events\NewUserEvent;

# request
use App\Http\Requests\Client\CallbackFormRequest;


class ClientHelpFormController extends Controller
{
    /**
     * Submit current form
     * @return \Illuminate\Http\Response
     */
    public function index(CallbackFormRequest $request)
    {
        NewUserEvent::dispatch( $request->validated() );
        
        $response = array(
            'result' => array(
                'status' => 1,
            ),
            'messages' => array(
                "Ваша заявка отправлена.",
                "Ожидайте ответа от представителя DOMOI Reqlty.",
            ),
        );    
        return $response;
    }
}
