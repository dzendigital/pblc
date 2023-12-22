<?php

namespace App\Http\Controllers\Client\Specialist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Specialist\Item as Specialist;
use App\Models\Gallery\Gallery;

use App\Repositories\Client\SpecialistTaskRepository;

class TaskController extends Controller
{

    private SpecialistTaskRepository $itemRepository;

    public function __construct(SpecialistTaskRepository $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    /**
     * Display a listing of the resource.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param string $slug
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        # здесь запрашивать только задачи специалиста
        $response = array(
            # "get" => $this->itemRepository->all(),
            "items" => $this->itemRepository->paginate(),
        );  

        $response["template"]["render"] = view( "client/specialist/task/paginated", $response)->render();

        return view( "client/specialist/task/index", $response);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $slug)
    {
        $item = $this->itemRepository->find($slug);

        $meta = array(
            'meta_title' => $item['meta_title'],
            'meta_description' => $item['meta_description'],
            'meta_keywords' => $item['meta_keywords'],
            'meta_h1' => $item['meta_h1'],
        );

        $specialist = Specialist::where("user_id", auth()->user()->id)->first();

        $response = array(
            'item' => $item,
            'executor' => $this->itemRepository->specialistAccept($item->id, $specialist->id),
            'meta' => $meta,
        );

        return view('client/specialist/task/show', $response);
    }

}
