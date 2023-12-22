<?php

namespace App\Http\Controllers\Client\Domoi\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Domoi\Client\Catalog\Catalog;
use App\Models\Domoi\Client\Catalog\Category;

use Illuminate\Support\Facades\Route;

class FilterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        # если запрос на получение элементов с учетом фильтра
        $is_filter = null;
        $filter_request = array(
            $request->input('price0'), 
            $request->input('price1'), 
            $request->input('area0'), 
            $request->input('area1'), 
            $request->input('district'), 
            $request->input('deadline'), 
        );
        $where = array();

        if( in_array(!null, $filter_request) ){
            # это запрос фильтра
            $is_filter = 1;
            $where = array();
            $where[] = array('price', '>=', $request->input('price0'));
            $where[] = array('price', '<=', $request->input('price1'));
            $where[] = array('area', '>=', $request->input('area0'));
            $where[] = array('area', '<=', $request->input('area1'));
            if( !is_null($request->input('district')) && $request->input('district') != "-" ){
                # только если выбрано
                $where[] = array('location', '=', $request->input('district'));
            }
            if( !is_null($request->input('deadline')) && $request->input('deadline') != "-" ){
                # только если выбрано
                if( $request->input('deadline') == "Сдан" ){
                    # только если выбрано что объект сдан
                    $where[] = array('complete_outdate', '!=', NULL);
                }else{
                    $where[] = array('complete_date_f', '=', $request->input('deadline'));
                }
            }
        }



        # если запрос на получение элементов в пагинации
        $limit = 12;
        $offset = !is_null($request->input('start')) ? $request->input('start') : 0;

        if( is_null($is_filter) ){
            $slug = null;
            $category = null;
            $map = null;

            # ищем в url вторую часть - slug
            if( count(array_filter(explode("/", $request->input('slug')))) == 2 ){
                $slug = array_filter(explode("/", $request->input('slug')))[2];
            }

            # получение объектов определеной категории
            if( !is_null($slug) ){
                $category = Category::where('slug', $slug)->first();
            }
           
            $where[] = array('is_visible', '=', 1);

            # запрос каталога - или в категории или без

            if( is_null($category) ){
                

                $catalog = Catalog::latest()->with(['characteristics', 'category', 'gallery', 'lots'])->whereNotNull('adress_coords')->where($where)->offset($offset)->limit($limit)->get();
                $map = Catalog::latest()->with(['characteristics', 'category', 'gallery', 'lots'])->whereNotNull('adress_coords')->where($where)->get();

                # dd(__METHOD__, 1, $where, $limit, $map, $catalog);
            
            }else{
                # добавим категорию каталога в условие
                $where[] = array('category_id', '=', $category->id);
                
                # dd(__METHOD__, 2, $where, $limit);

                $catalog = Catalog::latest()->with(['characteristics', 'category', 'gallery', 'lots'])->where($where)->whereNotNull('adress_coords')->offset($offset)->limit($limit)->get();
                $map = Catalog::latest()->with(['characteristics', 'category', 'gallery', 'lots'])->where($where)->whereNotNull('adress_coords')->get();
            }
            # сортировка
            foreach ($catalog as $key => $value) {
                # $catalog[$key]->gallery = $catalog[$key]->gallery->sortBy('sort');
                # $catalog[$key]->gallery = $catalog[$key]->gallery->sortByDesc('sort');
                # $catalog[$key]->gallery = $catalog[$key]->gallery->values();
            }

            $response = array(
                'catalog' => $map,
                'count_map' => $map->count(),
                'items' => $catalog,
                'count' => $catalog->count(),
            );
        }else{
            
            # 11.01.2021 - формируем $where с учетом видимости объекта
            $where[] = array('is_visible', '=', 1);

            $catalog = Catalog::latest()->with(['characteristics', 'category', 'gallery', 'lots'])->whereNotNull('adress_coords')->where($where)->offset($offset)->limit($limit)->get();
            $map = Catalog::latest()->with(['characteristics', 'category', 'gallery', 'lots'])->whereNotNull('adress_coords')->where($where)->get();
            $response = array(
                // 'catalog' => Catalog::latest()->with(['characteristics', 'category', 'gallery', 'lots'])->where($where)->offset($offset)->limit($limit + $offset)->get(),
                'map' => $map,
                'items' => $catalog,
                'count' => $map->count(),
            );
            # dd(__METHOD__, $catalog->count());
            
            # dd(__METHOD__, 3, $where, $limit, $offset, $response['items']);

        }
        return response()->json($response);
    }


}
