<?php
namespace App\View\Components\Panel\Head;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Route;

class HeadComponent extends Component
{

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.panel.head.head-component', []);
    }
}
