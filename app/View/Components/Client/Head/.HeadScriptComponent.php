<?php

namespace App\View\Components\Client\Head;

use Illuminate\View\Component;


class HeadScriptComponent extends Component
{

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->view = 'components.client.template.head.head-script-component';
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view($this->view);
    }
}
