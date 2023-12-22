<?php
namespace App\Interfaces;


interface TaskInterface
{
    /**
     * get all items
     * @method  GET
     * 
     */
    public function all();

    /**
     * paginate items
     * @method  GET
     * 
     */
    public function paginate($limit);

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