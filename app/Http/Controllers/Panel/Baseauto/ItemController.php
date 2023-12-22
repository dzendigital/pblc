<?php

namespace App\Http\Controllers\Panel\Baseauto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\Panel\Baseauto\ItemRequest;
use App\Models\Baseauto\Item;
use App\Models\Baseauto\Category;
use App\Models\Baseauto\Parameter;
use App\Models\Baseauto\ParameterPossible;
use App\Models\Baseauto\ParameterPossibleValue;
use App\Models\Gallery\Gallery;

use App\Repositories\Panel\BaseautoRepository as ItemRepository;
use App\Repositories\Panel\ParameterRepository as ParameterRepository;
use App\Repositories\Panel\ParameterPossibleRepository as ParameterPossibleRepository;

class ItemController extends Controller
{
    private ItemRepository $itemRepository;

    public function __construct(ItemRepository $itemRepository, ParameterRepository $parameterRepository, ParameterPossibleRepository $parameterPossibleRepository)
    {
        $this->itemRepository = $itemRepository;
        $this->parameterRepository = $parameterRepository;
        $this->parameterPossibleRepository = $parameterPossibleRepository;
    }  

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response = array();
        $response['items'] = $this->itemRepository->all();
        
        # $response['parameter'] = $this->parameterRepository->parameter();
        # $response['parameterPossible'] = $this->parameterRepository->parameterPossible();
        
        $response['category'] = Category::latest()->where('parent_id', 0)->with(['childs'])->get();

        

        return view("panel/baseauto/index", $response);
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

        # в лекции есть фото документов:
        if ( $request->input('doc') != null ) {
            foreach ( $request->input("doc") as $key => $value ) {
                if( !isset($value['id']) ){
                    # в базе нет (id в запросе отсутствует) - добавляем фото
                    $gallery_item = new Gallery($value);

                    # $gallery_item->sort = $value['sort'];
                    $gallery_item->sort = $key;
                    $gallery_item->save();

                    # сохраняем
                    $item->doc()->save($gallery_item);

                    
                }else{
                    # уже в базе
                }
            }
        }
        # в лекции есть галерея:
        if ( $request->input('gallery') != null ) {
            # добавляем к $item ссылку на gallery && сохраняем relation gallery
            foreach ( $request->input("gallery") as $key => $value ) {
                if( !isset($value['id']) ){
                    # в базе нет (id в запросе отсутствует) - добавляем фото
                    $gallery_item = new Gallery($value);

                    # $gallery_item->sort = $value['sort'];
                    $gallery_item->sort = $key;
                    $gallery_item->save();

                    # сохраняем
                    $item->gallery()->save($gallery_item);
                    // $item->gallery()->attach($gallery_item);
                    
                }else{
                    # уже в базе
                }
            }
        }

        
        # после создания и сохранения отношений - запросим новосозданный объект и вернем его для добавления во view
        $response = array(
            'result' => array(
                'status' => $result,
                'items' => $this->itemRepository->all(),
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

        # в лекции есть галерея:
        if ( $request->input('doc') != null ) {
            # добавляем к $item ссылку на gallery && сохраняем relation gallery
            foreach ( $request->input("doc") as $key => $value ) {
                if( !isset($value['id']) ){
                    # в базе нет (id в запросе отсутствует) - добавляем фото
                    $gallery_item = new Gallery($value);
                    # $gallery_item->sort = $value['sort'];
                    $gallery_item->sort = $key;

                    # сохраняем
                    $item->doc()->save($gallery_item);
                    
                }else{
                    $gallery_item = Gallery::where("id", $value['id'])->first();
                    $gallery_item->sort = $key;
                    $gallery_item->save();
                    # уже в базе
                }
            }
        }
        # в лекции есть галерея:
        if ( $request->input('gallery') != null ) {
            # добавляем к $item ссылку на gallery && сохраняем relation gallery
            foreach ( $request->input("gallery") as $key => $value ) {
                if( !isset($value['id']) ){
                    # в базе нет (id в запросе отсутствует) - добавляем фото
                    $gallery_item = new Gallery($value);
                    # $gallery_item->sort = $value['sort'];
                    $gallery_item->sort = $key;

                    # сохраняем
                    $item->gallery()->save($gallery_item);
                    
                }else{
                    $gallery_item = Gallery::where("id", $value['id'])->first();
                    $gallery_item->sort = $key;
                    $gallery_item->save();
                    # уже в базе
                }
            }
        }

        # сохраним slug
        $item->slug = $request->input('slug');

        # обновим основную запись
        $result = $item->update($validatedData);

        # после обновления
        $response = array(
            'result' => array(
                'status' => $result,
                'items' => $this->itemRepository->all(),
            ),
        );

        return $response;
    }
    /**
     * Return parameters of auto.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function parameter(Request $request)
    {   
        $auto_id = $request->input("id");
        $response = $this->itemRepository->find($auto_id);
        $response['brand'] = $this->parameterPossibleRepository->brand();
        
        $response['possible'] = $this->parameterPossibleRepository->parameterPossibleAdmin();
        return $response;
    }
    public function model(Request $request)
    {    
        $brand = $request->input("brand");
        $response = array();
        $response['model'] = $this->parameterPossibleRepository->model($brand);

        return $response;
    }
    public function generation(Request $request)
    {    
        $validated = $request->all();
        $response = array();
        $response['generation'] = $this->parameterPossibleRepository->generation($validated);

        return $response;
    }
    public function body(Request $request)
    {    
        $validated = $request->all();
        $response = array();
        $response['body'] = $this->parameterPossibleRepository->bodyStyle($validated);

        return $response;
    }
    /**
     * Update parameter of auto.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function parameterUpdate(Request $request, $id)
    {

        # валидация входящих полей
        $validatedData = $request->all();

        # поиск обновляемой записи
        $item = Item::findOrFail($id);

        foreach ( $validatedData['base'] as $key_parameter => $value_parameter ) {
            if ( $key_parameter == "id" ) {
                continue;
            }
            if ( $key_parameter == "is_approved" ) {
                $item[$key_parameter] = $value_parameter == 1 ? 1 : null;
                continue;
            }
            if ( $key_parameter == "color_code" ) {
                $color_code = array(
                    "#F0F0F0" => "Антибликовый белый",
                    "#C0C0C0" => "Серебряный",
                    "#808080" => "Серый",
                    "#000000" => "Черный",
                    "#FF0000" => "Красный",
                    "#800000" => "Темно-бордовый",
                    "#FFFF00" => "Желтый",
                    "#808000" => "Оливковый",
                    "#00FF00" => "Зеленый",
                    "#008000" => "Темно зеленый",
                    "#00FFFF" => "Аква",
                    "#008080" => "Бирюзовый",
                    "#0000FF" => "Синий",
                    "#000080" => "Темно-синий",
                    "#FF00FF" => "Фуксия",
                    "#800080" => "Фиолетовый",
                );
                $item[$key_parameter] = $value_parameter;
                $item["color"] = is_null($value_parameter) ? "цвет не указан" : $color_code[$value_parameter];
                continue;
            }
            $item[$key_parameter] = $value_parameter;
        }
        $item->save();

        $is_saved = array();
        foreach ( $validatedData['parameter'] as $key_parameter => $value_parameter ) {
            $item_parameter = ParameterPossible::where("slug", $key_parameter)->first();
            if ( is_null($item_parameter) ){
                continue;
            }

            # запрос существующего параметра
            $where = array();
            $where[] =  array('car_id', $item->id);
            $where[] =  array('parameter_id', $item_parameter->id);
            $is_parameter_set = Parameter::whereHas('car', function($query) use ($where){
                $query->where($where);
            })->first();
            
            # проверка, установлен ли параметр ранее
            if ( is_null($is_parameter_set) ) 
            {
                # если не установлен - создаем
                $parameter_model = array(
                    "parameter_id" => $item_parameter["id"],
                    "value" => $value_parameter,
                    "slug" => $key_parameter,
                    "is_visible" => 1,
                );
                $parameter_model = new Parameter($parameter_model);
                $parameter_model->slug = $key_parameter;
                $item->parameter()->save($parameter_model);

                $is_saved[$key_parameter] = 1;
                
            }else{
                # если установлен - заменяем значение
                $is_parameter_set->value = $value_parameter;
                $is_saved[$key_parameter] = $is_parameter_set->save();
            }
        }
        
        # после обновления
        $response = array(
            'result' => array(
                'status' => $is_saved,
                'items' => $this->itemRepository->all(),
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

        # после обновления
        $response = array(
            'result' => array(
                'status' => ( isset($result) ? $result : null ),
                'items' => Item::latest()->with(["category", "gallery"])->get(),
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
        $response = array(
            'result' => array(
                'status' => 1,
                'itemList' => $this->itemRepository->all(),
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
                    'items' => $this->itemRepository->all(),
                ),
            );
        }
        
        return $response;
    }

}
