<?php
namespace App\Interfaces\Client;


interface NotificationInterface
{
    /**
     * get all notification of auth account
     * @method  GET
     * 
     */
    public function allBy($role_slug);
    /**
     * get not recived notification of auth account
     * @method  GET
     * 
     */
    public function recieveBy($role_slug);
}

?>