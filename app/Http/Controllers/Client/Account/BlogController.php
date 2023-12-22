<?php

namespace App\Http\Controllers\Client\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use App\Models\File\Gallery;
use App\Repositories\Client\Account\AccountRepository;
use App\Repositories\Client\Account\BlogRepository;
use App\Http\Requests\Client\Account\BlogRequest as ItemRequest;

use App\Models\Blog\Item as Item;

class BlogController extends Controller
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
        $response = array(
            "_users" => $this->accountRepository->rootuser(),
            "blog" => $this->itemRepository->accountblog()
        );
        return view("client/account/blog/index", $response);
    }

    /**
     * Display the create form for school
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {     
        $response = array(
            "_blog_tag" => $this->itemRepository->tag(),
            "_blog_category" => $this->itemRepository->category()
        ); 
        return view("client/account/blog/create", $response); 
    }

    /**
     * In this context method store use for: filter and return template
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ItemRequest $request)
    {

        $_account = $this->accountRepository->account();


        $item = new Item($request->validated());
        $item->account_id = $_account->id;

        $result = $item->save();

        # в лекции есть галерея:
        if ( $request->input('tag_id') != null ) {
            foreach ( $request->input("tag_id") as $key => $value ) 
            {
                continue;
                if( isset($value['id']) ) continue;
                # в базе нет (id в запросе отсутствует) - добавляем фото
                $subitem = new Tag($value);
                $subitem->save();
                # сохраняем
                $item->tag()->save($subitem);   
            }
            # обновим привязку
            # $item->save();
        }
        session()->flash("message", ["Запись сохранена и <a href='/blog/{$item['slug']}'>доступна для предпросмотра</a>."]);
        return redirect("/account/blog/{$item->id}");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {   
        # response messages
        $success = [];
        $warning = [];

        # support link
        #$support_tpl = DB::table('settings')
        #                    ->where('slug', 'ssylka-na-specialista-texpodderzki')
        #                    ->first();
        
        # $support_tpl = collect($support_tpl)->toArray()['value'];
        $support_tpl = "";

        $where = [];
        $where[] = ["id", "=", $id];
        $item = $this->itemRepository->findWhere($where);
        

        $response = array(
            "_blog" => $item,
            "gallery" => $item->gallery->toArray(),
            "_blog_tag" => $this->itemRepository->tag(),
            "_blog_category" => $this->itemRepository->category(),
        ); 
        $success[] = "Добро пожаловать в режим редактирования записи.";
        if ( $item->trashed() ) {
            $warning[] = "Текущая запись не отображается на сайте потому что она в была временно скрыта.";
        }elseif( is_null($item->is_approve) ){
            $warning[] = "Текущая запись не отображается на сайте потому что она в статусе \"на модерации\". Для вашего аккаунта назначен <a target='_blank' href='{$support_tpl}'>специалист технической поддержки</a>";
        }else{
            $warning[] = "Запись размещена в каталоге ebbandgrow.ru";
            $success[] = "После внесения любых изменений запись отправляется на модерацию. Только постоянно модерируя предлагаемые изменения мы гарантируем что контент, размещаемый пользователями соответствует законам.";
        }
        session()->flash("success", $success);
        session()->flash("warning", $warning);
        
        return view("client/account/blog/show", $response); 
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
        $validated = $request->validated();
        $item = Item::whereId($id)->first();
        $item->fill($validated);
        $item->save();

        # 16.07.2023 в этом случае пути картинок будут разными после каждого обновления
        $gallery = $item->gallery()->get();
        if ( count($gallery) > 0 ) {
            # получение всех изображений 
        }

        if ( $request->input('gallery') != null ) {
            dd(__METHOD__, $request->input('gallery') );
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

        $response = array(
            "_blog" => $item,
            "gallery" => $gallery,
            "_blog_tag" => $this->itemRepository->tag(),
            "_blog_category" => $this->itemRepository->category(),
        ); 

        $success = [];
        $warning = [];
        $success[] = "Запись обновлена и <a href='/blog/{$item['slug']}'>доступна для предпросмотра</a>.";
        $warning[] = "После внесения изменений запись отправилась на модерацию и временно не отображается на сайте.";

        session()->flash("success", $success);
        session()->flash("warning", $warning);

        return view("client/account/blog/show", $response); 

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $item = Item::withTrashed()->find($id);


        if ( $request->method == 'deleteForce' ) {
            $item->forceDelete();
        }else{
            if ( $item->trashed() ) {
                $item->restore();
            }else{
                $item->delete();
            }
        }
        return response()->json(['status' => 'success'], 200);

        die("here below is classic delete row fn");
        # here below is classic delete row fn
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
    /**
     * Create relation with image src
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function filesetup(Request $request)
    {
        $model = array(
            "title" => $request->get('src'),
            "src" => $request->get('src'),
            "url" => $request->get('src'),
        );
        if ( is_null($request->blog) ) {
            # в текущем запросе запись еще не создана в БД

            $response = array(
                'gallery' => $model,
            );
            return response()->json($response, 200);
        }

        $item = Item::whereId($request->blog)->first();
        $gallery = new Gallery($model);
        $item->gallery()->save($gallery);

        $response = array(
            'gallery' => $gallery->toArray(),
            'item' => $item->toArray(),
        );
        return response()->json($response, 200);
    }
    /**
     * Detach image from blog, remove file
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function filedetach(Request $request)
    {
        $model = array(
            "id" => $request->get('file')
        );
        $gallery = Gallery::find($model['id']);
        # dd(__METHOD__, $gallery, Storage::disk('public')->exists($gallery->url), Storage::exists($gallery->url));
        $gallery->delete();

        // Storage::delete($item->url);

        return response()->json([], 200);
    }
    /**
     * Return template for gallery
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function refreshgallerytemplate(Request $request)
    {
        $item = Item::whereId($request->blog)->first();
        $gallery = $item->gallery;

        $response = array(
            'gallery' => $gallery->toArray(),
        );
        return view("components.client.account.blog.gallery-component", $response); 
    }

}
