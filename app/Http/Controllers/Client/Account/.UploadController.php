<?php

namespace App\Http\Controllers\Client\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\Panel\School\ItemRequest;
use App\Models\Gallery\Gallery;

use App\Http\Requests\Client\Baseauto\ItemRequest as AutoRequest;
use App\Repositories\Panel\ParameterPossibleRepository as ParameterPossibleRepository;
use App\Repositories\Client\AccountRepository as AccountRepository;
use App\Repositories\AutoRepository;

use App\Models\Account;
use App\Models\Baseauto\Item as AutoModel;
use App\Models\Baseauto\ParameterPossible as ParameterPossibleModel;
use App\Models\Baseauto\Parameter as ParameterModel;



class UploadController extends Controller
{

    private ParameterPossibleRepository $parameterPossibleRepository;
    private AccountRepository $accountRepository;
    private AutoRepository $autoRepository;

    public function __construct(ParameterPossibleRepository $parameterPossibleRepository, AccountRepository $accountRepository, AutoRepository $autoRepository)
    {
        $this->parameterPossibleRepository = $parameterPossibleRepository;
        $this->autoRepository = $autoRepository;
        $this->accountRepository = $accountRepository;
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
        #3  $brand = $this->parameterPossibleRepository->all("brand");

        # необходимые данные
        $account = $this->accountRepository->account();
        $response = array(
            "approve" => $account->approve->toArray(),
            "moderate" => $account->moderate->toArray(),
        );

        return view( "client/account/upload/index", $response);
    }
    public function form(Request $request)
    {   

        # необходимые данные
        $response = array(
        );
        return view( "client/account/upload/form", $response);
    }
    /**
     * Display a possible variants for model, based on brand value
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param string $slug
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
        $response["template"]["model"] = view( "components/client/account/upload/filterinput/select-model-component", $data)->render();

        # получаем дефолтное значение "Поколение"
        $data = array(
            "name" => "Поколение",
            "id" => "generation",
            "data" => array(),
        );
        $response["template"]["generation"] = view( "components/client/account/upload/filterinput/select-generation-component", $data)->render();
        # получаем значение "Тип кузова"
        $data = array(
            "name" => "Тип кузова",
            "id" => "body_style",
            "data" => array(),
        );
        $response["template"]["body_style"] = view( "components/client/account/upload/filterinput/select-body-style-component", $data)->render();

        return $response;
    }

    /**
     * Display a possible variants for generation, based on model value
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param string $slug
     * @return \Illuminate\Http\Response
     */
    public function model(Request $request)
    {   
        $validated = $request->all();
        $generation = $this->parameterPossibleRepository->generation($validated);
        # $body_style = $this->parameterPossibleRepository->bodyStyle($validated['model']);
        
        $response = array(
        );
        if ( !empty($generation) ) {
            # если у данной модели есть Поколение, построим разметку
            $data = array(
                "name" => "Поколение",
                "id" => "generation",
                "data" => $generation,
            );
            $response["template"]["generation"] = view( "components/client/account/upload/filterinput/select-generation-component", $data)->render();
            
            $data = array(
                "name" => "Тип кузова",
                "id" => "body_style",
                "data" => array(),
            );
            $response["template"]["body_style"] = view( "components/client/account/upload/filterinput/select-body-style-component", $data)->render();

        }else{
            $data = array(
                "name" => "Поколение",
                "id" => "generation",
                "data" => array(),
            );
            $response["template"]["generation"] = view( "components/client/account/upload/filterinput/select-generation-component", $data)->render();
            # если у данной модели отсутствует Поколение, построим разметку Кузова
            $bodyStyle = $this->parameterPossibleRepository->bodyStyle($validated);
            $data = array(
                "name" => "Тип кузова",
                "id" => "body_style",
                "data" => $bodyStyle,
            );
            $response["template"]["body_style"] = view( "components/client/account/upload/filterinput/select-body-style-component", $data)->render();

        }

        return $response;
    }/**
     * Display a possible variants for body_style, based on model value
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param string $slug
     * @return \Illuminate\Http\Response
     */
    public function generation(Request $request)
    {   
        $validated = $request->all();

        $bodyStyle = $this->parameterPossibleRepository->bodyStyle($validated);
        # $body_style = $this->parameterPossibleRepository->bodyStyle($validated['model']);
        # dd(__METHOD__, $generation);
        $response = array(
        );
        $data = array(
            "name" => "Тип кузова",
            "id" => "body_style",
            "data" => $bodyStyle,
        );
        $response["template"] = view( "components/client/account/upload/filterinput/select-body-style-component", $data)->render();

        return $response;
    }

    /**
     * Display the create form for school
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {        
        $where = array();
        $where[] = array("user_id", auth()->user()->id);
        $item = School::where($where)->first();
        dd(__METHOD__, $item);
        if ( !is_null($item) ) {
            return redirect("/account");
        }
        return view("/client/account/profile/create"); 
    }

    /**
     * In this context method store use for: filter and return template
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
        $request->validate(array('vin' => 'required', 'gallery' => 'required'), array('vin.required' => "Поле vin обязательно к заполнению (основные параметры).", 'gallery.required' => "Необходимо загрузить фотографию (основные параметры)."));
        $auto_validated = $request->only(['gallery', 'comment']);
        $parameter_validated = $request->except(['gallery', 'comment']);
        $gallery_validated = $request->input('gallery');

        $account = Account::where("user_id", auth()->user()->id)->first();

        # создание объекта с данными
        $item_model = array(
            'title' => isset($request['brand']) ? $request['brand'] : 'Название не указано',
            'model' => isset($request['model']) ? $request['model'] : null,
            'brand' => isset($request['brand']) ? $request['brand'] : null,
            'body_short' => isset($request['comment']) ? $request['comment'] : null,
            'year' => isset($request['year']) ? $request['year'] : null,
            'mileage' => isset($request['mileage']) ? $request['mileage'] : 0,
            'price' => isset($request['price']) ? $request['price'] : 0,
            'is_visible' => null,
            'account_id' => $account->id,
            'is_approved' => null,
            'approve_created_at' => null,
        );

        $auto = new AutoModel($item_model);
        
        $result = $auto->save();

        if ( $gallery_validated != null ) {
            foreach ( $gallery_validated as $key => $value ) {
                if( !isset($value['id']) ){
                    # в базе нет (id в запросе отсутствует) - добавляем фото
                    $gallery_item = new Gallery($value);
                    $gallery_item->sort = $value['sort'];
                    $gallery_item->save();

                    # сохраняем
                    $auto->gallery()->save($gallery_item);   
                }
            }
        }

        foreach ($parameter_validated as $key => $value) {
            $parameter_possible = ParameterPossibleModel::where("slug", $key)->first();
            if ( is_null($parameter_possible) ) {
                dd(__METHOD__, $parameter_possible, $value, $key);
            }
            $parameter_model = new ParameterModel([
                "car_id" => $auto->id,
                "parameter_id" => $parameter_possible['id'],
                "value" => $value,
                "is_visible" => 1,
            ]);
            $parameter_model->slug = $key;
            # dd(__METHOD__, $parameter_model);
            # привязка параметра со значением к авто
            $auto->parameter()->save($parameter_model);
            // $parameter->save();
        }
        
        $response = array(
            "status" => $result,
        );
        return $response;
    }

    /**
     * Display the search results
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $slug)
    {
        # запрос авто по slug
        $item = $this->autoRepository->find($slug);
        # необходимые данные
        $response = array(
            "account" => $this->accountRepository->account(),
            "item" => $item,
            "route" => "/account/upload/update",
            "method" => "PUT",
        );


        return view( "client/account/upload/form", $response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        # $required_default = array('vin' => 'required', 'gallery' => 'required');
        $required_default = array('vin' => 'required');
        $request->validate($required_default, array('vin.required' => "Поле vin обязательно к заполнению (основные параметры).", 'gallery.required' => "Необходимо загрузить фотографию (основные параметры)."));
        $auto_validated = $request->only(['gallery', 'comment']);
        $parameter_validated = $request->except(['gallery', 'comment']);
        $gallery_validated = $request->input('gallery');

        $account = Account::where("user_id", auth()->user()->id)->first();
        # обновление объекта с данными
        $item_model = array(
            'title' => isset($request['brand']) ? $request['brand'] : 'Название не указано',
            'model' => isset($request['model']) ? $request['model'] : null,
            'brand' => isset($request['brand']) ? $request['brand'] : null,
            'body_short' => isset($request['comment']) ? $request['comment'] : null,
            'year' => isset($request['year']) ? $request['year'] : null,
            'mileage' => isset($request['mileage']) ? intval($request['mileage']) : 0,
            'price' => isset($request['price']) ? intval($request['price']) : 0,
            'is_visible' => 1,
            'account_id' => $account->id,
            'is_approved' => null,
            'approve_created_at' => null,
        );
        $auto = AutoModel::where("id", $request->input("itemid"))->first();
        $is_update = null;
        $is_update = $auto->update($item_model);

        # удаления старых привязок
        $auto->gallery()->detach();
        if ( $gallery_validated != null ) {
            foreach ( $gallery_validated as $key => $value ) {
                if( !isset($value['id']) ){
                    # в базе нет (id в запросе отсутствует) - добавляем фото
                    $gallery_item = new Gallery($value);
                    $gallery_item->sort = $value['sort'];
                    $gallery_item->save();

                    # сохраняем
                    $auto->gallery()->save($gallery_item);   
                }
            }
        }

        foreach ($parameter_validated as $key => $value) 
        {

            if ( $key == 'itemid' ) 
            {
                continue;
            }
            if ( in_array($key, explode(", ", "year, mileage, price, color") ) ) 
            {
                continue;
            }
            if ($value === true) {
                $value = 1;
            }
            if ($value === false) {
                $value = null;
            }

            $parameter_possible = ParameterPossibleModel::where("slug", $key)->first();
            

            if ( is_null($parameter_possible) ) {
                dd(__METHOD__, $parameter_possible, $value, $key);
            }

            #1 
            $parameter_value = ParameterPossibleModel::where("slug", $key)->first();
            
            #2
            $where = array();
            $where[] = array("car_id", $auto->id);
            $where[] = array("slug", $key);
            $is_parameter = ParameterModel::where($where)->first();

            if ( empty($is_parameter) ) 
            {
                $model_raw = array(
                    "car_id" => $auto->id,
                    "parameter_id" => $parameter_value->id,
                    "slug" => $key,
                    "value" => $value,
                    "is_visible" => 1,
                    "sort" => null,
                );
                $model = new ParameterModel($model_raw);
                $is_save = $model->save();
                continue;
            }

            if ( $is_parameter->value == $value ) {
                continue;
            }

            # назначаем новое значение
            # сохраняем
            $is_parameter->value = $value;
            $is_update = $is_parameter->save();

        }

        $response = array(
            "status" => 1,
        );
        return $response;
    }

}
