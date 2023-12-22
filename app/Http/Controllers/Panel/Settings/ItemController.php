<?php

namespace App\Http\Controllers\Panel\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\Panel\Settings\ItemRequest;
use App\Models\Setting\Item;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response = array();
        $response['items'] = Item::latest()->get();  

        return view("panel/settings/index", $response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  
     * @return \Illuminate\Http\Response
     */
    public function store(ItemRequest $request)
    {   

        # валидация входящих полей
        $validatedData = $request->validated();


        # создание объекта с данными
        $item = new Item($validatedData);

        # выставляем видимость по-умолчанию
        $item->is_visible = 1;
        
        # сохранение объекта
        $result = $item->save();

        # после создания и сохранения отношений - запросим новосозданный объект и вернем его для добавления во view
        $response = array(
            'result' => array(
                'status' => $result,
                'items' => Item::latest()->get(),
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
    public function update(ItemRequest $request, $id)
    {

        # валидация входящих полей
        $validatedData = $request->validated();

        # поиск обновляемой записи
        $item = Item::findOrFail($id);

        # сохраним slug
        $item->slug = $request->input('slug');

        # обновим основную запись
        $result = $item->update($validatedData);

        # после обновления
        $response = array(
            'result' => array(
                'status' => $result,
                'items' => Item::latest()->get(),
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
            $result = Item::find($id)->delete();
            $response = array(
                'result' => array(
                    'status' => $result,
                    'items' => Item::latest()->get(),
                ),
            );
        }else{
            # массовое удаление
            $result = array();
            foreach ( Item::whereIn('id', json_decode($request->input('ids'), 1) )->get() as $key => $value ) {
                $result[] = $value->delete();
            }
            $response = array(
                'result' => array(
                    'status' => !in_array(!1, $result),
                    'items' => Item::latest()->get(),
                ),
            );
        }
        
        return $response;
    }

}
