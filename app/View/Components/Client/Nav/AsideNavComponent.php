<?php

namespace App\View\Components\Client\Nav;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Route;

use App\Models\Menu;
use App\Models\User as RootUser;

class AsideNavComponent extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $route = Route::current();
        $this->view = 'components.client.nav.aside-nav-component';
            
        /**
         * запрос пользователя 
         */
        $menu_auth = [
            [
                "title" => 'Авторам',
                "href" => "/account",
                "slug" => "account",
            ],
            [
                "title" => 'Блоги',
                "href" => "/blog",
                "slug" => "blog",
            ],
        ];
        $this->_users = null;
        $this->is_password_verified_at = null;
        if ( !is_null(auth()->user()) ) 
        {
            $this->_users = RootUser::where("id", auth()->user()->id)->first();
            if ( !is_null($this->_users) )
            {
                # проверка подтверждения аккаунта
                if ( !is_null(@$this->_users->password_verified_at) ) 
                {
                    $this->is_password_verified_at = $this->_users->password_verified_at->format('d.m.Y h:i:s');
                } 
                # добавление ссылки на лк
                if ( auth()->user()->roles[0]->slug == 'account') 
                {
                    $menu_auth = [
                        [
                            "title" => 'Аккаунт',
                            "href" => "/account/profile",
                            "slug" => "account/profile",
                        ],
                        [
                            "title" => 'Раздел блог',
                            "href" => "/account/blog",
                            "slug" => "account/blog",
                        ],
                        // [
                        //     "title" => 'Мой магазин',
                        //     "href" => "/account/shop",
                        //     "slug" => "account/shop",
                        // ],
                    ];

                }
            }
        }
        # фикс маршруты 
        $this->menu = array(
            array(
                "title" => 'Главная',
                "href" => "/",
                "slug" => "",
            ),
            array(
                "title" => 'О проекте',
                "href" => "/",
                "slug" => "",
            )
        );
        # 14/08/23 отключили маршруты
        $this->menu = [];
        $this->menu = array_merge($this->menu, $menu_auth);
        foreach ($this->menu as $key => $value) {
            $this->menu[$key]["is_active"] = null;
            
            if ( empty($value['slug']) ) continue;

            if ( $value["slug"] == $route->uri || (strpos($route->uri, $value['slug']) !== false)) 
            {
                $this->menu[$key]["is_active"] = 1;
            }
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
            'is_password_verified_at' => $this->is_password_verified_at,
        ]);
    }
}
