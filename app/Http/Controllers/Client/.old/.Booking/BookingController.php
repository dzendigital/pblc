<?php

namespace App\Http\Controllers\Client\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\Client\Booking\BookingRequest;

use App\Models\Transport\Transport;
use App\Models\Directions\Directions;
use App\Models\City\City;
use App\Models\Booking\Booking;

# use App\Models\Client\Catalog\Catalog;
# use App\Models\Client\Catalog\Category;

use App\Events\Booking\NewBookingEvent;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param string $slug
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        # поиск в БД по заданным параметрам: departure_city, arrival_city, (not departure_date)
        # $items = Directions::latest()->with(['departure', 'arrival', 'transport'])->get();

        # заполняем $where
        $where = array();
        $where['departure'][] = array('title', '=', $request->input('departure'));
        $where['arrival'][] = array('title', '=', $request->input('arrival'));
        # $where['transport'][] = array('title', '=', $request->input('arrival'));

        # поисковой запрос
        $search_results = Directions::whereHas('departure', function ($query) use ($where) {
            return $query->where($where['departure']);
        })->whereHas('arrival', function ($query) use ($where) {
            return $query->where($where['arrival']);
        })->latest()->has('transport')->with(['departure', 'arrival', 'transport', 'transport.gallery'])->get();

        # необходимые данные
        $directions = Directions::latest()->with(['departure', 'arrival'])->get();
        $departures = $directions->pluck('departure')->unique();
        $arrivals = $directions->pluck('arrival')->unique();
        
        $transport = $search_results->pluck('transport')->unique();
        // dd(__METHOD__, $transport);
        $response = array(
            'items' => $search_results,
            "departures" => $departures,
            "arrivals" => $arrivals,
            "transport" => $transport,
            "form" => array(
                "departure" => $request->input('departure'),
                "arrival" => $request->input('arrival'),
                "departure_date" => $request->input('departure_date'),
            ),      
        );      
        return view( "client/destinations/search", $response);
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
        //
    }

    /**
     * Display the specified resource.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $destination - destinations slug
     * @param  string  $transport - transport slug
     * @return \Illuminate\Http\Response
     * 
     */
    public function show($destination, $transport)
    {
        # получаем url предыдущего шага бронирования 
        $url = URL::previous();

        # заполняем $where
        $where = array();
        $where['destination'][] = array('slug', '=', $destination);
        $where['transport'][] = array('slug', '=', $transport);

        # поисковой запрос
        # $search_results = Directions::where($where['destination'])->latest()->with(["transport" => function($query) use ($where){
        #     $query->where($where['transport']);
        # }, 'transport.gallery', 'arrival', 'departure' ])->get();

        $search_results = Directions::where($where['destination'])->latest()->with(["transport" => function($query) use ($where){
            $query->where($where['transport'])->firstOrFail();
        }, 'transport.gallery', 'arrival', 'departure' ])->firstOrFail();

        
        # подгрузим мета информацию
        $meta = array(
            'title' => @$item['title'],
            'description' => @$item['title'],
            'kaywords' => @$item['title'],
            'h1' => "",
        );

        $response = array(
            'search_results' => $search_results->first(),
            #'departure' => $search_results->pluck('departure')->first(),
            #'arrival' => $search_results->pluck('arrival')->first(),
            #'transport' => $search_results->pluck('transport')->first()->first(), # wtf && why
            'departure' => $search_results->departure,
            'arrival' => $search_results->arrival,
            'transport' => $search_results->transport->first(),
            'item' => @$item,
            'items' => @$items,
            'meta' => $meta,
            'url' => $url,
        );

        # dd(__METHOD__, $where, $response, $search_results->transport->first());

        return view('client/booking/index', $response);
    }

    /**
     * Create booking && redirect to show route
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param string $slug
     * @return \Illuminate\Http\Response
     */
    public function confirm(BookingRequest $request)
    {

        # добавляем в базу: создаем бронирование
        $validated_array = $request->validated();
       
        # бронирование: форма
        $response = array(
            'form' => $validated_array,
            'departure' => City::where('id', $request->input('departure'))->latest()->first(),
            'arrival' => City::where('id', $request->input('arrival'))->latest()->first(),
            'transport_raw' => Transport::where('id', $request->input('transport'))->latest()->first(),
            # 'transport' => Directions::where('id', $request->input('direction'))->with("transport")->where('transport.id', $request->input('transport'))->latest()->first(),
            'directions' => Directions::where('id', $request->input('direction'))->latest()->first(),
            'transport' => "",
        );
        $where = array();
        $where['transport'][] = array('id', '=', $request->input('transport'));
        
        $response["transport"] = 
            Directions::where('id', $request->input('direction'))
                ->with(["transport" => function ($query) use ($where) {
                    return $query->where($where['transport']);
                }])
                ->first();


        # dd($response["transport"]->transport, $response);

        # добавление в базу
        $booking_model = array(
            'direction_id' => @$request->input('direction'),
            'transport_id' => @$request->input('transport'),
            'slug' => md5(Hash::make($response['directions']['id'])),
            'name' => @$response['form']['name'], 
            'number' => @$response['form']['number'], 
            'email' => @$response['form']['email'], 
            'passenger' => @$response['form']['passenger'], 
            'date' => @$response['form']['date'], 
            'child-seat' => @$response['form']['child-seat'], 
            'oneway' => @$response['form']['oneway'], 
        );



        # создадим объект 
        $item = new Booking($booking_model);

        # создадим привязки
        $item->directions()->associate($response['directions']);

        $item->transport()->associate($response['transport_raw']);

        # сохраним
        $item->save();

        # для отправки ссылки на бронирования админу - добавим slug в $response['form']
        $response['form']['slug'] = config('app.name') . "/booking-confirmation/" . $item->slug;


        
        # отправка письма
        NewBookingEvent::dispatch($response);
        
        # dd($response);


        return redirect("/booking-confirmation/{$booking_model['slug']}");
    }
    /**
     * Display a confirmation info about booking
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param string $slug
     * @return \Illuminate\Http\Response
     */
    public function confirmation($slug)
    {
        # найдем бронирование по hash
        $item = Booking::where('slug', $slug)->firstOrFail();

        # данные о заказчике
        $form = array();
        $form['name'] = $item['name']; 
        $form['number'] = $item['number']; 
        $form['email'] = $item['email']; 
        $form['passenger'] = $item['passenger']; 
        $form['date'] = $item['date']; 
        $form['child-seat'] = $item['child-seat']; 
        $form['oneway'] = $item['oneway']; 

        # бронирование: форма
        $response = array(
            'form' => $form,
            'departure' => $item->directions()->first() != null ? $item->directions()->first()->departure()->first() : array(),
            'arrival' => $item->directions()->first() != null ? $item->directions()->first()->arrival()->first() : array(),
            'transport_raw' => $item->transport()->first(),
            'transport' => "",
            'directions' => $item->directions()->first(),
        );

        $where = array();
        $where['transport'][] = array('id', '=', $item['transport_id']);
        
        $response["transport"] = 
            $item->directions()
                ->with(["transport" => function ($query) use ($where) {
                    return $query->where($where['transport']);
                }])
                ->first();

        #dd($response['transport']['transport']->first()->pivot->price);


        return view("/client/booking/confirm", $response);
    }

}
