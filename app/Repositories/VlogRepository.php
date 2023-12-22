<?php 
namespace App\Repositories;

use App\Interfaces\Client\BlogInterface;
use App\Models\Vlog\Item as Item;
use App\Models\Vlog\Category as Category;

class VlogRepository implements BlogInterface 
{
    public function all()
    {
        $where = array();
        $where[] = array("is_visible", "=", 1);

        $response = array();
        $response["raw"] = Item::latest()->with(["category", "video", "gallery"])->where($where);
        $response["get"] = Item::latest()->with(["category", "video", "gallery"])->where($where)->get();
        // dd(__METHOD__, $response['get']);
    	return $response;
    }
    public function category()
    {
        $where = array();
        $where[] = array("is_visible", "=", 1);
        $where[] = array("parent_id", "=", 0);
        $response = Category::latest()->where($where)->with(["childs", "childs.childs"])->get();
        return $response;
    }

    public function find($slug)
    {
        $item = Item::where('slug', $slug)->with(["category", "video", "gallery"])->firstOrFail();
        $item["meta_h1"] = $item["title"];
        $item["created_date"] = date('d.m.Y', strtotime($item["created_at"]));


    	return $item;
    }
    public function findWhere($where)
    {
        $item = Item::where($where)->first();
        dd(__METHOD__, $where, $item);
        return $item;
    }
    public function findCategoryItems($category_slug)
    {
        $where = array();
        $where[] = array("slug", "=", $category_slug);
        $item = Item::whereHas('category', function ($query) use ($where) {
            return $query->where($where);
        })->with(["category", "video", "gallery"]);
        # ->get()
        # ->toArray()
        return $item;
    }

    /**
     *
     * @param $id - id of element to what we are looking for similar
     *  
     */
    public function readmore($id)
    {
        $where = array();
        $where[] = array("is_visible", 1);
        $where[] = array("id", ">", $id);
        
        $items = Item::where($where)->limit(3);

        if ( $items->count() < 3 ) {
            $where = array();
            $where[] = array("is_visible", 1);
            $items = Item::where($where)->limit(3);
        }
        
        return $items->get();
    }
}
?>