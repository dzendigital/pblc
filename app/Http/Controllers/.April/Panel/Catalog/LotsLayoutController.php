<?php

namespace App\Http\Controllers\Panel\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

# работа с файлами
use Illuminate\Support\Facades\Storage;

# модель lots
use App\Models\Catalog\Lots;

class LotsLayoutController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $path = array(); # получаем путь файла, по которому его можно использовать во view
        $url = array(); # получаем путь файла, по которому его можно найти с помощью Storage::exists($url)
        $files = array();
        foreach ($request->file('file') as $file) {
            $url = Storage::put("public/tmp/catalog/lotslayout", $file); 
            $path = "tmp/catalog/lotslayout/" . $file->hashName(); 
            $filename = $file->getClientOriginalName();
            $files[] = array(
                "path" => $path,
                "filename" => $filename,
            );
        }
        $response = array(
            'result' => array(
                'status' => Storage::exists("public/" . $path), # возвращаем часть пути до файла: public/ + $path - фактическое расположение файла
                'path' => $path, # возвращаем часть пути до файла: $path - если добавить /storage получить путь к файлу для view
            ),    
        );
        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
