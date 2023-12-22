<?php

namespace App\Http\Controllers\Client\Forms\Callback;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

# events
use App\Events\Form\ContactusEvent;
use App\Events\Form\CallbackEvent;
use App\Events\Form\ServiceEvent;
use App\Events\Form\CalculatorEvent;

# request
use App\Http\Requests\Client\Form\CallbackFormRequest;
use App\Http\Requests\Client\Form\ServiceFormRequest;
use App\Http\Requests\Client\Form\ContactusFormRequest;
use App\Http\Requests\Client\Form\CalculatorFormRequest;

# model
use App\Models\Setting\Item as Setting;

class FormController extends Controller
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
    /**
     * Submit current form
     * @return \Illuminate\Http\Response
     */
    public function callback(CallbackFormRequest $request)
    {
        $validated = $request->validated();

        $validated['emailto'] = Setting::where('slug', 'email')->first()->value;
        $validated['method'] = "callback";
        CallbackEvent::dispatch( $validated );
        
        $response = array(
            'result' => array(
                'status' => 1,
            ),
            'messages' => array(
                "Ваша заявка отправлена.",
                "Ожидайте звонка от менеджера.",
            ),
        );    
        return $response;
    }
    /**
     * Submit current form
     * @return \Illuminate\Http\Response
     */
    public function service(ServiceFormRequest $request)
    {
        $validated = $request->validated();

        $validated['emailto'] = Setting::where('slug', 'email')->first()->value;

        ServiceEvent::dispatch( $validated );
        
        $response = array(
            'result' => array(
                'status' => 1,
            ),
            'messages' => array(
                "Ваша заявка отправлена.",
                "Ожидайте звонка от менеджера.",
            ),
        );    
        return $response;
    }
    /**
     * Submit current form
     * @return \Illuminate\Http\Response
     */
    public function contactus(ContactusFormRequest $request)
    {
        $validated = $request->validated();
        $validated['emailto'] = Setting::where('slug', 'email')->first()->value;
        $validated['method'] = "contactus";

        ContactusEvent::dispatch( $validated );
        
        $response = array(
            'result' => array(
                'status' => 1,
            ),
            'messages' => array(
                "Ваша заявка отправлена.",
                "Ожидайте ответа от представителя компании.",
            ),
        );    
        return $response;
    }
    /**
     * Submit current form
     * @return \Illuminate\Http\Response
     */
    public function calculator(CalculatorFormRequest $request)
    {
        $validated = $request->validated();

        $validated['emailto'] = Setting::where('slug', 'email')->first()->value;
        $validated['method'] = "calculator";

        CalculatorEvent::dispatch( $validated );
        
        $response = array(
            'result' => array(
                'status' => 1,
            ),
            'messages' => array(
                "Ваша заявка отправлена.",
                "Ожидайте ответа от представителя компании.",
            ),
        );    
        return $response;
    }
}
