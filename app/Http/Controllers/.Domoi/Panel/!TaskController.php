<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tasks;
use Carbon\Carbon;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Tasks $tasks)
    {
        return view('component', [
            'component' => 'panel.task-component', 
            'items' => $tasks->all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->only(['title', 'body', 'value', 'visible_at', 'visible_period']);
        $input['visible_at'] = is_null($input['visible_at']) ? Carbon::now() : $input['visible_at'];
        $task = new Tasks($input);
        $result = $task->save();
        $response = array(
            'result' => array(
                'status' => $result,
                'item' => $task,
            ),
        );
        
        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->only(['title', 'body', 'value', 'visible_at', 'visible_period']);
        $task = Tasks::findOrFail($id);
        $task->title = $input['title'];
        $task->body = $input['body'];
        $task->value = $input['value'];
        $task->visible_at = $input['visible_at'];
        $task->visible_period = $input['visible_period'];
        $result = $task->save();
        $response = array(
            'result' => array(
                'status' => $result,
                'item' => $task,
            ),
        );
        return $response;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = Tasks::find($id)->delete();
        $response = array(
            'result' => array(
                'status' => $result,
            ),
        );
        
        return $response;
    }
}
