<?php
namespace App\View\Components\Panel\Nav;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Route;

class NavComponent extends Component
{
    /*
     * Список доступных модулей
     */
    public $catalog = null;
    
    /*
     * Активная страница
     */
    public $uri = "Панель управления";
    
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->catalog = array(
            array(
                'title' => 'Панель управления',
                'link'  => "admin",
            ),
        );

        # для root пользователя в интерфейсе Панели управления доступны все модули
        # в зависимости от роли (или permissions) подгружаем нужные модули

        if( auth()->user()->roles()->first()->slug == 'admin'){
            $this->catalog = $this->avaliableModules();
        }
        if( auth()->user()->roles()->first()->slug == 'manager'){
            $this->catalog = $this->managerModules();
        }
        
        $route = Route::current();

        foreach ($this->catalog as $key => $value) {
            if( $value['link'] == $route->uri ){
                $this->uri = $value['title'];
            }
            continue;
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.panel.nav.nav-component', [
            'catalog' => $this->catalog,
            'uri' => $this->uri,
        ]);
    }

    /**
     * Get list of availiable admin modules
     *
     * @return array()
     */
    public function avaliableModules()
    {
        $catalog = array();
        $catalog[] = array(
            'title' => 'Анкета',
            'link'  => "component/docs",
        );
        $catalog[] = array(
            'title' => 'Пользователи',
            'link'  => "component/users",
        );
        $catalog[] = array(
            'title' => 'Страницы',
            'link'  => "component/page",
        );
        $catalog[] = array(
            'title' => 'Настройки сайта',
            'link'  => "component/settings",
        );
        if ( !1 ) 
        {
            // code...
            $catalog[] = array(
                'title' => 'Аккаунты пользователей',
                'link'  => "component/account",
            );
            $catalog[] = array(
                'title' => 'База автомобилей',
                'link'  => "component/baseauto",
            );
            $catalog[] = array(
                'title' => 'Договоры для специалистов',
                'link'  => "component/specialistdocs",
            );
            $catalog[] = array(
                'title' => 'Блог',
                'link'  => "component/blog",
            );
            $catalog[] = array(
                'title' => 'Влог',
                'link'  => "component/vlog",
            );
            $catalog[] = array(
                'title' => 'Отзывы платформы',
                'link'  => "component/reviews",
            );
            $catalog[] = array(
                'title' => 'Меню и контент страница',
                'link'  => "component/page",
            );
            $catalog[] = array(
                'title' => 'Платежная система',
                'link'  => "component/payment",
            );
        }
      

        return $catalog;
    }
    /**
     * Get list of availiable manager modules
     *
     * @return array()
     */
    public function managerModules()
    {
        $catalog = array();
        $catalog[] = array(
            'title' => 'База автомобилей',
            'link'  => "component/baseauto",
        );
        $catalog[] = array(
            'title' => 'Блог',
            'link'  => "component/blog",
        );
        $catalog[] = array(
            'title' => 'Отзывы',
            'link'  => "component/reviews",
        );
        $catalog[] = array(
            'title' => 'Аккаунты пользователей',
            'link'  => "component/account",
        );
        $catalog[] = array(
            'title' => 'Влог',
            'link'  => "component/vlog",
        );


        return $catalog;
    }
}
