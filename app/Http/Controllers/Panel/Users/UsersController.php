<?php

namespace App\Http\Controllers\Panel\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Account;

use App\Http\Requests\Panel\UserRequest;
use App\Repositories\Panel\UserRepository as ItemRepository;

use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
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
    public function index(User $item)
    {
        
        # 26.10.2022 - запрашиваем только записи, которые можно изменять
        return view("panel/user/index", [
            'items' => $this->itemRepository->byRole(auth()->user()->roles->first()->slug),
            'roles' => Role::all()
        ]);
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
    public function store(UserRequest $request)
    {
        # валидация входящих полей
        $validatedData = $request->validated();


        # создание объекта с данными
        $item = new User($validatedData);
        $item['email_verified_at'] = now();
        $item['password'] = Hash::make($validatedData['password']);

        # сохранение объекта
        $role = Role::where('id', $request->input('role'))->first();

        #сохраняем привязку роли
        $status = $item->save();
        $item->roles()->attach($role);
        
        switch ($role->slug) {
            case 'account':
                # создаем профиль Account
                $account_model = array(
                    "user_id" => $item->id,
                    "title" => $item->email,
                    "phone" => $item->phone,
                    "city" => null,
                    "email" => $item->email,
                    "is_sms" => null,
                    "is_email" => null,
                    "is_visible" => 1,
                    "sort" => null,
                );
                $account_a = new Account($account_model);
                $account_a->save();
            break;
        }

        # после создания и сохранения отношений - запросим новосозданный объект и вернем его для добавления во view
        $response = array(
            'result' => array(
                'status' => $status,
                'items' => $this->itemRepository->byRole(auth()->user()->roles->first()->slug),
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
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $item = User::where('id', $id)->with('roles')->first();
        $item['phone'] = $input['phone'];
        $item['email'] = $input['email'];
        if( $input['password'] != null ){
            $item['password'] = Hash::make($input['password']);
        }
        $status = $item->save();

        $response = array(
            'result' => array(
                'status' => $status,
                'items' => $this->itemRepository->byRole(auth()->user()->roles->first()->slug),
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
    public function role(Request $request, $id)
    {
        $input = $request->input('role');
        $item = User::where('id', $id)->with('roles')->first();
        // $r = $user->roles()->attach( Role::where('id', $input['role'])->first() );
        $item->refreshRoles($input);

        $response = array(
            'result' => array(
                'status' => $item->roles()->first()->id == $input,
                'items' => $this->itemRepository->byRole(auth()->user()->roles->first()->slug),
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
    public function destroy($id)
    {
        $result = User::find($id)->forceDelete();
        $response = array(
            'result' => array(
                'status' => $result,
            ),
        );
        
        return $response;
    }
}
