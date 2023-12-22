<?php
namespace App\Interfaces\Client;


interface UserPlatformInterface
{
    /**
     * get account data
     * @method  GET
     * 
     */
    public function account();
    
    /**
     * get item by ID
     * 
     * @param   integer     $id
     * @method  GET
     * 
     */
    public function findUser($id);
}

?>