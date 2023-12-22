<?php
namespace App\Interfaces\Panel;


interface AccountInterface
{
    /**
     * get all items
     * @method  GET
     * 
     */
    public function all();

    /**
     * get item by id
     * @method  GET
     * 
     */
    public function byId($id);

    /**
     * get auto and account by id
     * @method  GET
     * 
     */
    public function accountAuto($account_id, $auto_id);


}

?>