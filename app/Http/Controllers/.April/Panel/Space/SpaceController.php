<?php

namespace App\Http\Controllers\Panel\Space;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Catalog\Space;
use App\Models\Catalog\Gallery;
use App\Models\Catalog\Theme;
use App\Models\Catalog\Presets;

use App\Http\Requests\Panel\Catalog\SpaceRequest;

class SpaceController extends Controller
{
   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // dd(__METHOD__);
        return view( config('app.project') . ".panel/space/index", [
            'items' => Space::latest()->with(['theme', 'gallery', 'presets'])->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ArticlesRequest $request)
    {
        # валидация входящих полей
        $validatedData = $request->validated();


        # создание объекта с данными
        $item = new Articles($validatedData);
        

        # выставляем видимость по-умолчанию
        $item->is_visible = 1;
        
        # сохранение объекта
        $result = $item->save();
        # для тестов: $item = Articles::first();
        
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

        # в объекте есть пресеты:
        if ( $request->input('presets') != null ) {
            # сохраним галерею 
            
            # добавляем к $item ссылку на gallery && сохраняем relation gallery
            foreach ( $request->input("presets") as $key => $value ) {
                if( !isset($value['id']) ){
                    # в базе нет (id в запросе отсутствует) - добавляем фото
                    $preset = new Presets($value);

                    $preset->catalog_item_id = $item->id;
                    // $preset->catalog_item_id = 1;

                    # сохраняем
                    $item->presets()->save($preset);
                    
                }else{
                    # уже в базе
                }
            }

            # обновим привязку
            $item->is_active_gallery = 1;
            $item->save();
        }

        # после создания и сохранения отношений - запросим новосозданный объект и вернем его для добавления во view
        $response = array(
            'result' => array(
                'status' => $result,
                'item' => Articles::where('id', $item->id)->with(['gallery', 'presets'])->get(),
                'items' => Articles::latest()->with(['gallery', 'presets'])->get(),
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
    public function update(ArticlesRequest $request, $id)
    {
        # валидация входящих полей
        $validatedData = $request->validated();
        

        $item = Articles::findOrFail($id);
        
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

        # в объекте есть пресеты:
        if ( $request->input('presets') != null ) {
            # сохраним галерею 
            
            # добавляем к $item ссылку на gallery && сохраняем relation gallery
            foreach ( $request->input("presets") as $key => $value ) {
                if( !isset($value['id']) ){
                    # в базе нет (id в запросе отсутствует) - добавляем фото
                    $preset = new Presets($value);

                    $preset->catalog_item_id = $item->id;
                    // $preset->catalog_item_id = 1;

                    # сохраняем
                    $item->presets()->save($preset);
                    
                }else{
                    # уже в базе
                    $preset = Presets::find($value['id']);

                    $preset['sort'] = $key;
                    $preset->save();
                }
            }

            # обновим привязку
            $item->is_active_gallery = 1;
            $item->save();
        }

        # обновление основной записи
        $result = $item->update( $request->validated() );

        $response = array(
            'result' => array(
                'status' => $result,
                'item' => Articles::where('id', $item->id)->with(['gallery', 'presets'])->get(),
                'items' => Articles::latest()->with(['gallery', 'presets'])->get(),
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
            $result = Articles::find($id)->delete();
            $response = array(
                'result' => array(
                    'status' => $result,
                    'items' => Articles::latest()->with(['gallery', 'presets'])->get(),
                ),
            );
        }else{
            # массовое удаление
            $result = array();
            foreach ( Articles::whereIn('id', json_decode($request->input('ids'), 1) )->get() as $key => $value ) {
                $result[] = $value->delete();
            }
            $response = array(
                'result' => array(
                    'status' => !in_array(!1, $result),
                    'items' => Articles::latest()->with(['gallery', 'presets'])->get(),
                ),
            );
        }
        
        return $response;
    }
}
