<?php

namespace App\Http\Controllers\Panel\Specialist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Repositories\Panel\SpecialistDocumentRepository as ItemRepository;

# работа с файлами
use Illuminate\Support\Facades\Storage;

use App\Models\Specialist\Document;

class SpecialistDocumentController extends Controller
{
    private ItemRepository $itemRepository;

    public function __construct(ItemRepository $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response = [
            'items' => $this->itemRepository->all(),
        ];

        return view("panel/specialistdocs/index", $response);
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $item = $this->itemRepository->byId($id);

        # dd(__METHOD__, $item);
        # сохранение файлов: report
        if ( $request->input('document') != null ) {
            # добавляем к $item ссылку на gallery && сохраняем relation gallery
            foreach ( $request->input("document") as $key => $value ) {
                if( !isset($value['id']) ){
                    # в базе нет (id в запросе отсутствует) - добавляем
                    $tmp = new Document($value);

                    # сохраняем
                    $item->document()->save($tmp);   
                }
            }
        }

        $response = array(
            'result' => array(
                'status' => 1,
                'items' => $this->itemRepository->all(),
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

        # файл найден в базе
        $item = Document::find($id);
        
        # проверка что файл существует
        $is_exists = is_null($item) ? null : Storage::exists($item->url);

        if( $is_exists ){
            # если файл существует - удалить
            $result_delete = Storage::delete($item->url);
            $result_delete_item = is_null($item) ? null : $item->delete();
        }else{
            # production
            $result_delete = null;
            # если запись существует - удалить
            $result_delete_item = is_null($item) ? null : $item->delete();       
        }

        $response = array(
            'result' => array(
                'items' => $this->itemRepository->all(),
            ),
        );
        
        return $response;
    }
}
