<?php

namespace App\Http\Controllers\Client\Platform;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\Client\Task\ItemRequest;

use App\Repositories\SpecialistRepository;

use App\Models\Gallery\Gallery;
use App\Models\Task\Task;
use App\Models\User\Item as User;

class FormController extends Controller
{

    private SpecialistRepository $itemRepository;

    public function __construct(SpecialistRepository $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }
    /**
     * Display a listing of the resource.
     * @param string $slug
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response = array();
        return view( "client/platform/task/index", $response );
    }
     /**
     * In this context method store use for: create task
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ItemRequest $request)
    {
        $validated = $request->validated();

        $gallery_validated = $request->input('gallery');

        $user = User::where("user_id", auth()->user()->id)->first();
        
        $task = new Task($validated);

        $task->user_id = $user->id;

        $result = $task->save();

        if ( $gallery_validated != null ) {
            foreach ( $gallery_validated as $key => $value ) {
                if( !isset($value['id']) ){
                    # в базе нет (id в запросе отсутствует) - добавляем фото
                    $gallery_item = new Gallery($value);
                    $gallery_item->save();

                    # сохраняем
                    $task->gallery()->save($gallery_item);   
                }
            }
        }

        $response = array(
            "status" => $result,
        );
        return $response;
    }
}
