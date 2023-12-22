<?php

namespace App\Http\Controllers\Client\Forms\Calculator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

# events
use App\Events\Form\ContactusEvent;
use App\Events\Form\CallbackEvent;
use App\Events\Form\CalculatorEvent;

# request
use App\Http\Requests\Client\Form\CallbackFormRequest;
use App\Http\Requests\Client\Form\ContactusFormRequest;
use App\Http\Requests\Client\Form\CalculatorFormRequest;

# model
use App\Models\Setting\Item as Setting;

# repos
use App\Repositories\Panel\ParameterPossibleRepository;

class IndexController extends Controller
{
    private ParameterPossibleRepository $parameterPossibleRepository;
    
    public function __construct(ParameterPossibleRepository $parameterPossibleRepository)
    {
        $this->parameterPossibleRepository = $parameterPossibleRepository;
    }
    /**
     * Submit current form
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        dd(__METHOD__, $request->all());
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
     * Return model select by brand
     * @return \Illuminate\Http\Response
     */
    public function brand(Request $request)
    {
        $validated = $request->all();
        $model = $this->parameterPossibleRepository->model($validated['brand']);

        $response = array(
        );
        $data = array(
            "name" => "Модель",
            "id" => "model",
            "data" => $model,
        );
        $response["template"]["model"] = view( "components/client/form/calculator/select-model-component", $data)->render();

        # получаем дефолтное значение "Поколение"
        $data = array(
            "name" => "Поколение",
            "id" => "generation",
            "data" => array(),
        );
        $response["template"]["generation"] = view( "components/client/form/calculator/select-generation-component", $data)->render();

  
        return $response;
    }
    /**
     * Return generation select by brand && model
     * @return \Illuminate\Http\Response
     */
    public function model(Request $request)
    {
        $validated = $request->all();
        $generation = $this->parameterPossibleRepository->generation($validated);

        $response = array(
        );
        $data = array(
            "name" => "Поколение",
            "id" => "generation",
            "data" => $generation,
        );
        $response["template"]["generation"] = view( "components/client/form/calculator/select-generation-component", $data)->render();

        return $response;
    }
}
