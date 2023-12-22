<?php 
namespace App\Repositories\Panel;

use App\Interfaces\Panel\ParameterPossibleInterface;
use App\Models\Baseauto\Item as Item;
use App\Models\Baseauto\Parameter;
use App\Models\Baseauto\ParameterPossible;
use App\Models\Baseauto\ParameterPossibleValue;

class ParameterPossibleRepository implements ParameterPossibleInterface 
{
    public function all($parameter_slug)
    {
        $items = array();
        $items = ParameterPossible::where("slug", $parameter_slug)->with(["possible"]);
        // dd(__METHOD__, $items->first()->possible()->orderBy("value")->get());
        $result = $items->first()->possible()->orderBy("value")->get()->toArray();
        // dd(__METHOD__, $result);
        
        return $result;
    }
    public function brand()
    {
        $brand_possible = ParameterPossible::where('slug', '=', 'brand')->with(["possible"])->first();
        return $brand_possible->possible()->orderBy("value")->get()->toArray();
    }
    public function model($brand)
    {
        # запрашиваем id параметра (бренд в целом)
        $parameter = ParameterPossible::where("slug", "brand")->first();

        # запрашиваем возможное значение бренда (например BMW)
        $parameter_possible_brand = ParameterPossibleValue::where("value", $brand)->with(['child'])->first();
        # запрашиваем значения потомки
        $result = is_null($parameter_possible_brand) ? array() : $parameter_possible_brand->child()->get()->toArray();        
        
        return $result;
    }
    public function generation($model)
    {
        $where = array();
        $where[] = array("value", $model["brand"]);
        $brand_value = ParameterPossibleValue::where($where)->with(['child'])->first();


        $model_parameter_id = ParameterPossible::where("slug", "generation")->first()->id;
        $where = array();
        $where[] = array("parent_id", $brand_value["id"]);
        $where[] = array("value", $model["model"]);
        # $where[] = array("parameter_id", $model_parameter_possible->id);
        # $model_value = ParameterPossibleValue::where($where)->with(['child'])->first();
        $model_value = ParameterPossibleValue::where($where)->with(["child" => function($query) use ($model_parameter_id){
            $query->where('parameter_id', '=', $model_parameter_id);
        }])->first();

        # запрашиваем значения потомки
        $result = is_null($model_value->child) ? array() : $model_value->child->toArray();        
        
        return $result;
    }
    public function bodyStyle($model)
    {

        $where = array();
        $where[] = array("value", $model["brand"]);
        $brand_value = ParameterPossibleValue::where($where)->first();

        $where = array();
        $where[] = array("parent_id", $brand_value["id"]);
        $where[] = array("value", $model["model"]);
        $model_value = ParameterPossibleValue::where($where)->first();

        # если у авто отсутствует Поколение, запрашиваем Тип кузова
        if ( isset($model["generation"]) ) 
        {
            $where = array();
            $where[] = array("parent_id", $model_value["id"]);
            $where[] = array("value", $model["generation"]);
            # $where[] = array("paramerer_id", 4);
            $generation_value = ParameterPossibleValue::where($where)->with(['child'])->first();
            # запрашиваем значения потомки
            $result = is_null($generation_value) ? array() : $generation_value->child()->get()->toArray();        
        }else{
            $where = array();
            $where[] = array("parent_id", $brand_value["id"]);
            $where[] = array("value", $model["model"]);
            # $where[] = array("paramerer_id", 4);
            $generation_value = ParameterPossibleValue::where($where)->with(['child'])->first();
            # запрашиваем значения потомки
            $result = is_null($generation_value) ? array() : $generation_value->child()->get()->toArray();        

        }
        $value = isset($model["generation"]) ? $model["generation"] : $model["model"];

        // dd(__METHOD__, $generation_value->toArray());
        
        return $result;
    }

    public function parameterPossibleAdmin()
    {

        $tmp["transmission"] = ParameterPossible::where("slug", "transmission")->first()->toArray();
        $tmp["wheel_drive"] = ParameterPossible::where("slug", "wheel_drive")->first()->toArray();
        $tmp["wheel_position"] = ParameterPossible::where("slug", "wheel_position")->first()->toArray();
        $tmp["fuel_type"] = ParameterPossible::where("slug", "fuel_type")->first()->toArray();
        $tmp["airbags"] = ParameterPossible::where("slug", "airbags")->first()->toArray();
        $tmp["support_systems"] = ParameterPossible::where("slug", "support_systems")->first()->toArray();
        $tmp["isofix"] = ParameterPossible::where("slug", "isofix")->first()->toArray();
        $tmp["headlight"] = ParameterPossible::where("slug", "headlight")->first()->toArray();
        $tmp["heating"] = ParameterPossible::where("slug", "heating")->first()->toArray();
        $tmp["pendant"] = ParameterPossible::where("slug", "pendant")->first()->toArray();
        $tmp["conditioning"] = ParameterPossible::where("slug", "conditioning")->first()->toArray();
        $tmp["camera"] = ParameterPossible::where("slug", "camera")->first()->toArray();
        $tmp["power_windows"] = ParameterPossible::where("slug", "power_windows")->first()->toArray();
        $tmp["power_steering"] = ParameterPossible::where("slug", "power_steering")->first()->toArray();
        $tmp["steering_wheel"] = ParameterPossible::where("slug", "steering_wheel")->first()->toArray();
        $tmp["parking_assist"] = ParameterPossible::where("slug", "parking_assist")->first()->toArray();
        $tmp["cruise"] = ParameterPossible::where("slug", "cruise")->first()->toArray();
        $tmp["disk_type"] = ParameterPossible::where("slug", "disk_type")->first()->toArray();
        $tmp["disk_size"] = ParameterPossible::where("slug", "disk_size")->first()->toArray();
        $tmp["alarm"] = ParameterPossible::where("slug", "alarm")->first()->toArray();
        $tmp["inter_color"] = ParameterPossible::where("slug", "inter_color")->first()->toArray();
        $tmp["power_seats"] = ParameterPossible::where("slug", "power_seats")->first()->toArray();
        $tmp["seat_vent"] = ParameterPossible::where("slug", "seat_vent")->first()->toArray();
        $tmp["interior_material"] = ParameterPossible::where("slug", "interior_material")->first()->toArray();
        $tmp["seat_memory"] = ParameterPossible::where("slug", "seat_memory")->first()->toArray();
        $tmp["seat_number"] = ParameterPossible::where("slug", "seat_number")->first()->toArray();
        $tmp["seat_height"] = ParameterPossible::where("slug", "seat_height")->first()->toArray();
        $tmp["seat_heating"] = ParameterPossible::where("slug", "seat_heating")->first()->toArray();
        $tmp["audio_system"] = ParameterPossible::where("slug", "audio_system")->first()->toArray();

        foreach ($tmp as $key => $value) {
            $response[$value['slug']] = ParameterPossibleValue::where("parameter_id", $value['id'])->get()->toArray();
            if ( $value['slug'] == "disk_size" ) {
                // dd(__METHOD__, $response[$value['slug']]);
            }
        }
       
        return $response;
    }
}
?>