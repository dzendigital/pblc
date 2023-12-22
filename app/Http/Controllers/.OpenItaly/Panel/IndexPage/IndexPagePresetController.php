<?php

namespace App\Http\Controllers\Panel\IndexPage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexPagePresetController extends Controller
{

    public $presets = array(
        1 => array(
            'id' => null, # id по которому блок можно найти в бд привязанным к статье
            'title' => "Custom title",
            'type' => "text-preset",
            'list' => array(),
            'content' => "",
            'sort' => '',
        ),
    );
   
    /**
     * 
     * В данном классе используется для получения пресетов текстовых блоков
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        
        return response()->json($this->presets);
    }


    /**
     * 
     * В данном классе используется для получения пресета по id
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if ( isset($this->presets[$id]) ) {
            # для того, чтобы различать текстовые блоки для новой записи - добавим hash
            $this->presets[$id]['hash'] = time();
            return response()->json($this->presets[$id]);
        }
        
    }
}
