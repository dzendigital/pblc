<?php

namespace App\View\Components\Client\Blog;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Route;


class GalleryComponent extends Component
{

     /**
     * The view template
     *
     * @var string
     */
     public $view;
    /**
     * @var string
     */
    public $blog = [];
    public $gallery = [];

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($gallery = [])
    {                
        $this->gallery = $gallery;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view("components.client.account.blog.gallery-component", [
            "gallery" => $this->gallery
        ]);
    }
}
