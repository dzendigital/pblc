<?php

namespace App\View\Components\Client\Head;

use Illuminate\View\Component;

use Illuminate\Support\Facades\Route;

use App\Models\Page;
use App\Models\Menu;
use App\Models\Service\Item as Service;
use App\Models\Portfolio\Item as Portfolio;
use App\Models\Baseauto\Item as Auto;
use App\Models\Blog\Item as Blog;
use App\Models\Vlog\Item as Vlog;
use App\Models\Task\Item as Task;
use App\Models\Specialist\Item as Specialist;

class HeadPageComponent extends Component
{

    /**
    * The current path.
    *
    * @var string
    */
   public $uri;
   
     /**
     * The view template
     *
     * @var string
     */
     public $view;
    /**
     * The meta data of the page.
     *
     * @var string
     */
    public $meta = array(
        'title' => "",
        'description' => "",
        'kaywords' => "",
        'h1' => "",
    );

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($viewname = null)
    {
        $route = Route::current();
        # главная страница
        $this->uri = $route->uri;
        if ( $this->uri == "{url}" ) {
            $this->uri = $route->parameters['url'];
        }
        switch ($this->uri) {
            case 'account/profile':
                $this->meta = array(
                    'title' => "Личный кабинет автора / ebbandgrow.ru",
                    'description' => "Личный кабинет автора на сайте ebbandgrow.ru",
                    'keywords' => "Личный кабинет автора, ebbandgrow.ru",
                    'h1' => "Личный кабинет автора",
                );
            break;   
            case 'account/blog':
                $this->meta = array(
                    'title' => "Управление блогом / ebbandgrow.ru",
                    'description' => "Управление блогом автора на сайте ebbandgrow.ru",
                    'keywords' => "Блог автора, ebbandgrow.ru",
                    'h1' => "Блог автора",
                );
            break;            
            default:
                $this->meta = array(
                    'title' => "ebbandgrow.ru",
                    'description' => "ebbandgrow.ru",
                    'keywords' => "ebbandgrow.ru",
                    'h1' => "ebbandgrow.ru",
                );
            break;
        }
        # определяем view
        switch ($viewname) {
            case 'head-account-component':
                $this->view = 'components.client.head.head-account-component';
            break;
            default:
                $this->view = 'components.client.head.head-index-component';
            break;
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
            'meta' => $this->meta,
        ]);
    }
}
