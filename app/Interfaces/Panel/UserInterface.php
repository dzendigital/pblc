<?php
namespace App\Interfaces\Panel;


interface UserInterface
{
    /**
     * get all items
     * @method  GET
     * 
     */
    public function all();

    /**
     * get item by role of logged user
     * 
     * @param   integer     $id
     * @method  GET
     * 
     */
    public function byRole($id);

}

?>