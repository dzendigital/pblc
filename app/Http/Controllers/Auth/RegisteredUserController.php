<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User\Item as PlatformUser;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('.auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        # определяем роль 
        # определяем маршрут редиректа 
        $role = null;
        $redirect = null;
        # ADMIN ACCOUNT SPECIALIST USER MANAGER
        $is_verified = null;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ],[
            'email.unique' => 'Пользователь с таким email адресом уже зарегистрирован.',
        ]);
        
        # создаем пользователя
        $model = array(
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => is_null($is_verified) ? null : now(),
        );
        $user = new User($model);
        $user['email_verified_at'] = now();
        
        $user->save();

        # после создания пользователя использовать attach(), чтобы назначить Role User - доступ в личный кабинет пользователя
        switch ($request->input("role")) {
            case 'manager':
                $role = Role::where('slug', 'manager')->first();
                $redirect = RouteServiceProvider::MANAGER;
                break;

            case 'specialist':
                $role = Role::where('slug', 'specialist')->first();
                $redirect = RouteServiceProvider::SPECIALIST;
                $is_verified = now();
                break;

            case 'user':
                $role = Role::where('slug', 'user')->first();
                $redirect = RouteServiceProvider::USER;
                $platform_user_model = array(
                    'title' => $request->name,
                    'phone' => null,
                    'email' => $request->email,
                    'firstname' => $request->email,
                    'middlename' => null,
                    'lastname' => null,
                    'region' => null,
                    'city' => null,
                    'is_sms' => null,
                    'is_email' => 1,
                    'user_id' => $user->id,
                    
                );
                $platform_user = new PlatformUser($platform_user_model);
                $platform_user->save();

                break;
            
            default:
                $role = Role::where('slug', 'account')->first();
                $redirect = RouteServiceProvider::ACCOUNT;
                break;
        }

        # добавляем роль
        $user->roles()->attach( $role );

        Auth::login($user);

        event(new Registered($user));

        return redirect($redirect);
    }
}
