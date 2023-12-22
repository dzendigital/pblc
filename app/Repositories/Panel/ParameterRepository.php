<?php 
namespace App\Repositories\Panel;

use App\Interfaces\Panel\ParameterInterface;
use App\Models\Baseauto\Item as Item;
use App\Models\Baseauto\Parameter;
use App\Models\Baseauto\ParameterPossible;
use App\Models\Baseauto\ParameterPossibleValue;

class ParameterRepository implements ParameterInterface 
{
    public function all($parameter_id)
    {
        dd(__METHOD__, $parameter_id);
        $items = array();
        $items = Parameter::latest()->with(["category", "gallery", "parameter", "parameter.name"]);
        if ( is_null($raw) ) {
            $items = $items->get();
        }
        return $items;
    }
    public function collect()
    {
        $response = array();

        // $response['filter']['brand'] = $this->brand();
        $response['filter']['brand'] = $this->collectCarValue("brand");
        
        $response['filter']['transmission'] = $this->collectParameterValue("transmission");
        $response['filter']['wheel_drive'] = $this->collectParameterValue("wheel_drive");
        $response['filter']['fuel_type'] = $this->collectParameterValue("fuel_type");
        $response['filter']['airbags'] = $this->collectParameterValue("airbags");
        $response['filter']['support_systems'] = $this->collectParameterValue("support_systems");
        $response['filter']['isofix'] = $this->collectParameterValue("isofix");
        $response['filter']['headlight'] = $this->collectParameterValue("headlight");
        $response['filter']['heating'] = $this->collectParameterValue("heating");
        $response['filter']['pendant'] = $this->collectParameterValue("pendant");
        $response['filter']['conditioning'] = $this->collectParameterValue("conditioning");
        $response['filter']['camera'] = $this->collectParameterValue("camera");
        $response['filter']['power_windows'] = $this->collectParameterValue("power_windows");
        $response['filter']['power_steering'] = $this->collectParameterValue("power_steering");
        $response['filter']['steering_wheel'] = $this->collectParameterValue("steering_wheel");
        $response['filter']['parking_assist'] = $this->collectParameterValue("parking_assist");
        $response['filter']['cruise'] = $this->collectParameterValue("cruise");
        $response['filter']['disk_type'] = $this->collectParameterValue("disk_type");
        $response['filter']['disk_size'] = $this->collectParameterValue("disk_size");
        $response['filter']['alarm'] = $this->collectParameterValue("alarm");
        $response['filter']['inter_color'] = $this->collectParameterValue("inter_color");
        $response['filter']['power_seats'] = $this->collectParameterValue("power_seats");
        $response['filter']['seat_vent'] = $this->collectParameterValue("seat_vent");
        $response['filter']['interior_material'] = $this->collectParameterValue("interior_material");
        $response['filter']['seat_memory'] = $this->collectParameterValue("seat_memory");
        $response['filter']['seat_number'] = $this->collectParameterValue("seat_number");
        $response['filter']['seat_height'] = $this->collectParameterValue("seat_height");
        $response['filter']['seat_heating'] = $this->collectParameterValue("seat_heating");
        $response['filter']['audio_system'] = $this->collectParameterValue("audio_system");
        
        return $response["filter"];

    }
    public function parameter()
    {
        $response = array();
        $response['parameterPossible'] = $this->parameterPossible();
        # подготовка параметров для формы
        foreach ( $response['parameterPossible'] as $key => $value) {
            $response['parameter'][$value['slug']] = array(
                'id' => $value['id'],
                'title' => $value['title'],
                'possible' => array(),
            );
            if ( !empty($value['possible']->toArray()) ) 
            {
                $response['parameter'][$value['slug']]['possible'] = $value['possible']->toArray();
            }
        }
        $response['parameter'] = collect($response['parameter']);

        return $response["parameter"];
    }
    public function parameterPossible()
    {
        return ParameterPossible::latest()->with(["possible"])->get();
    }
    public function collectCarValue($key = null)
    {
        $response = array();
        $response[$key] = Item::where("is_approved", 1)->select("{$key} as value")->distinct()->get();

        return $response[$key];
    }

    /**
     * получение параметров для фильтра 
     * @param array $slug - slug параметра
     */
    public function collectParameterValue($slug = null)
    {
        $response = array();
        $where = array();
        $where[] = array("slug", $slug);
        $response[$slug] = Parameter::select("value")->whereHas("name", function($query) use ($where){
            return $query->where($where);
        })->distinct()->get();
        # dd(__METHOD__, $response, $where);
        return $response[$slug];
    }
    /**
     * получение параметров для фильтра 
     * получение параметров для фильтра на основе выбранного параметра 
     * @param array $selected - выбранные значение
     * @param array $await_parameters
     */
    public function collectParameterValueFor($selected = null, $await_parameters = null)
    {
        # выбор параметров из карточки товара
        $where_car = array();
        foreach ( $selected as $key => $value ) {
            if ( is_null($value) ) {
                continue;
            }
            $where_car[] = array($key, $value);
        }
        
        
        # выбор параметров
        $item = array();
        foreach ( $await_parameters as $key => $value ) 
        {
            if ( empty($where_car) ) 
            {
                $item[$value] = [];    
                continue;
            }
            $item_tmp = Parameter::whereHas("car", function($query) use ($where_car){
                return $query->where($where_car);
            })->whereHas("name", function($query) use ($value){
                return $query->where("slug", $value);
            })->distinct()->select("value")->get()->toArray();
            $item[$value] = $item_tmp;    
        }
        $result = $item;

        return $result;
    }
    public function collectCarValueFor($selected = null, $await_parameters = null)
    {
        # включение основных запрашиваемых значений
        $select = array();
        foreach ( $await_parameters as $key => $value ) {
            switch ($value) {
                case 'model':
                    $select[] = "model";
                break;
            }
        }

        if ( !is_null($selected) ) 
        {
            # выбор параметров из карточки товара
            $where = array();
            foreach ( $selected as $key => $value ) {
                $where[] = array($key, "=", $value);
            }

            $item = Item::where($where)->select($select)->distinct()->get()->toArray();

        }
        $result = array(
            "model" => array(),
        );
        foreach ($item as $key => $value) {
            foreach ($value as $k => $v) {
                $result[$k][] = array(
                    "value" => $v,
                );
            }
        }
        return $result;        
    }
    
    public function model($model)
    {
        $where = array();
        # получение из значений в базе
        if ( 1 ) 
        {            
            # Способ 1
            # выбор авто по марке 
            # получение бренда
            $item = new Item();
            foreach ($model as $key => $value) 
            {
                if ( empty($value) ) {
                    continue;
                }
                $item = $item->whereHas("parameter", function($query) use ($key){
                    return $query->where("slug", $key);
                })->whereHas("parameter.name", function($query) use ($value){
                    return $query->where("value", $value);
                });
            }
            $item->with(["parameter" => function($query){
                $query->where('slug', '=', "model");
            }]);

            $model_raw = $item->get()->pluck("parameter")->toArray();
            # список поколений по выбранной марки и бренду автомобиля
            $model = array();
            foreach ($model_raw as $key => $value) {
                foreach ($value as $k => $v) {
                    if (!isset($model[$v['value']])) {
                        $model[$v['value']] = $v;
                    }
                }
            }
        }else
        {
            # Способ 2
            $result_raw = ParameterPossibleValue::where("value", "=", $model['brand'])->with(['child'])->get()->pluck("child")->toArray();
            $result = array();
            foreach ($result_raw as $key => $value) {
                foreach ($value as $k => $v) {
                    if (!isset($result[$v['value']])) {
                        $result[$v['value']] = $v;
                    }
                }
            }
        }

        return $result;
    }
    public function generation($model)
    {
        $where = array();
        $item = new Item();
        foreach ($model as $key => $value) {
            if ( empty($value) ) {
                continue;
            }
            $item = $item->whereHas("parameter", function($query) use ($key){
                return $query->where("slug", $key);
            })->whereHas("parameter.name", function($query) use ($value){
                return $query->where("value", $value);
            });
        }
        $item->with(["parameter" => function($query){
            $query->where('slug', '=', "generation");
        }]);

        # список поколений по выбранной марки и бренду автомобиля
        $generation_raw = $item->get()->pluck("parameter")->toArray();
        $generation = array();
        foreach ($generation_raw as $key => $value) {
            foreach ($value as $k => $v) {
                if (!isset($generation[$v['value']])) {
                    $generation[$v['value']] = $v;
                }
            }
        }
        
        return $generation;
    }
    public function bodyStyle($model)
    {
        $where = array();
        $item = new Item();
        foreach ($model as $key => $value) {
            if ( empty($value) ) {
                continue;
            }
            $item = $item->whereHas("parameter", function($query) use ($key){
                return $query->where("slug", $key);
            })->whereHas("parameter.name", function($query) use ($value){
                return $query->where("value", $value);
            });
        }
        $item->with(["parameter" => function($query){
            $query->where('slug', '=', "body_style");
        }]);

        # список "Тип кузова" по выбранной марке, бренду и поколению автомобиля
        $body_style_raw = $item->get()->pluck("parameter")->toArray();

        $body_style = array();
        foreach ($body_style_raw as $key => $value) {
            foreach ($value as $k => $v) {
                if (!isset($body_style[$v['value']])) {
                    $body_style[$v['value']] = $v;
                }
            }
        }

        return $body_style;
    }
    public function ptsOwners($model)
    {
        $where = array();
        $item = new Item();
        foreach ($model as $key => $value) {
            if ( empty($value) ) {
                continue;
            }
            $item = $item->whereHas("parameter", function($query) use ($key){
                return $query->where("slug", $key);
            })->whereHas("parameter.name", function($query) use ($value){
                return $query->where("value", $value);
            });
        }
        $item->with(["parameter" => function($query){
            $query->where('slug', '=', "pts_owners");
        }]);

        # список "Владельцев по ПТС" по выбранной марке, бренду и поколению автомобиля
        $pts_owners_raw = $item->get()->pluck("parameter")->toArray();

        $body_style = array();
        foreach ($pts_owners_raw as $key => $value) {
            foreach ($value as $k => $v) {
                if (!isset($pts_owners[$v['value']])) {
                    $pts_owners[$v['value']] = $v;
                }
            }
        }

        return $pts_owners;
    }
}
?>