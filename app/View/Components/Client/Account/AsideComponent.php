<?php

namespace App\View\Components\Client\Account;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Route;


class AsideComponent extends Component
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
        # о странице
        $route = Route::current();
        // $this->uri = $route->uri;
        # определяем view
        switch ($viewname) {
            case 'account.blog.create':
                $this->view = 'components.client.account.blog.aside-component';
                $this->ul = [
                    [
                        "uri" => "account/blog",
                        "href" => "/account/blog/",
                        "is_active" => null,
                        "a" => "Список",
                        "small" => "Список всех записей блога"
                    ],
                    [
                        "uri" => "account/blog/create",
                        "href" => "/account/blog/create/",
                        "is_active" => null,
                        "a" => "Добавить",
                        "small" => "Добавить новую запись"
                    ]
                ];
                # поиск активного элемента
                foreach ($this->ul as $key_ul => $value_ul) 
                {
                    if ( $value_ul['uri'] != $route->uri ) continue;
                    $this->ul[$key_ul]['is_active'] = 1;
                }
            break;
            case 'account.blog.edit':
                $this->view = 'components.client.account.blog.aside-component';
                $this->ul = [
                    [
                        "uri" => "account/blog",
                        "href" => "/account/blog/",
                        "is_active" => null,
                        "a" => "Список",
                        "small" => "Список всех записей блога"
                    ],
                    [
                        "uri" => "account/blog/create",
                        "href" => "/account/blog/create/",
                        "is_active" => null,
                        "a" => "Добавить",
                        "small" => "Добавить новую запись"
                    ],
                    [
                        "uri" => "account/blog/{blog}",
                        "href" => null,
                        "is_active" => null,
                        "a" => "Редактирование",
                        "small" => "Редактирование записи"
                    ]
                ];
                # поиск активного элемента
                foreach ($this->ul as $key_ul => $value_ul) 
                {
                    if ( $value_ul['uri'] != $route->uri ) continue;
                    $this->ul[$key_ul]['is_active'] = 1;
                }
            break;
            case 'account.blog':
                $this->view = 'components.client.account.blog.aside-component';
                $this->ul = [
                    [
                        "uri" => "account/blog",
                        "href" => "/account/blog/",
                        "is_active" => null,
                        "a" => "Список",
                        "small" => "Список всех записей блога"
                    ],
                    [
                        "uri" => "account/blog/create",
                        "href" => "/account/blog/create/",
                        "is_active" => null,
                        "a" => "Добавить",
                        "small" => "Добавить новую запись"
                    ],
                ];
                # поиск активного элемента
                foreach ($this->ul as $key_ul => $value_ul) 
                {
                    if ( $value_ul['uri'] != $route->uri ) continue;
                    $this->ul[$key_ul]['is_active'] = 1;
                }

            break;
            case 'account.shop':
                $this->view = 'components.client.account.shop.aside-component';
                $this->ul = [
                    [
                        "uri" => "account/shop",
                        "href" => "/account/shop/",
                        "is_active" => null,
                        "a" => "Список",
                        "small" => "Список всех записей блога"
                    ],
                    [
                        "uri" => "account/shop/create",
                        "href" => "/account/shop/create/",
                        "is_active" => null,
                        "a" => "Добавить",
                        "small" => "Добавить новую запись"
                    ]
                ];
                # поиск активного элемента
                foreach ($this->ul as $key_ul => $value_ul) 
                {
                    if ( $value_ul['uri'] != $route->uri ) continue;
                    $this->ul[$key_ul]['is_active'] = 1;
                }

            break;
            case 'account.profile':
                $this->view = 'components.client.account.profile.aside-component';
                
                $this->ul = [
                    [
                        "uri" => "account/shop",
                        "href" => "/account/shop/",
                        "is_active" => null,
                        "a" => "Список",
                        "small" => "Список всех записей блога"
                    ],
                    [
                        "uri" => "account/shop/create",
                        "href" => "/account/shop/create/",
                        "is_active" => null,
                        "a" => "Добавить",
                        "small" => "Добавить новую запись"
                    ]
                ];
                # поиск активного элемента
                foreach ($this->ul as $key_ul => $value_ul) 
                {
                    if ( $value_ul['uri'] != $route->uri ) continue;
                    $this->ul[$key_ul]['is_active'] = 1;
                }

            break;
            default:
                dd(__METHOD__, "view not defined");
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
            "ul" => $this->ul
        ]);
    }
}
