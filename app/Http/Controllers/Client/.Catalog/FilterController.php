<?php

namespace App\Http\Controllers\Client\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Requests\Client\Filter\CarRequest;
use App\Http\Requests\Client\Filter\CarRangeRequest;
use App\Http\Requests\Client\Filter\ParameterRequest;
use App\Http\Requests\Client\Filter\ParameterCheckboxRequest;
use App\Http\Requests\Client\Filter\ParameterRangeRequest;

use App\Repositories\Client\BaseautoRepository as ItemRepository;
use App\Repositories\Panel\ParameterRepository as ParameterRepository;
use App\Repositories\Panel\ParameterPossibleRepository as ParameterPossibleRepository;

class FilterController extends Controller
{
    private ItemRepository $itemRepository;

    public function __construct(ItemRepository $itemRepository, ParameterRepository $parameterRepository, ParameterPossibleRepository $parameterPossibleRepository)
    {
        $this->itemRepository = $itemRepository;
        $this->parameterRepository = $parameterRepository;
        $this->parameterPossibleRepository = $parameterPossibleRepository;
    }  

    /**
     * Возвращает не заполненную разметку фильтра и товаров
     * 
     * @return \Illuminate\Http\Response
     */
    public function default()
    {
        $data = array();
        $response = array();
        $data["items"] = $this->itemRepository->all(true);
        $data["paginate"] = array(
            "raw" => $data['items'],
            "items" => $data['items']->paginate(9),
        );

        $data['filter'] = $this->parameterRepository->collect();
        $data['filter']['count'] = $data['items']->count();

        // <x-client.catalog.filter-component :filter="$filter"/>
        // <x-client.catalog.item-component :items="$response['paginate']['items']"/>

        $response["template"]['filter'] = view( "components/client/catalog/filter-component", $data)->render();
        $response["template"]['catalog'] = view( "components/client/catalog/item-component", $data['paginate'])->render();
        return $response;
    }
    /**
     * Возвращает разметку товаров с учетом фильтра
     * 
     * @return \Illuminate\Http\Response
     */
    public function select(CarRequest $request_car, CarRangeRequest $request_car_range, ParameterRequest $request_parameter, ParameterCheckboxRequest $request_parameter_checkbox, ParameterRangeRequest $request_parameter_range)
    {
        # формируем данные
        $validated_car = $request_car->validated();
        $validated_parameter = $request_parameter->validated();
        $validated_car_range = $request_car_range->validated();
        $validated_parameter_range = $request_parameter_range->validated();
        $request_parameter_checkbox = $request_parameter_checkbox->validated();

        # формируем where к 2-м таблицам

        $where_car = array();
        $where_parameter = array();
        $where_parameter_range = array();
        
        foreach ( $validated_car as $key => $value) {
            $where_car[] = array($key, "=", $value);
        }
        foreach ( $validated_parameter as $key => $value) {
            $where_parameter[] = array(
                "slug" => $key,
                "value" => $value,
            );
        }
        foreach ( $validated_car_range as $key => $value) {
            switch ($key) {
                case 'price_from':
                    $where_car[] = array("price", ">=", $value);
                break;
                case 'price_to':
                    $where_car[] = array("price", "<=", $value);
                break;
                case 'mileage_from':
                    $where_car[] = array("mileage", ">=", $value);
                break;
                case 'mileage_to':
                    $where_car[] = array("mileage", "<=", $value);
                break;
                case 'year_from':
                    $where_car[] = array("year", ">=", $value);
                break;
                case 'year_to':
                    $where_car[] = array("year", "<=", $value);
                break;
            }
        }
        foreach ( $validated_parameter_range as $key => $value) {
            switch ($key) {
                case 'engine_size_from':
                    $where_parameter_range[] = array(
                        "slug" => "engine_size",
                        "action" => ">=",
                        "value" => $value,
                    );
                break;
                case 'engine_size_to':
                    if ( !isset($validated_parameter_range["engine_size_from"]) || $validated_parameter_range["engine_size_from"] < $validated_parameter_range["engine_size_to"] ) {
                        # dd(__METHOD__, $value, floatval($value));
                        $where_parameter_range[] = array(
                            "slug" => "engine_size",
                            "action" => "<=",
                            "value" => number_format(floatval($value), 1, '.', ''),
                        );
                    }
                break;
                case 'ride_height_from':
                    # $where_parameter[] = array("ride_height", ">=", $value);
                    $where_parameter_range[] = array(
                        "slug" => "ride_height",
                        "action" => ">=",
                        "value" => intval($value),
                    );
                break;
                case 'ride_height_to':
                    if ( $validated_parameter_range["ride_height_from"] < $validated_parameter_range["ride_height_to"] ) {
                        # $where_parameter[] = array("ride_height", "<=", $value);
                        $where_parameter_name[] = array("slug", "=", $key);
                        $where_parameter_possible[] = array("value", "<=", $value);
                    }
                break;
                case 'horse_power_from':
                    # $where_parameter[] = array("horse_power", ">=", $value);
                    $where_parameter_range[] = array(
                        "slug" => "horse_power",
                        "action" => ">=",
                        "value" => intval($value),
                    );
                break;
                case 'horse_power_to':
                    if ( $validated_parameter_range["horse_power_from"] < $validated_parameter_range["horse_power_to"] ) {
                        # $where_parameter[] = array("horse_power", "<=", $value);
                        $where_parameter_name[] = array("slug", "=", $key);
                        $where_parameter_possible[] = array("value", "<=", $value);
                    }
                break;
                case 'acceleration_from':
                    # $where_parameter[] = array("acceleration", ">=", $value);
                    $where_parameter_range[] = array(
                        "slug" => "acceleration",
                        "action" => ">=",
                        "value" => number_format(floatval($value), 1, '.', ''),
                    );
                break;
                case 'acceleration_to':
                    if ( $validated_parameter_range["acceleration_from"] < $validated_parameter_range["acceleration_to"] ) {
                        # $where_parameter[] = array("acceleration", "<=", $value);
                        $where_parameter_name[] = array("slug", "=", $key);
                        $where_parameter_possible[] = array("value", "<=", $value);
                    }
                break;
                case 'consumption_from':
                    # $where_parameter[] = array("consumption", ">=", $value);
                    $where_parameter_range[] = array(
                        "slug" => "consumption",
                        "action" => ">=",
                        "value" => number_format(floatval($value), 1, '.', ''),
                    );
                break;
                case 'consumption_to':
                    if ( $validated_parameter_range["consumption_from"] < $validated_parameter_range["consumption_to"] ) {
                        # $where_parameter[] = array("consumption", "<=", $value);
                        $where_parameter_name[] = array("slug", "=", $key);
                        $where_parameter_possible[] = array("value", "<=", $value);
                    }
                break;
                case 'trunk_from':
                    # $where_parameter[] = array("trunk", ">=", $value);
                    $where_parameter_range[] = array(
                        "slug" => "trunk",
                        "action" => ">=",
                        "value" => intval($value),
                    );
                break;
                case 'trunk_to':
                    if ( $validated_parameter_range["trunk_from"] < $validated_parameter_range["trunk_to"] ) {
                        # $where_parameter[] = array("trunk", "<=", $value);
                        $where_parameter_name[] = array("slug", "=", $key);
                        $where_parameter_possible[] = array("value", "<=", $value);
                    }
                break;
            }
        }
        foreach ( $request_parameter_checkbox as $key => $value) {
            if ( !$value ) {
                continue;
            }
            $where_parameter[] = array(
                "slug" => $key,
                "value" => 1,
            );
        }
        
        # dd(__METHOD__, $where_car, $where_parameter, $validated_parameter);
        # запрос 
        
        $response = array();
        $items = $this->itemRepository->where(array("where_car" => $where_car, "where_parameter" => $where_parameter, "where_parameter_range" => $where_parameter_range));

        $response = array(
            "items" => $items->paginate(9),
            "get" => $items->get(),
        );
        $response['filter']['count'] = $response['get']->count();

        $response["template"] = view( "components/client/catalog/item-component", $response)->render();
        
        return $response;
        
    }
    /**
     * Возвращает разметку полей фильтра после выбора бренда
     * 
     * model (in item)
     * generation
     * body_style
     * pts_owners
     * @return \Illuminate\Http\Response
     */
    public function brand(Request $request)
    {
        $validated = $request->all();

        # получаем модели
        $model = $this->parameterPossibleRepository->model($validated['brand']);
        // $model = is_null($validated['brand']) ? array() : $this->parameterRepository->model($validated);

        $data = array(
            "name" => "Модель",
            "id" => "model",
            "data" => $model,
        );
        $response["template"]["model"] = view( "components/client/catalog/filterinput/select-model-component", $data)->render();
        # получаем дефолтное значение "Поколение"
        $data = array(
            "name" => "Поколение",
            "id" => "generation",
            "data" => array(),
        );
        $response["template"]["generation"] = view( "components/client/catalog/filterinput/select-generation-component", $data)->render();
        # получаем значение "Тип кузова"
        $data = array(
            "name" => "Тип кузова",
            "id" => "body_style",
            "data" => array(),
        );
        $response["template"]["body_style"] = view( "components/client/catalog/filterinput/select-body-style-component", $data)->render();
        if ( !1 ) {
            # получаем значение "Владельцев по ПТС"
            $pts_owners = $this->parameterRepository->ptsOwners($validated);
            $data = array(
                "name" => "Владельцев по ПТС",
                "id" => "pts_owners",
                "data" => $pts_owners,
            );
            $response["template"]["pts_owners"] = view( "components/client/catalog/filterinput/select-pts-owners-component", $data)->render();
        }

        return $response;
    }
    public function model(Request $request)
    {
        $validated = $request->all();
        # $generation = empty($validated['model']) ? array() : $this->parameterRepository->generation($validated);
        $generation = $this->parameterPossibleRepository->generation($validated);

        $data = array(
            "name" => "Поколение",
            "id" => "generation",
            "data" => $generation,
        );

        $response["template"]["generation"] = view( "components/client/catalog/filterinput/select-generation-component", $data)->render();

        if ( !1 ) {
            # получаем значение "Владельцев по ПТС"
            $pts_owners = $this->parameterRepository->ptsOwners($validated);
            $data = array(
                "name" => "Владельцев по ПТС",
                "id" => "pts_owners",
                "data" => $pts_owners,
            );
            $response["template"]["pts_owners"] = view( "components/client/catalog/filterinput/select-pts-owners-component", $data)->render();
        }

        return $response;
    }
    public function generation(Request $request)
    {

        $validated = $request->all();
        $bodyStyle = $this->parameterRepository->bodyStyle($validated);
        
        $data = array(
            "name" => "Тип кузова",
            "id" => "body_style",
            "data" => $bodyStyle,
        );

        $response["template"]["body_style"] = view( "components/client/catalog/filterinput/select-body-style-component", $data)->render();
        if ( !1 ) {
            # получаем значение "Владельцев по ПТС"
            $pts_owners = $this->parameterRepository->ptsOwners($validated);
            $data = array(
                "name" => "Владельцев по ПТС",
                "id" => "pts_owners",
                "data" => $pts_owners,
            );
            $response["template"]["pts_owners"] = view( "components/client/catalog/filterinput/select-pts-owners-component", $data)->render();
        }

        return $response;
    }


}
