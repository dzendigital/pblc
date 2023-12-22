<?php

namespace App\View\Components\Client\Account\Shop;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Route;


class IndexComponent extends Component
{

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
    public $shop = [];

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($items = [])
    {
        $this->view = 'components.client.account.shop.index-component';
                
        $this->shop = $items;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view($this->view, [
            "shop" => $this->shop,
        ]);
    }
}
