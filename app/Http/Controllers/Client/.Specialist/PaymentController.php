<?php

namespace App\Http\Controllers\Client\Specialist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\Panel\School\ItemRequest;
use App\Models\Gallery\Gallery;

use App\Repositories\Client\SpecialistPlatformRepository as ItemRepository;

class PaymentController extends Controller
{
    private ItemRepository $itemRepository;

    public function __construct(ItemRepository $itemRepository)
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
        # необходимые данные
        # $items = Course::where("title", "like", "%{$request->input('query')}%")->get(); 
        $response = array(
            "items" => $this->itemRepository->document()
        );
        // dd(__METHOD__, $response);
        return view( "client/specialist/payment/index", $response);
    }

    /**
     * Return model school of registered user
     *
     * @return \Illuminate\Http\Response
     */
    public function school()
    {
        $session = auth()->user()->id;
        $school = School::where("user_id", $session)->first();
        return $school;
    }

    /**
     * Display the create form for school
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {        
        $where = array();
        $where[] = array("user_id", auth()->user()->id);
        $item = School::where($where)->first();

        if ( !is_null($item) ) {
            return redirect("/account");
        }
        return view("/client/account/profile/create"); 
    }

    /**
     * In this context method store use for: filter and return template
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $item = new School($request->all());        

        $tarif = Tarif::where("slug", "tarif-demo")->first();

        $item->user_id = auth()->user()->id;
        $item->tarif_id = $tarif->id;
        $item->is_visible = 1;

        $item->save();

        # в лекции есть галерея:
        if ( $request->input('gallery') != null ) {

            # добавляем к $item ссылку на gallery && сохраняем relation gallery
            foreach ( $request->input("gallery") as $key => $value ) {
                if( !isset($value['id']) ){

                    # в базе нет (id в запросе отсутствует) - добавляем фото

                    $gallery_item = new Gallery($value);
                    $gallery_item["src"] = "/public" . $value["path"];
                    $gallery_item->save();

                    # сохраняем
                    $item->gallery()->save($gallery_item);       
                }
            }
        }

        # после обновления
        $response = array(
            'result' => array(
                'status' => 1,
            ),
            'success' => array(
                "Профиль школы создан.",
                "Вы будете перенаправлены в личный кабинет.",
            ),
            'href' => "/account/profile",
        );
        return $response;
    }

    /**
     * Display the search results
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $slug)
    {
        if ( !isset($slug) ) {
            abort(404);
        }
        $session = auth()->user()->id;
        $where = array();
        $where[] = array("user_id", $session);
        $where[] = array("slug", $slug);
        $school = School::where($where)->with(["gallery"])->first();

        if ( is_null($school) ) {
            abort(404);
        } 

        $response = array(
            "item" => $school,
            "form" => array(
                "h1" => "Редактирование профиля",
                "id" => $school->id,
                "title" => $school->title,
                "slug" => $school->slug,
                "phone" => $school->phone,
                "email" => $school->email,
                "adress" => $school->adress,
                "latitude" => $school->latitude,
                "longitude" => $school->longitude,
                "tarif_id" => $school->tarif_id,
                "user_id" => $school->user_id,
                "body_short" => $school->body_short,
                "meta_title" => $school->meta_title,
                "meta_description" => $school->meta_description,
                "meta_keywords" => $school->meta_keywords,
                "meta_canonical" => $school->meta_canonical,    

                "gallery" => @$school->gallery()->orderBy("id", "DESC")->first(),
                "gallery_src" => @$school->gallery()->orderBy("id", "DESC")->first()->src,
            ),
            "template" => array(
                "button" => "Обновить",
            ),
        );
        # dd(__METHOD__, $response);

        # $response["template"]["paginated"] = view("/client/account/course/show", $response)->render();

        return view("/client/account/profile/show", $response); 
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

dd(__METHOD__, 1);
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
        $where = array();
        $where[] = array("user_id", auth()->user()->id);
        $where[] = array("id", $id);
        $item = School::where($where)->with(["gallery"])->first();

        # dd(__METHOD__, $request->input("gallery"), $item);
    
        if ( $request->input('gallery') != null ) {
            # добавляем к $item ссылку на gallery && сохраняем relation gallery
            foreach ( $request->input("gallery") as $key => $value ) {
                if( !isset($value['id']) ){
                    # в базе нет (id в запросе отсутствует) - добавляем фото

                    $gallery_item = new Gallery($value);

                    $gallery_item["src"] = "/public" . $value["path"];
                    # dd(__METHOD__, $gallery_item, $value, $item->gallery());
                    # сохраняем
                    $item->gallery()->save($gallery_item);       
                }
            }
            # обновим привязку
            $item->is_active_gallery = 1;
            $item->save();

        }

        # сохраним slug
        $item->slug = $request->input('slug');

        # обновим основную запись
        $result = $item->update($request->validated());

        # после обновления
        $response = array(
            'result' => array(
                'status' => $result,
            ),
            'success' => array(
                "Информация сохранена."
            ),
            'href' => "/account/profile/" . $item['slug'],
        );
        # dd(__METHOD__, $item, $response);
        return $response;
    }

}
