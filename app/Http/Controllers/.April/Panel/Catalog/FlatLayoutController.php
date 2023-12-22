<?php

namespace App\Http\Controllers\Panel\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

# работа с файлами
use Illuminate\Support\Facades\Storage;

# модели
use App\Models\Catalog\Catalog;


class FlatLayoutController extends Controller
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
            if ( false ) {
                # для дальнейшего сохранения ссылкой на relation использовать структуру ниже
                $url = Storage::put("public/tmp/catalog/flatlayout", $file); 
                $path = Storage::url($url); 
                $filename = $file->getClientOriginalName();
                $files[] = array(
                    "url" => $url,
                    "path" => $path,
                    "filename" => $filename,
                );
            }else{
                $url = Storage::put("public/tmp/catalog/flatlayout", $file); 
                $path = "tmp/catalog/flatlayout/" . $file->hashName(); 
                $filename = $file->getClientOriginalName();
                $files[] = array(
                    "path" => $path,
                    "filename" => $filename,
                );
            }
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
        # запись в базе найдена
        $item = Catalog::find($id);
        $filename = $item->flat_layout;
        # проверка что файл существует
        $is_exists = is_null($filename) ? null : Storage::exists("public/" . $filename);
        if( $is_exists ){
            # если файл существует - удалить
            if( true ){
                # production
                $result_delete = Storage::delete("public/" . $filename);
                $item->flat_layout = null;
                $result_delete_item = $item->save();
            }else{
                $result_delete = true;
            }

        }else{
            # если файл не существует - вернем null и удалим из view
            if( true ){
                # production
                $result_delete = null;
                $item->flat_layout = null;
                $result_delete_item = $item->save();
            }else{
                # dev
                $result_delete = null;
            }
        }
        $response = array(
            'result' => array(
                'status' => $result_delete, # статус удаления файла
                'status_item' => $result_delete_item, # статус удаления записи в БД
                'item' => $item, # запись из базы
            ),    
        );
        return $response;
    }
}
