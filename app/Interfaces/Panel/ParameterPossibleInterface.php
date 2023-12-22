<?php
namespace App\Interfaces\Panel;


interface ParameterPossibleInterface
{
    /**
     * get all items
     * @method  GET
     * 
     */
    public function all($parameter_slug);

    /**
     * get model base on selected brand
     * @method  GET
     * 
     */
    public function model($brand);
    /**
     * get bodyStyle base on selected generation
     * @method  GET
     * 
     */
    public function generation($model);
    /**
     * get model base on selected model
     * @method  GET
     * 
     */
    public function bodyStyle($model);
}

?>