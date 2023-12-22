<?php
namespace App\Interfaces\Client;


interface AccountInterface
{
    /**
     * get account data
     * @method  GET
     * 
     */
    public function account();
    /**
     * get rootuser data
     * @method  GET
     * 
     */
    public function rootuser();
    
    /**
     * get account' report data
     * @method  GET
     * 
     */
    public function report();

    /**
     * get account' report data with orderBy column
     * @method  GET
     * 
     */
    public function reportOrderBy($orderKey, $orderValue);

    /**
     * get account' payment data
     * @method  GET
     * 
     */
    public function payment();

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