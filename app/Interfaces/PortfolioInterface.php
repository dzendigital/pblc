<?php
namespace App\Interfaces;


interface PortfolioInterface
{
    /**
     * get all items
     * @method  GET
     * 
     */
    public function all();

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