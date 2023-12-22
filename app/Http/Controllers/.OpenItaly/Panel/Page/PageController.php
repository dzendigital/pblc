<?php

namespace App\Http\Controllers\Panel\Page;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Models\Menu;
use App\Models\Page;
use App\Http\Requests\Panel\PageRequest;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        # меню поддерживает 2 уровня вложености
        $items = Menu::where('parent_id', 0)->latest()->with(['pages', 'childs.pages', 'childs.pages'])->get();
        return view(".panel/pages/index", [
            'items' => $items,
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
    public function store(PageRequest $request)
    {
        # валидация входящих полей
        $validatedData = $request->validated();
         dd(__METHOD__, $validatedData);
        # создание объекта с данными
        $item = new Page($validatedData);

        # сохранение объекта
        $result = $item->save();
        # после создания и сохранения отношений - запросим новосозданный объект и вернем его для добавления во view
        $response = array(
            'result' => array(
                'status' => $result,
                'item' => Page::where('id', $item->id)->with(['menu'])->get(),
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
    public function update(PageRequest $request, $id)
    {
        $item = Page::findOrFail($id);
        # обновление основной записи
        $result = $item->update( $request->validated() );

        $response = array(
            'result' => array(
                'status' => $result,
                'items' => Menu::latest()->with(['pages', 'childs.pages', 'childs.pages'])->get(),
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
    public function destroy($id)
    {
        $result = Page::find($id)->delete();
        $response = array(
            'result' => array(
                'status' => $result,
            ),
        );
        
        return $response;
    }
}
