<?php

namespace App\Http\Controllers\Panel\City;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\City\City;
use App\Models\Directions\Directions;
use App\Models\Gallery\Gallery;

use App\Models\Menu;
use App\Models\Page;

use App\Http\Requests\Panel\City\CityRequest;

use Illuminate\Support\Str;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        dd(__METHOD__);
        return view( config('app.project') . ".panel/lectures/index", [
            'items' => Lectures::latest()->with(['gallery', 'video'])->get(),
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CityRequest $request)
    {   

        # валидация входящих полей
        $validatedData = $request->validated();

        # создание объекта с данными
        $item = new City($validatedData);        

        # выставляем видимость по-умолчанию
        # $item->is_visible = 1;
        
        # сохранение объекта
        $result = $item->save();

        # после создания и сохранения отношений - запросим новосозданный объект и вернем его для добавления во view
        $response = array(
            'result' => array(
                'status' => $result,
                'item' => City::where('id', $item->id)->with(['gallery'])->get(),
                'items' => City::latest()->with(['gallery'])->get(),
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
    public function update(Request $request, $id)
    {

        # валидация входящих полей
        # $validatedData = $request->validated();

        # найдем в БД запрашиваемую запись
        $item = City::findOrFail($id);
        
        # обновим поля: название, url
        $item['title'] = $request->input('title');
        
        $item['meta_title'] = $request->input('meta_title');
        $item['meta_description'] = $request->input('meta_description');
        $item['meta_keywords'] = $request->input('meta_keywords');
        $item['seo_text'] = $request->input('seo_text');
        
        $item['slug'] = $request->input('slug');


        # в городе есть галерея:
        if ( $request->input('gallery') != null ) {
            # добавляем к $item ссылку на gallery && сохраняем relation gallery
            foreach ( $request->input("gallery") as $key => $value ) {
                if( !isset($value['id']) ){
                    # в базе нет (id в запросе отсутствует) - добавляем фото
                    $gallery_item = new Gallery($value);
                    $gallery_item->catalog_city_id = $item->id;

                    # сохраняем
                    $item->gallery()->save($gallery_item);
                    
                }else{
                    # уже в базе
                }
            }

            # обновим привязку
            $item->is_active_gallery = 1;
            # $item->save();

        }
        # в городе выставлен чекбокс - отображать в боковом меню:
        if ( $request->has('is_menu') ) {
            if ( $request->input('is_menu') != null ) {
                # попытка восстановить удаленную ранее страницу с меню
                $menu = Menu::withTrashed()->where("id", $item->is_menu)->first();
                if ( !is_null($menu) ) {
                    if ( !is_null($menu->deleted_at) ) {
                        # восстановление пункта меню если deleted_at заполнен
                        Menu::withTrashed()->where("id", $item->is_menu)->restore();
                        Menu::withTrashed()->where("id", $item->is_menu)->first()->pages()->restore();
                    }
                    # обновление пункта меню если deleted_at не заполнен
                    # получим новые данные
                    $menu = Menu::where("id", $item->is_menu)->first();
                    $menu->title = $item['title'];

                    $menu->save();

                }else{
                    # добавляем пункт меню в модуль “Меню и контент страница”
                    $menu = new Menu(array(
                        "title" => $item->title,
                        "parent_id" => 0,
                        "is_visible" => 0,
                        "is_managable" => 1,
                    ));
                    # сохранение объекта
                    if( $menu->save() ){
                        # создание страницы, привязка к пункту меню и сохранение
                        $page = new Page(array(
                            "body" => "",
                            "meta_title" => $menu->title,
                        ));
                        $menu->pages()->save($page);
                    }            
                    # обновим привязку
                    $item->is_menu = $menu->id;
                }
            }else{
                $menu = $item->menu()->first();
                if ( !empty($menu) ) {
                    # если привязка есть - удаляем пункт меню
                    $menu->delete();
                    # если привязка есть - удаляем страницу
                    $menu->pages()->delete();
                }
                # обновим привязку
                $item->is_menu = NULL;
            }
        }

        # обновление основной записи
        $result = $item->save();
        
        # после обновления
        $response = array(
            'result' => array(
                'status' => $result,
                'item' => City::where('id', $item->id)->with(['gallery'])->get(),
                'items' => City::latest()->with(['gallery'])->get(),
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
            $item = City::where('id', $items[$key]['id'])->first();
            $item['sort'] = $items[$key]['sort'];
            $item->save();
        }
        $response = array(
            'result' => array(
                'status' => 1,
                'itemList' => City::latest()->with(['gallery'])->get(),
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
            $result = City::find($id)->delete();
            
            $response = array(
                'result' => array(
                    'status' => $result,
                    'city' => City::latest()->with(['gallery'])->get(),
                    'items' => Directions::latest()->with(['departure', 'arrival', 'transport.gallery'])->get(),
                ),
            );
        }else{
            # массовое удаление
            $result = array();
            foreach ( City::whereIn('id', json_decode($request->input('ids'), 1) )->get() as $key => $value ) {
                $result[] = $value->delete();
            }
 
            $response = array(
                'result' => array(
                    'status' => !in_array(!1, $result),
                    'city' => City::latest()->with(['gallery'])->get(),
                    'items' => Directions::latest()->with(['departure', 'arrival', 'transport.gallery'])->get(),
                ),
            );
        }
        
        return $response;
    }
}
