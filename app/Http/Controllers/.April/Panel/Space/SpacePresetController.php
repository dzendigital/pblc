<?php

namespace App\Http\Controllers\Panel\Space;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SpacePresetController extends Controller
{

    public $presets = array(
        1 => array(
            'id' => null, # id по которому блок можно найти в бд привязанным к статье
            'title' => "Текстовый блок",
            'type' => "text-preset",
            'content' => "",
        ),
        2 => array(
            'id' => null, # id по которому блок можно найти в бд привязанным к статье
            'title' => "Цитата",
            'type' => "quote-preset",
            'content' => "",
        ),
        3 => array(
            'id' => null, # id по которому блок можно найти в бд привязанным к статье
            'title' => "Блок 2 изображения",
            'type' => "two-image-preset",
            'content' => "",
            'list' => array(),
        ),
        4 => array(
            'id' => null, # id по которому блок можно найти в бд привязанным к статье
            'title' => "Блок 3 изображения",
            'type' => "three-image-preset",
            'content' => "",
            'list' => array(),
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
