<?php

namespace App\Http\Controllers\Panel\Feedback;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Feedback\Feedback;

use App\Http\Requests\Panel\Feedback\FeedbackRequest;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("panel/feedback/index", [
            'items' => Feedback::latest()->get(),
        ]);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(FeedbackRequest $request, $id)
    {
        # валидация входящих полей
        $validatedData = $request->validated();
        
        $item = Feedback::findOrFail($id);
        
        # обновление основной записи
        $result = $item->update( $request->validated() );

        $response = array(
            'result' => array(
                'status' => $result,
                'items' => Feedback::latest()->get(),
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
    public function destroy(Request $request, $id)
    {
        if( is_null($request->input('ids')) ){
            $result = Feedback::find($id)->delete();
            $response = array(
                'result' => array(
                    'status' => $result,
                    'items' => Feedback::latest()->get(),
                ),
            );
        }else{
            # массовое удаление
            $result = array();
            foreach ( Feedback::whereIn('id', json_decode($request->input('ids'), 1) )->get() as $key => $value ) {
                $result[] = $value->delete();
            }
            $response = array(
                'result' => array(
                    'status' => !in_array(!1, $result),
                    'items' => Feedback::latest()->get(),
                ),
            );
        }
        
        return $response;
    }
}
