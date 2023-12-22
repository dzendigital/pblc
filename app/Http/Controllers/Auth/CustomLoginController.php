<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\PhoneLoginRequest;

class CustomLoginController extends Controller
{
    public function __construct()
    {
    }
    public function login(PhoneLoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        # используем пути редиректов в личный кабинет

        switch ( Auth::user()->roles()->first()->slug ) {
            case 'admin':
                $return_url = RouteServiceProvider::ADMIN;
                break;
            case 'account':
                $return_url = RouteServiceProvider::ACCOUNT;
                break;
            case 'specialist':
                $return_url = RouteServiceProvider::SPECIALIST;
                $specialist = $this->itemRepository->find(Auth::user()->id);
                if ( !is_null($specialist) && !is_null($specialist->region) ) 
                {
                    # устанавливаем город в сессию
                    $city = $this->cityRepository->findRegion($specialist->region);
                }
                break;
            case 'user':
                $return_url = RouteServiceProvider::USER;
                break;
            case 'manager':
                $return_url = RouteServiceProvider::MANAGER;
                break;
            default:
                break;
        }
        
        return redirect($return_url);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
