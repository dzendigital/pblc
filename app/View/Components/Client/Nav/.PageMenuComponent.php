<?php

namespace App\View\Components\Client\Nav;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Route;

# use App\Models\Page;
use App\Models\Menu;

class PageMenuComponent extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        dd(__METHOD__, 1);
        $route = Route::current();
        $this->view = 'components.client.template.nav.page-menu-component';

        # фикс маршруты 
        $this->menu = array(
            array(
                'title' => 'Home',
                'path'  => '',
                'path_href'  => array(''),
                'is_active' => null,
            ),
            array(
                'title' => 'Destinations',
                'path'  => 'destinations',
                'path_href'  => array('destinations'),
                'is_active' => null,
            ),
            array(
                'title' => 'Booking',
                'path'  => 'destinations-search',
                'path_href'  => array('destinations-search'),
                'is_active' => null,
            ),
            array(
                'title' => 'Feedback',
                'path'  => 'feedback',
                'path_href'  => array('feedback'),
                'is_active' => null,
            ),
        );

        # страницы из ПУ
        $pages = Menu::where('parent_id', 0)->latest()->with(['pages', 'childs.pages'])->doesntHave('city')->get()->toArray();
        
        # соединяем
        $this->menu = array_merge($this->menu, $pages);

        foreach ( $this->menu as $key => $value) {
            if ( isset($value['path_href']) ) {
                # если идем по фикс
                if( in_array($route->uri, $value['path_href']) ){
                    $this->menu[$key]['is_active'] = 1;
                }
            }else{
                # если идем страницам из ПУ
                $this->menu[$key]['is_active'] = !1;
                $this->menu[$key]['path'] = $this->menu[$key]['slug'];
                if ( $route->url == $value['slug'] ) {
                    $this->menu[$key]['is_active'] = 1;
                }
                if ( isset($value['childs']) && !empty($value['childs']) ) {
                    foreach ( $value['childs'] as $k => $v) {
                        $this->menu[$key]['childs'][$k]['is_active'] = !1;
                        $this->menu[$key]['childs'][$k]['path'] = $v['slug'];
                        // dd(__METHOD__, $route->url, $value['path_href']);
                        if ( $route->url == $v['slug'] ) {
                            $this->menu[$key]['childs'][$k]['is_active'] = 1;
                        }   
                    }
                }
            }
        }

        // dd(__METHOD__, $this->menu, $route->url, $route);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view($this->view, [
            'menu' => $this->menu,
        ]);
    }
}
