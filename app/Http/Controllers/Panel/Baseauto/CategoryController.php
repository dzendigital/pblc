<?php

namespace App\Http\Controllers\Panel\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Http\Requests\Panel\Blog\CategoryRequest;
use App\Models\Blog\Item;
use App\Models\Blog\Category;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $response = array();
        # $response['page'] = IndexPage::latest()->with(['presets'])->first();  
        return view("panel/Item/index", $response);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {   

        # валидация входящих полей
        $validatedData = $request->validated();

        # создание объекта с данными
        $item = new Category($validatedData);        

        # выставляем видимость по-умолчанию
        $item->is_visible = 1;
        # выставляем сортировку по-умолчанию
        $item->sort = 1;
        
        # сохранение объекта
        $result = $item->save();

        # после создания и сохранения отношений - запросим новосозданный объект и вернем его для добавления во view
        $response = array(
            'result' => array(
                'status' => $result,
                'item' => Category::where('id', $item->id)->get(),
                'items' => Category::latest()->where('parent_id', 0)->with(['childs'])->get(),
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
    public function update(CategoryRequest $request, $id)
    {

        # валидация входящих полей
        $validatedData = $request->validated();

        # найдем в БД запрашиваемую запись
        $item = Category::findOrFail($id);
            
        # сохраним title
        $item->title = $request->input('title');

        # сохраним slug
        $item->slug = $request->input('slug');

        # сохраним is_visible
        $item->is_visible = $request->input('is_visible');

        # обновление основной записи
        $result = $item->update($validatedData);

        # после обновления
        $response = array(
            'result' => array(
                'status' => $result,
                'item' => Category::where('id', $item->id)->get(),
                'items' => Category::latest()->where('parent_id', 0)->with(['childs'])->get(),
            ),
        );
        return $response;
    }

    /**
     * Update sort of items.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function sort(Request $request)
    {
        $items = $request->all();
        foreach ($items as $key => $value) {
            $items[$key]['sort'] = $key;
            # найдем в БД выбранный элемент меню
            $item = Category::where('id', $items[$key]['id'])->first();
            $item['sort'] = $items[$key]['sort'];
            $item->save();
        }
        $response = array(
            'result' => array(
                'status' => 1,
                'itemList' => Category::latest()->where('parent_id', 0)->with(['childs'])->get(),
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
            
            # найдем удаляемый объект
            $result = Category::find($id)->delete();
            
            $response = array(
                'result' => array(
                    'status' => $result,
                    'category' => Category::latest()->where('parent_id', 0)->with(['childs'])->get(),
                    'items' => Item::latest()->with(['category'])->get(),
                ),
            );
        }else{
            # массовое удаление
            $result = array();
            foreach ( Category::whereIn('id', json_decode($request->input('ids'), 1) )->get() as $key => $value ) {
                $result[] = $value->delete();
            }
 
            $response = array(
                'result' => array(
                    'status' => !in_array(!1, $result),
                    'category' => Category::latest()->where('parent_id', 0)->with(['childs'])->get(),
                    'items' => Item::latest()->with(['category'])->get(),
                ),
            );
        }
        return $response;
    }

}
