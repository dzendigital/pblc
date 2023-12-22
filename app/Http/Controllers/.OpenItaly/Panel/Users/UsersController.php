<?php

namespace App\Http\Controllers\Panel\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

use App\Http\Requests\Panel\UserRequest;

use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(User $users)
    {
        return view("panel/user/index", [
            'items' => $users->with('roles')->orderBy('created_at', 'DESC')->get(),
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
        // $status = 1;

        # после создания и сохранения отношений - запросим новосозданный объект и вернем его для добавления во view
        $response = array(
            'result' => array(
                'status' => $status,
                'items' => $item->with('roles')->orderBy('created_at', 'DESC')->get(),
            ),
        );    
        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $user = User::where('id', $id)->with('roles')->first();
        $user['email'] = $input['email'];
        if( $input['password'] != null ){
            $user['password'] = Hash::make($input['password']);
        }
        $status = $user->save();

        $response = array(
            'result' => array(
                'status' => $status,
                'items' => $user->with('roles')->orderBy('created_at', 'DESC')->get(),
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
        $user = User::where('id', $id)->with('roles')->first();
        // $r = $user->roles()->attach( Role::where('id', $input['role'])->first() );
        $user->refreshRoles($input);

        $response = array(
            'result' => array(
                'status' => $user->roles()->first()->id == $input,
                'items' => $user->with('roles')->orderBy('created_at', 'DESC')->get(),
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
        $result = User::find($id)->delete();
        $response = array(
            'result' => array(
                'status' => $result,
            ),
        );
        
        return $response;
    }
}
