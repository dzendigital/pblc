<?php

namespace App\Http\Controllers\Panel\Vlog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\Panel\Vlog\ItemRequest;
use App\Models\Vlog\Item;
use App\Models\Vlog\Category;
use App\Models\Gallery\Gallery;
use App\Models\Gallery\Video;

use App\Repositories\VlogRepository as ItemRepository;

class ItemController extends Controller
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
        $response = array();
        $items = $this->itemRepository->all();
        $response['items'] = $items['get'];
        $response['category'] = $this->itemRepository->category();
        return view("panel/vlog/index", $response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ItemRequest $request)
    {   

        # валидация входящих полей
        $validatedData = $request->validated();

        # создание объекта с данными
        $item = new Item($validatedData);

        # выставляем видимость по-умолчанию
        $item->is_visible = 1;
        
        # сохранение объекта
        $result = $item->save();

        # в запросе есть галерея:
        if ( $request->input('gallery') != null ) {
            # добавляем к $item ссылку на gallery && сохраняем relation gallery
            foreach ( $request->input("gallery") as $key => $value ) {
                if( !isset($value['id']) ){
                    # в базе нет (id в запросе отсутствует) - добавляем фото
                    $tmp = new Gallery($value);
                    $tmp->save();

                    # сохраняем
                    $item->gallery()->save($tmp);
                    
                }
            }
        }

        # в запросе есть галерея:
        if ( $request->input('video') != null ) {
            # добавляем к $item ссылку на gallery && сохраняем relation gallery
            foreach ( $request->input("video") as $key => $value ) {
                if( !isset($value['id']) ){
                    # в базе нет (id в запросе отсутствует) - добавляем фото
                    $tmp = new Video($value);
                    $tmp->save();

                    # сохраняем
                    $item->video()->save($tmp);
                    
                }
            }
        }


        # после создания и сохранения отношений - запросим новосозданный объект и вернем его для добавления во view
        $items = $this->itemRepository->all();
        $response = array(
            'result' => array(
                'status' => $result,
                'items' => $items['get'],
            ),
        );    
        return $response;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ItemRequest $request, $id)
    {

        # валидация входящих полей
        $validatedData = $request->validated();

        # поиск обновляемой записи
        $item = Item::findOrFail($id);

        # в запросе есть галерея:
        if ( $request->input('gallery') != null ) {
            # добавляем к $item ссылку на gallery && сохраняем relation gallery
            foreach ( $request->input("gallery") as $key => $value ) {
                if( !isset($value['id']) ){
                    # в базе нет (id в запросе отсутствует) - добавляем фото
                    $tmp = new Gallery($value);
                    $tmp->save();
                    # сохраняем
                    $item->gallery()->save($tmp);
                    
                }
            }
        }

        # в запросе есть галерея:
        if ( $request->input('video') != null ) {
            # добавляем к $item ссылку на gallery && сохраняем relation video
            foreach ( $request->input("video") as $key => $value ) {
                if( !isset($value['id']) ){
                    # в базе нет (id в запросе отсутствует) - добавляем фото
                    $tmp = new Video($value);
                    $tmp->save();

                    # сохраняем
                    $item->video()->save($tmp);
                    
                }
            }
        }

        # сохраним slug
        $item->slug = $request->input('slug');

        # обновим основную запись
        $result = $item->update($validatedData);

        $items = $this->itemRepository->all();
        $response = array(
            'result' => array(
                'status' => $result,
                'items' => $items['get'],
            ),
        );

        return $response;
    }
    /**
     * Remove video manyToMany relation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function video(Request $request)
    {
        # валидация входящих полей
        // $validatedData = $request->validated();

        # поиск обновляемой записи
        $item = Item::findOrFail( $request->input("item_id") );

        # удаляем привязку
        $result = $item->video()->detach($request->input("gallery_id"));
        $items = $this->itemRepository->all();

        # после обновления
        $response = array(
            'result' => array(
                'status' => ( isset($result) ? $result : null ),
                'items' => $items['get'],
            ),
        );

        return $response;
    }
    /**
     * Remove gallery manyToMany relation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function gallery(Request $request)
    {
        # валидация входящих полей
        // $validatedData = $request->validated();

        # поиск обновляемой записи
        $item = Item::findOrFail( $request->input("item_id") );

        # удаляем привязку
        $result = $item->gallery()->detach($request->input("gallery_id"));

        $items = $this->itemRepository->all();

        # после обновления
        $response = array(
            'result' => array(
                'status' => ( isset($result) ? $result : null ),
                'items' => $items['get'],
            ),
        );

        return $response;
    }
    /**
     * Update sort of items.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function sort(Request $request)
    {
        $items = $request->all();
        foreach ($items as $key => $value) {
            $items[$key]['sort'] = $key;
            # найдем в БД выбранный элемент меню
            $item = Item::where('id', $items[$key]['id'])->first();
            $item['sort'] = $items[$key]['sort'];
            $item->save();
        }
        $items = $this->itemRepository->all();
        $response = array(
            'result' => array(
                'status' => 1,
                'items' => $items['get'],
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
            $result = Item::find($id)->delete();
            $response = array(
                'result' => array(
                    'status' => $result,
                    'items' => Item::latest()->with(["category", "gallery"])->get(),
                ),
            );
        }else{
            # массовое удаление
            $result = array();
            foreach ( Item::whereIn('id', json_decode($request->input('ids'), 1) )->get() as $key => $value ) {
                $result[] = $value->delete();
            }
            $response = array(
                'result' => array(
                    'status' => !in_array(!1, $result),
                    'items' => Item::latest()->with(["category", "gallery"])->get(),
                ),
            );
        }
        
        return $response;
    }

}
