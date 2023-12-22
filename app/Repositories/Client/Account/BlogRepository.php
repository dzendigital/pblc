<?php 
namespace App\Repositories\Client\Account;

use App\Interfaces\Client\BlogInterface;

use App\Models\Blog\Item;
use App\Models\Blog\Category;
use App\Models\Blog\Tag;

class BlogRepository implements BlogInterface 
{

    public function index()
    {   
        $item = Item::orderBy('publish_at', 'DESC')->get();
        return $item;
    }
    public function indexpaginate()
    {   
        $item = Item::orderBy('publish_at', 'DESC')->paginate(6);
        return $item;
    }
    public function accountblog()
    {
        $account = auth()->user()->account;
        if ( is_null($account) ) return [];
        return $account->blog;
    }
    public function findWhere($where)
    {
        $item = Item::withTrashed()->where($where)->first();
        return $item;
    }
    public function category()
    {
        $where = array();
        $where[] = array("is_visible", "=", 1);
        $where[] = array("parent_id", "=", 0);

        $response = Category::where($where)->with(["childs", "childs.childs"])->get();
        return $response;
    }
    public function categoryWhere($where)
    {   
        $whereRaw = array();
        $whereRaw[] = array("is_visible", "=", 1);
        $whereRaw[] = array("parent_id", "=", 0);
        $where = array_merge($where, $whereRaw);

        $response = Category::where($where)->with(["childs", "childs.childs"])->get();
        return $response;
    }
    public function tag()
    {
        $where = array();
        $where[] = array("is_visible", "=", 1);

        $response = Tag::where($where)->get();
        return $response;
    }

}
?>