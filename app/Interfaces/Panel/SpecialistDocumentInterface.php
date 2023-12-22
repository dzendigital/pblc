<?php
namespace App\Interfaces\Panel;


interface SpecialistDocumentInterface
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



}

?>