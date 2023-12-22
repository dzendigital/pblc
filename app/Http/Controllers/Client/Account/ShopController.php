<?php

namespace App\Http\Controllers\Client\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

use App\Repositories\Client\Account\AccountRepository;
use App\Repositories\Client\Account\BlogRepository;
use App\Http\Requests\Client\Account\BlogRequest as ItemRequest;

class ShopController extends Controller
{

    private BlogRepository $itemRepository;
    private AccountRepository $accountRepository;

    private $_account = null; 

    public function __construct(BlogRepository $itemRepository, AccountRepository $accountRepository)
    {
        $this->itemRepository = $itemRepository;
        $this->accountRepository = $accountRepository;



    } 
    /**
     * Display a listing of the resource.
     * 
     * 
     */
    
    public function index(Request $request)
    {    
        # авторизованый пользователь
        $this->_users = $this->accountRepository->rootuser();
        $response = array(
            "_users" => $this->_users
        );
        return view( "client/account/shop/index", $response);
    }

    /**
     * Display the create form for school
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {        
        dd(__METHOD__, 'форма для создания нового блога');
        return view("/client/account/profile/create"); 
    }

    /**
     * In this context method store use for: filter and return template
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ItemRequest $request)
    {
        dd(__METHOD__, $request->validated());
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        dd(__METHOD__, 'форма для редактирования блога');
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
        dd(__METHOD__, $request->validated(), $id);

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

}
