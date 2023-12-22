<?php

namespace App\Http\Controllers\Panel\Page;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Page\Menu;
use App\Models\Page\Page;

use App\Http\Requests\Panel\MenuRequest;

use Illuminate\Support\Str;

class MenuController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MenuRequest $request)
    {
        # валидация входящих полей
        $validatedData = $request->validated();
        # все страницы, создаваемые админом - упраляемые
        $validatedData['is_managable'] = 1;
        # создание объекта с данными
        $item = new Menu($validatedData);
        
        # проверка slug на: присутствие и уникальность
        if( is_null($item->slug) && is_null($request->input('slug')) ){
            $title = $request->input('title');
            $item->slug = Str::slug($title, '-');
        }
        # сохранение объекта
        $result = $item->save();
        if( $result ){
            # создание страницы, привязка к пункту меню и сохранение
            $page = new Page();
            $page->meta_title = $item->title;
            $item->pages()->save($page);
        }
        # после создания и сохранения отношений - запросим новосозданный объект и вернем его для добавления во view
        $response = array(
            'result' => array(
                'status' => $result,
                'item' => Menu::where('id', $item->id)->with(['pages', 'childs.pages'])->first(),
                'itemList' => Menu::where('parent_id', 0)->latest()->with(['pages', 'childs.pages'])->get(),
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
    public function update(MenuRequest $request, $id)
    {
        # если в MenuRequest установить unique:pages_menu - при обновлении текущая запись тоже учитывается, а не должна.
        $validatedData = $request->validated();
        // $item = Menu::where("title", $validatedData['title'])->orWhere("slug", $validatedData['slug'])->where("id", '!=', $request->input('id'))->get();
        # если в БД есть запись в которой совпадают title и id || slug и id - это дубль и запрос не должен пройти валидацию
        $item = Menu::where([
           ["title", '=', $validatedData['title']],
           ["id", '!=', $request->input('id')]
        ])->Orwhere([
           ["slug", '=', $validatedData['slug']],
           ["id", '!=', $request->input('id')]
        ])->get();
        if( $item->count() ){
            # валидация не пройдена
            return response()->json([
                "message" => "The given data was invalid.",
                'errors' => array(
                    "custom_error" => array("Поля 'Наименование' и 'URL' должно быть уникальным."),
                ),
            ], 422);
        }
        $item = Menu::find($id);
        # обновление основной записи
        if( is_null($validatedData['slug']) ){
            # если не установлен slug - сгенерируем автоматически
            $validatedData['slug'] = Str::slug($validatedData['title'], '-');
        }
        $result = $item->update( $validatedData );
        
        $response = array(
            'result' => array(
                'status' => $result,
                'itemList' => Menu::where('parent_id', 0)->latest()->with(['pages', 'childs.pages'])->get(),
            ),
        );
        return $response;
    }

    /**
     * Update sort of menus.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function sort(Request $request)
    {
        $menu = $request->all();
        foreach ($menu as $key => $value) {
            if( !empty($value['childs']) ){
                foreach ($value['childs'] as $k => $v) {
                    $menu[$key]['childs'][$k]['sort'] = $k; 
                    
                    # найдем в БД выбранный подэлемент меню и сохраним
                    $submenu = Menu::where('id', $menu[$key]['childs'][$k]['id'])->first();
                    $submenu['sort'] = $menu[$key]['childs'][$k]['sort'];
                    $submenu->save();
                }
            }
            $menu[$key]['sort'] = $key;
            # найдем в БД выбранный элемент меню
            $item = Menu::where('id', $menu[$key]['id'])->with(['pages', 'childs.pages'])->first();
            $item['sort'] = $menu[$key]['sort'];
            $item->save();
        }
        $response = array(
            'result' => array(
                'status' => 1,
                'itemList' => Menu::where('parent_id', 0)->latest()->with(['pages', 'childs.pages'])->get(),
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
        $log = array(
            'menu' => array(),
            'pages' => array(),
        );
        $item = Menu::where('id', $id)->with('pages', 'childs.pages')->get();
        foreach ($item as $key => $value) {
            if ( !is_null($value->pages) ) {
                $log['pages'][] = !is_null($value->pages->meta_title) ? $value->pages->meta_title : $value->pages->id;
                $value->pages->delete();
            }
            if ( !is_null($value->childs) ) {
                foreach ($value->childs as $k => $v) {
                    $log['menu'][] = $v->title;
                    $v->delete();
                }
            }
        }
        $log['menu'][] = $item->first()->title;
        $result = $item->first->delete();   
        $response = array(
            'result' => array(
                'status' => $result,
                'log' => $log,
                'itemList' =>  Menu::where('parent_id', 0)->latest()->with(['pages', 'childs.pages'])->get(), # в ответе отправляем полное меню для перерисовки во view
            ),
        );
        
        return $response;
    }
}
