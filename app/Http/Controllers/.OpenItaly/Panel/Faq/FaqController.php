<?php

namespace App\Http\Controllers\Panel\Faq;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\Panel\Faq\FaqRequest;

use App\Models\Faq\Faq;


class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("panel/faq/index", [
            'items' => Faq::latest()->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\Panel\Faq\FaqRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FaqRequest $request)
    {
        # валидация входящих полей
        $validatedData = $request->validated();


        # создание объекта с данными
        $item = new Faq($validatedData);
        
        # выставляем видимость по-умолчанию
        $item->is_visible = 1;
                
        # сохранение объекта
        $result = $item->save();


        # после создания и сохранения отношений - запросим новосозданный объект и вернем его для добавления во view
        $response = array(
            'result' => array(
                'status' => $result,
                'items' => Faq::latest()->get(),
            ),
        );    
        return $response;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\Panel\Transport\FaqRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(FaqRequest $request, $id)
    {
        # валидация входящих полей
        $validatedData = $request->validated();
        
        # поиск обновляемой записи
        $item = Faq::findOrFail($id);

        # выставляем видимость 
        # $item->is_visible = $request->input("is_visible") ? 1 : null;

        # выставляем отображение страницы на главной 
        # $item->is_index = $request->input("is_index") ? 1 : null;

        # обновление основной записи
        $result = $item->update( $request->validated() );

        # ответ
        $response = array(
            'result' => array(
                'status' => $result,
                'items' => Faq::latest()->get(),
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
            $result = Transport::find($id)->delete();
            $response = array(
                'result' => array(
                    'status' => $result,
                    'items' => Transport::latest()->with(['gallery'])->get(),
                ),
            );
        }else{
            # массовое удаление
            $result = array();
            foreach ( Transport::whereIn('id', json_decode($request->input('ids'), 1) )->get() as $key => $value ) {
                $result[] = $value->delete();
            }
            $response = array(
                'result' => array(
                    'status' => !in_array(!1, $result),
                    'items' => Transport::latest()->with(['gallery'])->get(),
                ),
            );
        }
        
        return $response;
    }
}
