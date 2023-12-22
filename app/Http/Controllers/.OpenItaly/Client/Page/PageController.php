<?php

namespace App\Http\Controllers\Client\Page;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Page;
use App\Models\Menu;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * Ищет созданную в ПУ страницу
     * если страница есть: возвращает контент и загружает view
     * если страницы нет: возвращает 404
     *
     * @return \Illuminate\Http\Response
     */
    public function view(Menu $menu, Page $page, $url)
    {

        # проверяем, есть ли в БД информация по страницы с запрашиваемым url
        $menu = Menu::all();
        $item = Menu::where([
            ["slug", '=', $url],
            ["is_visible", '=', '1'],
        ])->with(['pages'])->first();
        
        if( is_null($item) || !$item->count() ){
            # страница не найдена - редирект 404
            abort(404);
        }
        if( is_null($item->pages) || !$item->pages->count() ){
            # страница не найдена - редирект 404
            abort(404);
        }

        return view("client/pages/index", [
            'item' => $item,
        ]);
    }
}
