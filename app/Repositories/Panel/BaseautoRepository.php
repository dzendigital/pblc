<?php 
namespace App\Repositories\Panel;

use App\Interfaces\Panel\BaseautoInterface;
use App\Models\Baseauto\Item as Item;

class BaseautoRepository implements BaseautoInterface 
{
    public function all($raw = null)
    {
        $items = array();
        # 05/12/2022 - изменил запрашиваемые отношения для использования в админке
        # $items = Item::latest()->with(["category", "gallery", "parameter", "parameter.name"]);
        # dd(__METHOD__, $items->first()->parameter()->get()->toArray());
        $items = Item::latest()->with(["gallery", "doc"]);
        if ( is_null($raw) ) {
            $items = $items->get();
        }
        return $items;
    }
    public function find($id)
    {
        $item = array();
        $item = Item::where('id', $id)->with(["gallery", "parameter", "parameter.name"]);
        $parameter = $item->first()->parameter()->get()->toArray();
        $gallery = $item->first()->gallery()->get()->toArray();
        
        $response = array(
            "parameter" => $parameter,
            "gallery" => $gallery,
        );

        return $response;
    }
    public function where($where)
    {
        $where_car = $where["where_car"];        
        $where_parameter = $where['where_parameter'];
        $where_parameter_range = $where['where_parameter_range'];

        if ( empty($where_car) ) {
            $items = Item::latest();

            foreach ( $where_parameter as $key => $value ) {
                $w = array(
                    "slug" => $value["slug"],
                    "value" => $value["value"],
                );
                $items->whereHas('parameter', function($query) use ($w){
                    $query->where("slug", $w['slug'])->where("value", $w['value']);
                });
            }
            foreach ( $where_parameter_range as $key => $value ) {
                $items->whereHas('parameter', function($query) use ($value){
                    $query->where("slug", $value["slug"])->where("value", $value["action"], $value["value"]);
                });
            }
        }else{
            $items = Item::latest()->where($where_car);

            foreach ( $where_parameter as $key => $value ) {
                $w = array(
                    "slug" => $value["slug"],
                    "value" => $value["value"],
                );
                $items->whereHas('parameter', function($query) use ($w){
                    $query->where("slug", $w['slug'])->where("value", $w['value']);
                });
            }
            foreach ( $where_parameter_range as $key => $value ) {
                $items->whereHas('parameter', function($query) use ($value){
                    $query->where("slug", $value["slug"])->where("value", $value["action"], $value["value"]);
                });
            }
        }
        return $items;
    }
}
?>
