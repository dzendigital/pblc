<?php

namespace App\Http\Controllers\Auth\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        
        return view('/auth/user/login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
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
                break;
            case 'user':
                $return_url = RouteServiceProvider::USER;
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
