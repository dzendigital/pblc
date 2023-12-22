<?php
namespace App\Interfaces\Panel;


interface ParameterInterface
{
    /**
     * get all items
     * @method  GET
     * 
     */
    public function all($parameter_id);
    /**
     * collect all in one parameters
     * @method  GET
     * 
     */
    public function collect();
    /**
     * collect avaliable parameters from car database
     * @method  GET
     * 
     */
    public function collectCarValue($key = null);
    /**
     * collect avaliable parameters from parameter database
     * @method  GET
     * 
     */
    public function collectParameterValue($slug = null);
    
    /**
     * collect avaliable parameters from car database for selected values
     * @method  GET
     * 
     */
    public function collectCarValueFor($selected = null, $await_parameters = null);
    
    /**
     * collect avaliable parameters for selected values 
     * @method  GET
     * 
     */
    public function collectParameterValueFor($selected = null, $await_parameters = null);

}

?>