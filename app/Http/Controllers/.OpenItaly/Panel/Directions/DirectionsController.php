<?php

namespace App\Http\Controllers\Panel\Directions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Models\Directions\Directions;
use App\Models\City\City;
use App\Models\Transport\Transport;
use App\Models\Catalog\Gallery; # с этим что-то не так
use App\Http\Requests\Panel\Directions\DirectionsRequest;
use App\Http\Requests\Panel\Catalog\LecturesRequest;

class DirectionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("panel/directions/index", [
            'items' => Directions::latest()->with(['departure', 'arrival', 'transport.gallery'])->get(),
            'city' => City::latest()->with(['gallery'])->get(),
            'transport' => Transport::latest()->with(['gallery'])->get(),
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\Panel\Directions\DirectionsRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(DirectionsRequest $request)
    {
        # валидация входящих полей
        $validatedData = $request->validated();

        # создание объекта с данными
        $item = new Directions($validatedData);

        # обновляем видимость по-умолчанию
        $item->is_visible = 1;
        
        # сохраним slug
        if ( $request->input('slug') != null ) {
            $item->slug = $request->input('slug');
        }

        # сохранение объекта
        $result = $item->save();

        # $request содержит информацию о городе отправления
        if ( $request->input('departure_city_id') != null ) {

            # найдем город 
            $departure_city = City::find($request->input('departure_city_id'));

            # привяжем направление к городу отправления
            $item->departure()->associate($departure_city);
            # $item->departure()->save($departure_city);

            # сохраним привязку
            $item->save();

        }
        # $request содержит информацию о городе прибытия
        if ( $request->input('arrival_city_id') != null ) {

            # найдем город 
            $arrival_city = City::find($request->input('arrival_city_id'));

            # привяжем направление к городу отправления
            $item->arrival()->associate($arrival_city);
            # $item->arrival()->save($arrival_city);

            # сохраним привязку
            $item->save();   
        }
       
        # $request содержит информацию о транспорте
        if ( $request->input('transport') != null ) {
            # сохраним транспорт 
            
            # удалим старые привязки belongsToMany 
            $item->transport()->detach();

            # сохраняем relation т.к транспорт уже добавлен
            foreach ( $request->input("transport") as $key => $value ) {

                # найдем выбранный транспорт
                $transport_item = Transport::find($value['id']);
                
                # привязка belongsToMany: направления имеют несколько вариантов транспорта
                $item->transport()->attach($transport_item, ['price' => $value['price']]);

            }

            # обновим привязку
            # $item->is_active_gallery = 1;
            $item->save();
        }
        
        # после создания и сохранения отношений - запросим новосозданный объект и вернем его для добавления во view
        $response = array(
            'result' => array(
                'status' => $result,
                'items' => Directions::latest()->with(['departure', 'arrival', 'transport.gallery'])->get(),
                'item' => Directions::where('id', $item->id)->with(['departure', 'arrival', 'transport.gallery'])->get(),
            ),
        );    
        return $response;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\Panel\Directions\DirectionsRequest $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DirectionsRequest $request, $id)
    {
        # валидация входящих полей
        $validatedData = $request->validated();


        # поиск обновляемой записи
        $item = Directions::findOrFail($id);

        # $request содержит информацию о городе отправления
        if ( $request->input('departure_city_id') != null ) {

            # найдем город 
            $departure_city = City::find($request->input('departure_city_id'));


            # привяжем направление к городу отправления
            $item->departure()->associate($departure_city);

            # сохраним привязку
            # $item->save();
        }


        # $request содержит информацию о городе прибытия
        if ( $request->input('arrival_city_id') != null ) {

            # найдем город 
            $arrival_city = City::find($request->input('arrival_city_id'));

            # привяжем направление к городу отправления
            $item->arrival()->associate($arrival_city);

            # сохраним привязку
            # $item->save();   
        }

        # удалим старые привязки belongsToMany 
        $a = $item->transport()->detach();

        # $request содержит информацию о транспорте
        if ( $request->input('transport') != null ) {
            # сохраняем relation т.к транспорт уже добавлен
            foreach ( $request->input("transport") as $key => $value ) {
                # найдем выбранный транспорт
                $transport_item = Transport::find($value['id']);
                
                # привязка belongsToMany: направления имеют несколько вариантов транспорта
                $item->transport()->attach($transport_item, ['price' => $value['price']]);

            }

            # обновим привязку
            # $item->is_active_gallery = 1;
            # $item->save();
        }

        # сохраним slug
        $item->slug = $request->input('slug');

        # обновление основной записи
        $result = $item->update( $request->validated() );

        $response = array(
            'result' => array(
                'status' => $result,
                'items' => Directions::latest()->with(['departure', 'arrival', 'transport.gallery'])->get(),
                'item' => Directions::where('id', $item->id)->with(['departure', 'arrival', 'transport.gallery'])->get(),
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
            $item = Directions::where('id', $items[$key]['id'])->first();
            $item['sort'] = $items[$key]['sort'];
            $item->save();
        }
        $response = array(
            'result' => array(
                'status' => 1,
                'itemList' => Directions::latest()->with(['departure', 'arrival', 'transport.gallery'])->get(),
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
            $result = Directions::find($id)->delete();
            $response = array(
                'result' => array(
                    'status' => $result,
                    'items' => Directions::latest()->with(['departure', 'arrival', 'transport.gallery'])->get(),
                ),
            );
        }else{
            # массовое удаление
            $result = array();
            foreach ( Directions::whereIn('id', json_decode($request->input('ids'), 1) )->get() as $key => $value ) {
                $result[] = $value->delete();
            }
            $response = array(
                'result' => array(
                    'status' => !in_array(!1, $result),
                    'items' => Directions::latest()->with(['departure', 'arrival', 'transport.gallery'])->get(),
                ),
            );
        }
        
        return $response;
    }
}
