<?php

namespace App\View\Components\Client\Blog;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Route;

use App\Models\Blog\Item as BlogModel;
use App\Repositories\Client\Account\BlogRepository;

class IndexComponent extends Component
{

    private BlogRepository $blogRepository;

    public function __construct(BlogRepository $blogRepository)
    {
        $this->blogRepository = $blogRepository;

        $this->view = 'components.client.blog.index-component';
        // $this->blog = BlogModel::all();
        $this->blog = $this->blogRepository->indexpaginate();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view($this->view, [
            "view" => $this->view,
            "blog" => $this->blog
        ]);
    }
}
