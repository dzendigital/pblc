<?php

namespace App\Http\Controllers\Panel\Gallery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

# работа с файлами
use Illuminate\Support\Facades\Storage;

# модель audio
use App\Models\Gallery\Audio;


class AudioController extends Controller
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
            $url = Storage::put("public/tmp/catalog/audio", $file); 
            $path = Storage::url($url); 
            $filename = $file->getClientOriginalName();
            $files[] = array(
                "url" => $url,
                "path" => $path,
                "filename" => $filename,
            );
        }
        $response = array(
            'result' => array(
                'status' => true, # change it next time
                'files' => $files,
            ),    
        );
        return $response;
    }

    /**
     * Remove the specified resource from storage.
     * проверяет существует ли фото
     * удаляет с сервера
     *
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        # запись в базе найдена
        $item = Audio::find($id);
        # проверка что файл существует
        $is_exists = is_null($item) ? null : Storage::exists($item->url);
        if( $is_exists ){
            if( true ){
                # если файл существует - удалить
                $result_delete = Storage::delete($item->url);
                $result_delete_item = is_null($item) ? null : $item->delete();
            }else{
                $result_delete = true;
                $result_delete_item = true;
            }

        }else{
            if( true ){
                # production
                # удаляем realtion из БД
                $result_delete = null;
                # если запись существует - удалить
                $result_delete_item = is_null($item) ? null : $item->delete();
            }else{
                # dev
                $result_delete = null;
                $result_delete_item = null;
            }
        }
        $response = array(
            'result' => array(
                'status' => $result_delete, # статус удаления файла
                'status_item' => $result_delete_item, # статус удаления записи
                'item' => $item, # запись из базы
            ),    
        );
        return $response;
    }
}
