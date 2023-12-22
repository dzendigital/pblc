<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

use App\Http\Requests\Auth\ResetPasswordRequest;

use App\Models\User as RootUser;
use App\Models\Account as Account;
use App\Models\User\Item as PlatformUser;
use App\Models\Specialist\Item as PlatformSpec;



class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $user = RootUser::where("email", $request->input('email'))->first();

        switch ($user->roles()->first()->slug) {
            case 'user':
                $view = '.auth.user.reset-password';
                break;
            case 'specialist':
                $view = '.auth.specialist.reset-password';
                break;
            case 'account':            
            default:
                $view = '.auth.reset-password';
                break;
        }

        return view($view, ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(ResetPasswordRequest $request)
    {
        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.

        $user = RootUser::where("email", $request->input('email'))->first();

        switch ($user->roles()->first()->slug) {
            case 'user':
                $view = 'user.login';
                break;
            case 'specialist':
                $view = 'specialist.login';
                break;
            case 'account':            
            default:
                $view = 'login';
                break;
        }

        return $status == Password::PASSWORD_RESET
                    ? redirect()->route($view)->with('status', __($status))
                    : back()->withInput($request->only('email'))
                            ->withErrors(['email' => __($status)]);
    }
}
