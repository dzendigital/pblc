<?php

namespace App\Http\Controllers\Client\User;

use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests\Client\User\ProfileRequest as ItemRequest;
use App\Http\Requests\Client\User\PasswordRequest as PasswordRequest;

use App\Models\User\Item as User;
use App\Models\User as RootUser;
use App\Models\Gallery\Gallery;
use Illuminate\Support\Facades\Storage;

use App\Repositories\Client\UserPlatformRepository as ItemRepository;

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
            "item" => $this->itemRepository->account()
        );

        return view( "client/user/profile/index", $response);
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

        # после обновления
        $response = array(
            'result' => array(
                'status' => $result,
            ),
            'success' => array(
                'status' => array("Информация сохранена."),
            ),
            'href' => "/user/profile",
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
            'href' => "/user/profile",
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
            'href' => "/user/profile/" . $item['slug'],
        );
        return $response;
    }


    /**
     * create user platform account
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {        
        $user = $this->itemRepository->findUser(auth()->user()->id);
        $platform_user_model = array(
            'title' => $user->name,
            'phone' => null,
            'email' => $user->email,
            'firstname' => $user->email,
            'middlename' => null,
            'lastname' => null,
            'region' => null,
            'city' => null,
            'is_sms' => null,
            'is_email' => 1,
            'user_id' => $user->id,
            
        );
        $platform_user = new User($platform_user_model);
        $platform_user->save();
        # $email = auth()->user()->email;
        # $specialist = Specialist::where([["email", "=", $email]])->first();

        return redirect("/user/profile", 301);; 
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
            'href' => "/user/profile/"
        );
        return $response;
    }

}
