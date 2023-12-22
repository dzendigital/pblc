<?php 
namespace App\Repositories\Client\Account;

use App\Interfaces\Client\AccountInterface;

use App\Models\User as RootUser;
use App\Models\Account as Item;
use App\Models\Account\Report as Report;
use App\Models\Account\Payment as Payment;

class AccountRepository implements AccountInterface 
{
    public function account()
    {
        $item = array();
        if ( !is_null(auth()->user()) ) {
            $item = Item::where("user_id", auth()->user()->id)->with(["gallery"])->first();
        }
        return $item;
    }
    public function rootuser()
    {
        $item = array();
        if ( !is_null(auth()->user()) ) {
            $item = RootUser::where("id", auth()->user()->id)->first();
        }
        return $item;
    }
    public function report()
    {
        $item = array();
        # $item = Report::where("user_id", auth()->user()->id)->with(["gallery", "report"])->groupBy("created_at")->get();
        $where = array();
        $where[] = auth()->user()->id;
        $item = [];
        $item = Report::whereHas('account', function ($query) use ($where) {
            return $query->whereIn("user_id", $where);
        })->get()->groupBy(function($data) {
            return \Carbon\Carbon::parse($data->created_at)->format('d.m.Y');
        })->toArray();
        
        return $item;
    }
    public function reportOrderBy($orderKey, $orderValue)
    {
        $item = array();
        $where = array();
        $where[] = auth()->user()->id;
        $item = [];
        // $item = Report::whereHas('account', function ($query) use ($where) {
        //     return $query->whereIn("user_id", $where);
        // })->get()->groupBy(function($data) {
        //     return \Carbon\Carbon::parse($data->created_at)->format('d.m.Y');
        // })->toArray();
        $item = Report::whereHas('account', function ($query) use ($where) {
            return $query->whereIn("user_id", $where);
        })->orderBy($orderKey, $orderValue)->get()->groupBy(function($data) {
            return \Carbon\Carbon::parse($data->created_at)->format('d.m.Y');
        })->toArray();
        return $item;
    }
    public function payment()
    {
        $item = array();
        $where = array();
        $where[] = auth()->user()->id;
        $item = [];
        $item = Payment::whereHas('account', function ($query) use ($where) {
            return $query->whereIn("user_id", $where);
        })->get()->toArray();
        return $item;
    }
    public function find($id)
    {
        $item = Item::where("user_id", $id)->first();
        
        return $item;
    }
}
?>