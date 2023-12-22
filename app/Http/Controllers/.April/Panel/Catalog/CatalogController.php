<?php

namespace App\Http\Controllers\Panel\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Catalog\Catalog;
use App\Models\Catalog\Gallery;
use App\Models\Catalog\Lots;
use App\Models\Catalog\Characteristics;

use App\Http\Requests\Panel\CatalogRequest;

class CatalogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Catalog::latest()->with(['characteristics'])->get();
        return view( config('app.project') . ".panel/catalog/index", [
            'items' => $items
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CatalogRequest $request)
    {
        # валидация входящих полей
        $validatedData = $request->validated();

        // dd(__METHOD__, $request->validated(), $request->all());

        # создание объекта с данными
        $item = new Catalog($validatedData);

        # сохранение объекта
        $result = $item->save();
        
        # для сохранения нужно условие - прожатый чекбокс - is_active_lots 
        if( !empty($validatedData['is_active_lots']) ){
            # добавляем к $item ссылку на lots
            if( !empty($request->input("lots")) ){
                #сохраняем relation lots
                foreach ($request->input("lots") as $key => $value) {
                    # в базе нет (id в запросе отсутствует) - добавляем
                    $lots_item = new Lots($value);
                    $lots_item->catalog_item_id = $item->id;
                    $item->lots()->save($lots_item);
                }
            }
        }
        # для сохранения нужно условие - прожатый чекбокс - is_active_characteristics 
        if( !empty($validatedData['is_active_characteristics']) ){
            # добавляем к $item ссылку на characteristics
            if( !empty($request->input("characteristics")) ){
                # сохраняем relation characteristics
                foreach ($request->input("characteristics") as $key => $value) {
                    # в базе нет (id в запросе отсутствует) - добавляем
                    $lots_item = new Characteristics($value);
                    $lots_item->catalog_item_id = $item->id;
                    $item->characteristics()->save($lots_item);
                }
            }
        }
        # для сохранения нужно условие - прожатый чекбокс - is_active_gallery 
        if( !empty($validatedData['is_active_gallery']) ){
            # добавляем к $item ссылку на gallery
            if( !empty($request->input("gallery")) ){
                #сохраняем relation gallery
                foreach ($request->input("gallery") as $key => $value) {
                    if( !isset($value['id']) ){
                        # в базе нет (id в запросе отсутствует) - добавляем
                        $gallery_item = new Gallery($value);
                        $gallery_item->catalog_item_id = $item->id;
                        // $gallery_item_insert_result = $gallery_item->save();
                        $item->gallery()->save($gallery_item);
                    }else{
                        # уже в базе
                    }
                }
            }
        }
        # после создания и сохранения отношений - запросим новосозданный объект и вернем его для добавления во view
        $response = array(
            'result' => array(
                'status' => $result,
                'item' => Catalog::where('id', $item->id)->with(['characteristics', 'category', 'gallery', 'lots'])->get(),
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
    public function update(CatalogRequest $request, $id)
    {
        $item = Catalog::findOrFail($id);
        #dd(__METHOD__, $item, $request->all(), !empty($request->input("characteristics")), !empty($request->input("lots")), !empty($request->input("gallery")));
        # обновление characteristics
        if( !empty($request->input("characteristics")) ){
            #удалим все привязки relation characteristics и добавим только те, что пришли
            foreach ($item->characteristics()->get() as $key => $value) {
                $value->delete();
            }
            #сохраняем relation characteristics
            foreach ($request->input("characteristics") as $key => $value) {
                if( !isset($value['id']) ){
                    # в базе нет (id в запросе отсутствует) - добавляем
                    $lots_item = new Characteristics($value);
                    $item->characteristics()->save($lots_item);
                }else{
                    # уже в базе - обновляем поля
                    # т.к привязки были удалены ранее - востановим те, которые остались
                    $lots_item = Characteristics::withTrashed()->find($value['id']);
                    $lots_item->restore();  
                    $lots_item->title = $value['title'];
                    $lots_item->value = $value['value'];
                    $lots_item->save();
                }
            }
        }
        # обновление lots
        if( !empty($request->input("lots")) ){
            #удалим все привязки relation lots и добавим только те, что пришли
            foreach ($item->lots()->get() as $key => $value) {
                $value->delete();
            }
            #сохраняем relation lots
            foreach ($request->input("lots") as $key => $value) {
                if( !isset($value['id']) ){
                    # в базе нет (id в запросе отсутствует) - добавляем
                    $lots_item = new Lots($value);
                    $item->lots()->save($lots_item);
                }else{
                    # уже в базе - обновляем поля
                    # т.к привязки были удалены ранее - востановим те, которые остались
                    $lots_item = Lots::withTrashed()->find($value['id']);
                    $lots_item->restore();  
                    $lots_item->title = $value['title'];
                    $lots_item->floor = $value['floor'];
                    $lots_item->area = $value['area'];
                    $lots_item->price = $value['price'];
                    $lots_item->layout = $value['layout'];
                    $lots_item->save();
                }
            }
        }
        # обновление gallery
        if( !empty($request->input("gallery")) ){
            #сохраняем relation gallery
            foreach ($request->input("gallery") as $key => $value) {
                if( !isset($value['id']) ){
                    if( is_null($value['sort']) ){
                        $value['sort'] = $key;
                    }
                    # в базе нет (id в запросе отсутствует) - добавляем
                    $gallery_item = new Gallery($value);
                    // $gallery_item_insert_result = $gallery_item->save();
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
                'item' => Catalog::where('id', $item->id)->with(['characteristics', 'category', 'gallery', 'lots'])->first(),
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
            $result = Catalog::find($id)->delete();
            $response = array(
                'result' => array(
                    'status' => $result,
                ),
            );
        }else{
            # массовое удаление
            $result = array();
            foreach ( Catalog::whereIn('id', json_decode($request->input('ids'), 1) )->get() as $key => $value ) {
                $result[] = $value->delete();
            }
            $response = array(
                'result' => array(
                    'status' => !in_array(!1, $result),
                    'items' => Catalog::latest()->with(['characteristics', 'category', 'gallery', 'lots'])->get(),
                ),
            );
        }
        
        return $response;
    }
}
