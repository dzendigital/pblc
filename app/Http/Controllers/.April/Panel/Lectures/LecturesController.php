<?php

namespace App\Http\Controllers\Panel\Lectures;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

# use App\Models\Catalog\Catalog;
use App\Models\Catalog\Lectures;
use App\Models\Catalog\Gallery;

use App\Http\Requests\Panel\Catalog\LecturesRequest;

class LecturesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view( config('app.project') . ".panel/lectures/index", [
            'items' => Lectures::latest()->with(['gallery'])->get(),
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LecturesRequest $request)
    {
        # валидация входящих полей
        $validatedData = $request->validated();

        # создание объекта с данными
        $item = new Lectures($validatedData);
        
        # выставляем видимость по-умолчанию
        $item->is_visible = 1;
        
        # сохранение объекта
        $result = $item->save();

        # в лекции есть галерея:
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

        # после создания и сохранения отношений - запросим новосозданный объект и вернем его для добавления во view
        $response = array(
            'result' => array(
                'status' => $result,
                'item' => Lectures::where('id', $item->id)->with(['gallery'])->get(),
                'items' => Lectures::latest()->with(['gallery'])->get(),
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
    public function update(LecturesRequest $request, $id)
    {
        # валидация входящих полей
        $validatedData = $request->validated();
        
        $item = Lectures::findOrFail($id);
        
        # в лекции есть галерея:
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
                    
                    # $gallery_item->catalog_item_id = $item->id;

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
        # обновление основной записи
        $result = $item->update( $request->validated() );

        $response = array(
            'result' => array(
                'status' => $result,
                'item' => Lectures::where('id', $item->id)->with(['gallery'])->first(),
                'items' => Lectures::latest()->with(['gallery'])->get(),
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
            $result = Lectures::find($id)->delete();
            $response = array(
                'result' => array(
                    'status' => $result,
                    'items' => Lectures::latest()->with(['gallery'])->get(),
                ),
            );
        }else{
            # массовое удаление
            $result = array();
            foreach ( Lectures::whereIn('id', json_decode($request->input('ids'), 1) )->get() as $key => $value ) {
                $result[] = $value->delete();
            }
            $response = array(
                'result' => array(
                    'status' => !in_array(!1, $result),
                    'items' => Lectures::latest()->with(['gallery'])->get(),
                ),
            );
        }
        
        return $response;
    }
}
