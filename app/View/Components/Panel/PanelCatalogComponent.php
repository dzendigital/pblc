<?php
namespace App\View\Components\Panel;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Route;

class PanelCatalogComponent extends Component
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

        if( auth()->user()->roles()->first()->name == 'Admin'){
            $this->catalog = $this->avaliableModules();
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
        return view('components.panel.template.nav.panel-catalog-component', [
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
            'title' => 'Главная страница',
            'link'  => "component/index-page",
        );
        $catalog[] = array(
            'title' => 'Направления',
            'link'  => "component/directions",
        );
        $catalog[] = array(
            'title' => 'Транспорт',
            'link'  => "component/transport",
        );
        $catalog[] = array(
            'title' => 'Отзывы',
            'link'  => "component/feedback",
        );
        $catalog[] = array(
            'title' => 'Пользователи',
            'link'  => "component/users",
        );
        $catalog[] = array(
            'title' => 'Меню и контент страница',
            'link'  => "component/page",
        );
        $catalog[] = array(
            'title' => 'FAQ - вопросы',
            'link'  => "component/faq",
        );

        return $catalog;
    }
}
