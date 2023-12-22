<?php

namespace App\Http\Controllers\Client\Specialist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Specialist\Item as Specialist;

# работа с файлами
use App\Models\Gallery\Gallery;
use App\Models\Specialist\Portfolio as Portfolio;
use Illuminate\Support\Facades\Storage;

use App\Http\Requests\Client\Specialist\ProfileRequest as ItemRequest;
use App\Http\Requests\Client\Specialist\PasswordRequest as PasswordRequest;
use App\Repositories\Client\SpecialistPlatformRepository as ItemRepository;


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
        # необходимые данные 
        $response = array(
            "item" => $this->itemRepository->account(),
            "service" => $this->itemRepository->account()->service()->where("is_visible", 1)->whereNotNull("price")->get()
        );
        return view( "client/specialist/profile/index", $response);
    }

    /**
     * Display the create form for school
     * ATTENTION: custom middleware
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {        
        # $email = auth()->user()->email;
        # $specialist = Specialist::where([["email", "=", $email]])->first();

        return view("/client/specialist/profile/create"); 
    }

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ItemRequest $request)
    {
        $user = $this->itemRepository->findUser(auth()->user()->id);

        if ( Specialist::where("user_id", $user->id)->exists() ) 
        {
            $response = array(
                'result' => array(
                    'status' => null,
                ),
                'success' => array(
                    'status' => array("Профиль специалиста создан ранее. Вы будете перенаправлены в личный кабинет."),
                ),
                'href' => "/specialist/profile",
            );
            return response()->json($response, 422);
        }

        $item = new Specialist();
        $item->title = $user->email; 
        $item->email = $user->email; 
        $item->inn = $user->orgname_inn; 
        $item->user_id = $user->id; 

        $item->is_organization = $request->input('is_organization') ? 1 : null;
        $item->is_soleproprietor = $request->input('is_soleproprietor') ? 1 : null;
        $item->firstname = $request->input('firstname');
        $item->middlename = $request->input('middlename');
        $item->lastname = $request->input('lastname');
        $item->region = $request->input('region');

        $item->orgname = $request->input('orgname');

        $is_save = $item->save();

        if ( $request->input('gallery') != null ) {
            # добавляем к $item ссылку на gallery && сохраняем relation gallery
            foreach ( $request->input("gallery") as $key => $value ) {
                if( !isset($value['id']) ){
                    # в базе нет (id в запросе отсутствует) - добавляем фото

                    $gallery_item = new Gallery($value);

                    $gallery_item["src"] = $value["src"];
                    $gallery_item["url"] = $value["url"];

                    # сохраняем
                    $item->gallery()->save($gallery_item);       
                }
            }
        }

        # после обновления
        $response = array(
            'result' => array(
                'status' => $is_save,
            ),
            'success' => array(
                'status' => array("Информация сохранена."),
            ),
            'href' => "/specialist/profile",
        );
        return $response;
    }

    /**
     * Update password of root user who own user profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function password(PasswordRequest $request)
    {
        $item = $this->itemRepository->findUser(auth()->user()->id);
        
        # обновим основную запись
        $result = $item->update(array("password" => Hash::make($request->password)));

        # после обновления
        $response = array(
            'result' => array(
                'status' => $result,
            ),
            'success' => array(
                'status' => array("Информация сохранена."),
            ),
            'href' => "/specialist/profile",
        );
        return $response;
    }

    /**
     * Update service list of specialist
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function service(Request $request)
    {
        $is_update = $this->itemRepository->updateService($request->input("service"));
       
        # после обновления
        $response = array(
            'result' => array(
                'status' => $is_update,
            ),
            'success' => array(
                'message' => array("Информация сохранена."),
            ),
            'href' => "/specialist/profile",
        );
        return $response;
    }

    /**
     * Update portfolio gallery list of specialist
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function portfolio(Request $request)
    {
        # аккаунт
        $item = $this->itemRepository->find(auth()->user()->id);

        # сохранение файла
        $path = array(); # получаем путь файла, по которому его можно использовать во view
        $url = array(); # получаем путь файла, по которому его можно найти с помощью Storage::exists($url)
        $files = array();
        foreach ($request->file('file') as $file) {
            $url = Storage::put("public/tmp/catalog/galleryportfolio", $file); 
            $path = Storage::url($url); 
            $filename = $file->getClientOriginalName();
            $files[] = array(
                "url" => $url,
                "path" => $path,
                "filename" => $filename,
            );
        }

        # сохранение relation
        $is_save = array();
        foreach ( $files as $key => $value ) {

            # в базе нет (id в запросе отсутствует) - добавляем фото
            $tmp = new Portfolio($value);
            $tmp["src"] = $value["url"];
            $tmp["url"] = $value["path"];


            # сохраняем
            $is_save[] = $item->portfolio()->save($tmp);       

        }

        # после обновления
        $response = array(
            'result' => array(
                'status' => 1,
            ),
            'success' => array(
                'message' => array("Информация сохранена."),
            ),
            'href' => "/specialist/profile",
        );
        return $response;
    }

    /**
     * Update portfolio gallery list of specialist
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function portfolioremove($portfolio_id)
    {
        # аккаунт
        $item = $this->itemRepository->account();
        
        
        # после обновления
        $response = array(
            'result' => array(
                'status' => $item->portfolio()->where('id', $portfolio_id)->delete(),
            ),
            'success' => array(
                'message' => array("Информация сохранена."),
            ),
            'href' => "/specialist/profile",
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


    public function updateraw(ItemRequest $request)
    {
        $item = $this->itemRepository->find(auth()->user()->id);
        # обновим основную запись
        $result = $item->update($request->validated());

        # после обновления
        $response = array(
            'result' => array(
                'status' => $result,
            ),
            'success' => array(
                'status' => array("Информация сохранена."),
            ),
            'href' => "/specialist/profile",
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
            'href' => "/specialist/profile/" . $item['slug'],
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
            'href' => "/specialist/profile/"
        );
        return $response;
    }

}
