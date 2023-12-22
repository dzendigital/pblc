<?php 
namespace App\Repositories\Panel;

use App\Interfaces\Panel\AccountInterface;
use App\Models\Account as Item;
use App\Models\Baseauto\Item as Auto;

class AccountRepository implements AccountInterface 
{
    public function all()
    {
        $items = array();
        $items = Item::with('user', 'report', 'payment', 'auto', 'auto.parameter', 'auto.gallery')->orderBy('created_at', 'DESC')->get();
        return $items;
    }
    public function byId($id)
    {
        $items = array();
        $items = Item::where('id', $id)->with('user', 'report', 'payment')->first();
        return $items;
    }
    public function accountAuto($account_id, $auto_id)
    {

        $where = array();
        $where[] = array("id", $auto_id);
        $where[] = array("account_id", $account_id);

        $item = Auto::where($where)->first();

        return $item;
    }
}
?>