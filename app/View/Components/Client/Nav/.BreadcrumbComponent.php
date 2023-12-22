<?php

namespace App\View\Components\Client\Nav;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Route;

# use App\Models\Page;
use App\Models\Menu;

class BreadcrumbComponent extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {

        $route = Route::current();
        $this->view = 'components.client.nav.breadcrumb-component';
        
        # фикс маршруты 
        $this->menu = array(
            array(
                "title" => 'Главная страница',
                "href" => "",
            )
        );

        # данные открытой страницы
        $where = array();
        // $where[] = array("parent_id", "=", "0");
        // $where[] = array("is_visible", "=", "1");
        $where[] = array("slug", "=", $route->uri);
        $item = Menu::where($where)->with(['parent'])->first();
        
        if ( !is_null($item) ) {
            
            $parent = $item->parent()->first();

            if ( !is_null($parent) ) {
                $this->menu[] = array(
                    "title" => $parent->title,
                    "href" => $parent->slug,
                );
            }
            
            $this->menu[] = array(
                "title" => $item->title,
                "href" => null,
            );
            
        }else{
            $this->menu[] = array(
                "title" => 'Сайт yapodbor',
                "href" => null,
            );
        }
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
