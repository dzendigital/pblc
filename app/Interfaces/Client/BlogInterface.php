<?php
namespace App\Interfaces\Client;


interface BlogInterface
{
    /**
     * get all items
     * @method  GET
     * 
     */
    # public function all();

    /**
     * get category
     * 
     * @method  GET
     * 
     */
    # public function category();

    /**
     * get tag
     * 
     * @method  GET
     * 
     */
    public function tag();
    
    /**
     * get item by ID
     * 
     * @param   integer     $id
     * @method  GET
     * 
     */
    # public function find($slug);


    /**
     * get item by where condition
     */
    public function findWhere($where);
    
    /**
     * get item by ID
     * 
     * @param   integer     $id
     * @method  GET
     * 
     */
    # public function findCategoryItems($category_slug);


    /**
     * get previous && next item to the item
     * 
     * @method  GET
     * 
     */
    # public function readmore($id);


}

?>