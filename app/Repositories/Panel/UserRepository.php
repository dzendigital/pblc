<?php 
namespace App\Repositories\Panel;

use App\Interfaces\Panel\UserInterface;
use App\Models\User as Item;

class UserRepository implements UserInterface 
{
    public function all()
    {
        $items = array();
        $items = Item::with('roles')->orderBy('created_at', 'DESC')->get();
        return $items;
    }
    public function byRole($role)
    {
        $items = array();
        $roles = array();
        switch ($role) {
            case 'manager':
                $roles[] = 'manager';
                $roles[] = 'user';
                $roles[] = 'specialist';
                $roles[] = 'account';
            break;
            
            default:
                // code...
                break;
        }
        if ( !empty($roles) ) 
        {
            $items = Item::whereHas('roles', function ($query) use ($roles) {
                return $query->whereIn("slug", $roles);
            })->with('roles')->orderBy('created_at', 'DESC')->get();
        }else{
            $items = Item::with('roles')->orderBy('created_at', 'DESC')->get();
        }
        return $items;
    }
}
?>