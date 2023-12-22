<?php

namespace App\Http\Controllers\Client\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Page;
use App\Models\Menu;

class BlogController extends Controller
{
    /**
     * 
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pages_plucked = array();
        $pages = Menu::where('parent_id', 0)->latest()->with(['pages', 'childs.pages'])->whereHas('city')->get()->toArray();
        
        foreach ($pages as $key => $value) {
            $pages_plucked[] = $value;
            if ( isset($value['childs']) && !empty($value['childs']) ) {
                foreach ( $value['childs'] as $k => $v ) {
                    $pages_plucked[] = $v;
                }
            }
        }

        return view("/client/blog/index", [
            "pages" => $pages_plucked,
            "pages_raw" => $pages,
            "is_active_menu_slug" => "blog",
        ]);
    }

    /**
     * Display the search results
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $slug)
    {
        $pages_plucked = array();
        $pages = Menu::where('slug', $slug)->latest()->with(['pages', 'childs.pages'])->whereHas('city')->get()->toArray();
        
        foreach ($pages as $key => $value) {
            $pages_plucked[] = $value;
            if ( isset($value['childs']) && !empty($value['childs']) ) {
                foreach ( $value['childs'] as $k => $v ) {
                    $pages_plucked[] = $v;
                }
            }
        }
        
        return view("/client/blog/index", [
            "pages" => $pages_plucked,
            "is_active_menu_slug" => $slug,
        ]);
    }
    /**
     * Display the search results
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function blogpost($slug)
    {
        $pages_plucked = array();
        $pages = Menu::where('slug', $slug)->with(['pages'])->firstOrFail();
        
        # если элемент является подменю, то найдем родительский элемент
        $parent = Menu::where('id', $pages->parent_id)->first();

        # назначим $slug с учетом родительского элемента
        $slug = is_null($parent) ? $slug : $parent->slug; 


        $pages = $pages->toArray();

        return view("/client/blog/show", [
            "pages" => $pages,
            "is_active_menu_slug" => $slug,
        ]);
    }
}
