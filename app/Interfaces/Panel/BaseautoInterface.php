<?php
namespace App\Interfaces\Panel;


interface BaseautoInterface
{
    /**
     * get all items
     * @method  GET
     * 
     */
    public function all($raw);

    /**
     * get items with where condition
     * @method  GET
     * 
     */
    public function where($where);

    
    /**
     * get item by ID
     * 
     * @param   integer     $id
     * @method  GET
     * 
     */
    public function find($id);

}

?>