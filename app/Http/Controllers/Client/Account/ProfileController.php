<?php

namespace App\Http\Controllers\Client\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\Client\Account\ProfileRequest as ItemRequest;
use App\Http\Requests\Client\Account\ProfilePasswordRequest as ProfilePasswordRequest;
# работа с файлами
use App\Models\Gallery\Gallery;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

use App\Repositories\Client\Account\AccountRepository as ItemRepository;
use App\Models\User as RootUser;

class ProfileController extends Controller
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
        $response = array(
            "_account" => $this->itemRepository->account()
        );
        return view( "client/account/profile/index", $response);
    }


    /**
     * In this context method store use for: filter and return template
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ItemRequest $request)
    {

        $item = $this->itemRepository->find(auth()->user()->id);
        # обновим основную запись
        $result = $item->update($request->validated());

        // dd(__METHOD__, $result, $item, $request->validated());

        # после обновления
        $response = array(
            'result' => array(
                'status' => $result,
            ),
            'success' => array(
                'status' => array("Информация сохранена."),
            ),
            'href' => "/account/profile",
        );
        return $response;
    }
    /**
     * установка постоянного пароля 
     */
    public function passwordupdate(ProfilePasswordRequest $request)
    {

        $item = RootUser::where("id", auth()->user()->id)->first();
        # обновим основную запись
        $model = array(
            "password" => Hash::make($request->input("password")),
            "password_verified_at" => null,
        );
        $item->password = $model['password'];
        $item->password_verified_at = $model['password_verified_at'];
        $result = $item->save();
        
        # после обновления
        $response = array(
            'result' => array(
                'status' => $result,
            ),
            'success' => array(
                'status' => array("Информация сохранена."),
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
    public function show1(Request $request, $slug)
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
    public function update(Request $request, $id)
    {
        $item = $this->itemRepository->find(auth()->user()->id);
        foreach ( $item->gallery()->get() as $key => $value) {
            // $value->delete();
            $item->gallery()->detach($value->id);
            Gallery::find($value->id)->delete();
            if ( Storage::exists($value->url) ) {
                Storage::delete($value->url);
            }
        }

        if ( $request->input('gallery') != null ) {
            # добавляем к $item ссылку на gallery && сохраняем relation gallery
            foreach ( $request->input("gallery") as $key => $value ) {
                if( !isset($value['id']) ){
                    # в базе нет (id в запросе отсутствует) - добавляем фото

                    $gallery_item = new Gallery($value);

                    $gallery_item["src"] = $value["src"];
                    $gallery_item["url"] = $value["path"];

                    // $gallery_item->save();

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
                "Информация сохранена."
            ),
            'href' => "/account/profile/" . $item['slug'],
        );
        return $response;
    }
    /**
     *
     * Remove image of profile.
     *
     */
    public function removeimage(Request $request)
    {

        $item = $this->itemRepository->find(auth()->user()->id);
        foreach ( $item->gallery()->get() as $key => $value) {
            $item->gallery()->detach($value->id);
            Gallery::find($value->id)->delete();
            if ( Storage::exists($value->url) ) {
                Storage::delete($value->url);
            }
        }

        # после обновления
        $response = array(
            'result' => array(
                'status' => 1,
            ),
            'success' => array(
                "Фотография удалена."
            ),
            'href' => "/account/profile/"
        );
        return $response;
    }

}
