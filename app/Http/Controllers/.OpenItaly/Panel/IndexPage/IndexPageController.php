<?php

namespace App\Http\Controllers\Panel\IndexPage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\IndexPage\IndexPage;
use App\Models\Gallery\Gallery;
use App\Models\Presets\Presets;


class IndexPageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response = array();
        $response['page'] = IndexPage::latest()->with(['presets'])->first();  
        return view("panel/index-page/index", $response);
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

        # валидация входящих полей
        $validatedData = $request->validate([
            "body" => "",

            "meta_title" => "",
            "meta_description" => "",
            "meta_keywords" => "",
        ]);

        # найдем в БД запрашиваемую запись
        $item = IndexPage::findOrFail($id);
        

        # удалим старые привязки к пресет (preset)

        # в объекте есть пресеты:
        if ( $request->input('presets') != null ) {
            $item->presets()->delete();
            
            # добавляем к $item ссылку на gallery && сохраняем relation gallery
            foreach ( $request->input("presets") as $key => $value ) {
                if( !isset($value['id']) ){
                    # в базе нет (id в запросе отсутствует) - добавляем фото
                    $preset = new Presets($value);

                    $preset->catalog_index_page_id = $item->id;
                    // $preset->catalog_item_id = 1;

                    # сохраняем
                    $item->presets()->save($preset);
                    
                }else{
                    # уже в базе
                    $preset = Presets::withTrashed()->find($value['id']);
                    
                    $preset['sort'] = $key;
                    $preset['content'] = $value['content'];
                    $preset['list'] = $value['list'];
                    
                    // $preset->save();
                    $preset->restore();
                }
            }

            # обновим привязку
            # $item->is_active_gallery = 1;
            $item->save();
        }

        # обновим основную запись
        $result = $item->update($validatedData);

        # после обновления
        $response = array(
            'result' => array(
                'status' => $result,
                'item' => IndexPage::latest()->with(['presets'])->first(),
            ),
        );
        return $response;
    }

}
