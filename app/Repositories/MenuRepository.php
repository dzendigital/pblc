<?php 
namespace App\Repositories;

use App\Interfaces\MenuInterface;

use App\Models\Page;
use App\Models\Menu;
use App\Models\User;
use App\Models\Account;

class MenuRepository implements MenuInterface 
{
    public function all()
    {
        $where = array();
        $where[] = array("is_visible", "=", 1);
        $where[] = array("is_onmenu", "=", 1);

        $response = array();
        $response["raw"] = Menu::with(["pages"])->where($where);
        $response["get"] = Menu::with(["pages"])->where($where)->orderBy("sort")->get();

    	return $response["get"];
    }
}
?>