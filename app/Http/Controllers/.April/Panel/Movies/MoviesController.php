<?php

namespace App\Http\Controllers\Panel\Movies;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Catalog\Movies;
use App\Models\Catalog\Gallery;
use App\Models\Catalog\Theme;
use App\Models\Catalog\Video;

use App\Http\Requests\Panel\Catalog\MoviesRequest;

class MoviesController extends Controller
{
   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // dd(__METHOD__,  Movies::latest()->with(['theme', 'gallery', 'video'])->get());
        return view( config('app.project') . ".panel/movies/index", [
            'items' => Movies::latest()->with(['theme', 'gallery', 'video'])->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MoviesRequest $request)
    {
        # валидация входящих полей
        $validatedData = $request->validated();


        # создание объекта с данными
        $item = new Movies($validatedData);

        # выставляем видимость по-умолчанию
        $item->is_visible = 1;

        # сохранение объекта
        $result = $item->save();
        # для тестов: $item = Movies::first();
        
        # в объекте есть галерея:
        if ( $request->input('gallery') != null ) {
            # сохраним галерею 
            
            # добавляем к $item ссылку на gallery && сохраняем relation gallery
            foreach ( $request->input("gallery") as $key => $value ) {
                if( !isset($value['id']) ){
                    # в базе нет (id в запросе отсутствует) - добавляем фото
                    $gallery_item = new Gallery($value);
                    $gallery_item->catalog_item_id = $item->id;

                    # сохраняем
                    $item->gallery()->save($gallery_item);
                    
                }else{
                    # уже в базе
                }
            }

            # обновим привязку
            $item->is_active_gallery = 1;
            # q $item->save();
        }

        # в объекте есть ссылка на видеофайл:
        if ( $request->input('video') != null ) {
            # сохраним ссылку на видеофайл
            foreach ( $request->input("video") as $key => $value ) {
                if( !isset($value['id']) ){
                    # в базе нет (id в запросе отсутствует) - добавляем фото
                    $video_item = new Video($value);
                    $video_item->catalog_item_id = $item->id;

                    # сохраняем
                    $item->gallery()->save($video_item);
                    
                }else{
                    # уже в базе
                }
            }

            # обновим привязку
            $item->is_active_video = 1;
            $item->save();
        }

        # после создания и сохранения отношений - запросим новосозданный объект и вернем его для добавления во view
        $response = array(
            'result' => array(
                'status' => $result,
                'item' => Movies::where('id', $item->id)->with(['theme', 'gallery', 'video'])->get(),
                'items' => Movies::latest()->with(['theme', 'gallery', 'video'])->get(),
            ),
        );    
        return $response;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MoviesRequest $request, $id)
    {
        # валидация входящих полей
        $validatedData = $request->validated();
        
        # найдем обновляемый объект
        $item = Movies::findOrFail($id);

        # в статье есть галерея:
        if ( $request->input('gallery') != null ) {
            #сохраняем relation gallery
            foreach ( $request->input("gallery") as $key => $value ) {
                if( !isset($value['id']) ){

                    # в базе нет (id в запросе отсутствует) - добавляем новое фото
                    if( !isset($value['sort']) || is_null($value['sort']) ){
                        $value['sort'] = $key;
                    }
                    # в базе нет (id в запросе отсутствует) - добавляем
                    $gallery_item = new Gallery($value);

                    # сохраняем
                    $item->gallery()->save($gallery_item);
                }else{
                    # уже в базе, обновим поле sort
                    $gallery_item = Gallery::find($value['id']);

                    $gallery_item['sort'] = $key;
                    $gallery_item->save();
                }
            }
        }
        # в объекте есть ссылка на видеофайл:
        if ( $request->input('video') != null ) {
            # сохраним ссылку на видеофайл
            foreach ( $request->input("video") as $key => $value ) {
                if( !isset($value['id']) ){
                    # в базе нет (id в запросе отсутствует) - добавляем фото
                    $video_item = new Video($value);
                    $video_item->catalog_item_id = $item->id;

                    # сохраняем
                    $item->gallery()->save($video_item);
                    
                }else{
                    # уже в базе, обновим
                    $video_item = Video::find($value['id']);

                    $video_item['url'] = $value['url'];
                    $video_item['sort'] = $key;

                    # сохраняем
                    $video_item->save();
                }
            }
        }

        # обновление основной записи
        $result = $item->update( $request->validated() );

        $response = array(
            'result' => array(
                'status' => $result,
                'item' => Movies::where('id', $item->id)->with(['theme', 'gallery', 'video'])->get(),
                'items' => Movies::latest()->with(['theme', 'gallery', 'video'])->get(),
            ),
        );   
        
        return $response;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if( is_null($request->input('ids')) ){
            $result = Movies::find($id)->delete();
            $response = array(
                'result' => array(
                    'status' => $result,
                    'items' => Movies::latest()->with(['theme', 'gallery', 'video'])->get(),
                ),
            );
        }else{
            # массовое удаление
            $result = array();
            foreach ( Movies::whereIn('id', json_decode($request->input('ids'), 1) )->get() as $key => $value ) {
                $result[] = $value->delete();
            }
            $response = array(
                'result' => array(
                    'status' => !in_array(!1, $result),
                    'items' => Movies::latest()->with(['theme', 'gallery', 'video'])->get(),
                ),
            );
        }
        
        return $response;
    }
}