<?php

namespace App\Http\Controllers\Panel\Podcasts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Catalog\Podcasts;
use App\Models\Catalog\Gallery;
use App\Models\Catalog\Theme;
use App\Models\Catalog\Audio;
use App\Models\Catalog\Links;

use App\Http\Requests\Panel\Catalog\PodcastsRequest;

class PodcastsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view( config('app.project') . ".panel/podcasts/index", [
            'items' => Podcasts::latest()->with(['gallery', 'audio', 'links'])->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PodcastsRequest $request)
    {
        # валидация входящих полей
        $validatedData = $request->validated();

        # создание объекта с данными
        $item = new Podcasts($validatedData);
        # выставляем видимость по-умолчанию
        $item->is_visible = 1;
        
        # сохранение объекта
        $result = $item->save();
        # для тестов: $item = Podcasts::first();
            
        # в объекте есть галерея: фото
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
            $item->save();
        }

        # в объекте есть галерея: аудио
        if ( $request->input('audio') != null ) {
            # сохраним галерею 
            
            # добавляем к $item ссылку на gallery && сохраняем relation gallery
            foreach ( $request->input("audio") as $key => $value ) {
                if( !isset($value['id']) ){
                    # в базе нет (id в запросе отсутствует) - добавляем фото
                    $gallery_item = new Audio($value);
                    $gallery_item->catalog_item_id = $item->id;

                    # сохраняем
                    $item->audio()->save($gallery_item);
                    
                }else{
                    # уже в базе
                }
            }

            # обновим привязку
            $item->is_active_audio = 1;
            $item->save();
        }

        # в объекте есть ссылки
        if ( $request->input('links') != null ) {
            # сохраним 
            $links_item = new Links();
            $links_item['links'] = $request->input('links.links');
            
            $links_item->catalog_item_id = $item->id;

            # сохраняем
            $item->links()->save($links_item);

            # обновим привязку
            $item->is_active_links = 1;
            $item->save();
        }


        # после создания и сохранения отношений - запросим новосозданный объект и вернем его для добавления во view
        $response = array(
            'result' => array(
                'status' => $result,
                'item' => Podcasts::where('id', $item->id)->with(['gallery', 'audio', 'links'])->get(),
                'items' => Podcasts::latest()->with(['gallery', 'audio', 'links'])->get(),
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
    public function update(PodcastsRequest $request, $id)
    {
        # валидация входящих полей
        $validatedData = $request->validated();
        

        $item = Podcasts::findOrFail($id);
        
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

        # в объекте есть галерея: аудио
        if ( $request->input('audio') != null ) {
            # сохраним галерею 
            
            # добавляем к $item ссылку на gallery && сохраняем relation gallery
            foreach ( $request->input("audio") as $key => $value ) {
                if( !isset($value['id']) ){
                    # в базе нет (id в запросе отсутствует) - добавляем фото
                    $gallery_item = new Audio($value);
                    $gallery_item->catalog_item_id = $item->id;

                    # сохраняем
                    $item->audio()->save($gallery_item);
                    
                }else{
                    # уже в базе, обновим поле sort
                    $gallery_item = Audio::find($value['id']);

                    $gallery_item['sort'] = $key;
                    $gallery_item->save();
                }
            }

            # обновим привязку
            # $item->is_active_audio = 1;
            # $item->save();
        }

        # в объекте есть ссылки
        if ( $request->input('links') != null ) {
            # обновим: уже в базе, обновим поле sort
            $links_item = Links::find($request->input('links.id'));
            $links_item['links'] = $request->input('links.links');

            $links_item->save();

            # обновим привязку
            # $item->is_active_links = 1;
            # $item->save();
        }

       
        # обновление основной записи
        $result = $item->update( $request->validated() );

        $response = array(
            'result' => array(
                'status' => $result,
                'item' => Podcasts::where('id', $item->id)->with(['gallery', 'audio', 'links'])->get(),
                'items' => Podcasts::latest()->with(['gallery', 'audio', 'links'])->get(),
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
            $result = Podcasts::find($id)->delete();
            $response = array(
                'result' => array(
                    'status' => $result,
                    'items' => Podcasts::latest()->with(['gallery', 'audio', 'links'])->get(),
                ),
            );
        }else{
            # массовое удаление
            $result = array();
            foreach ( Podcasts::whereIn('id', json_decode($request->input('ids'), 1) )->get() as $key => $value ) {
                $result[] = $value->delete();
            }
            $response = array(
                'result' => array(
                    'status' => !in_array(!1, $result),
                    'items' => Podcasts::latest()->with(['gallery', 'audio', 'links'])->get(),
                ),
            );
        }
        
        return $response;
    }
}
