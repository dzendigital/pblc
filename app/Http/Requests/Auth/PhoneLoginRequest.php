<?php

namespace App\Http\Requests\Auth;
use Carbon\Carbon;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

use App\Models\User as RootUser;

class PhoneLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'phone' => 'required',
            'password' => 'required|string',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate()
    {
        $this->ensureIsNotRateLimited();

        $credentials = array(
            'phone' => preg_replace( '/[^0-9]/', '', $this->get('phone')), 
            'password' => $this->get('password')
        );

        # сверяем время действия временого пароля
        switch ($this->input('usertype')) {
            case 'specialist':
                $user = RootUser::where([["phone", "=", $credentials['phone']]])->has('specialist')->first();
            break;
            
            case 'account':
                $user = RootUser::where([["phone", "=", $credentials['phone']]])->has('account')->first();
            break;
            
            default:
                throw ValidationException::withMessages([
                    'email' => "Тип пользователя не найден. Обратитесь к администратору сайта.",
                ]);
            break;
        }
        if ( is_null($user) ) 
        {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }
        if ( !is_null($user->password_verified_at) ) {
            if ( is_null($user) ) 
            {
                if ( $user->password_verified_at > Carbon::now() ) 
                {
                    dd(__METHOD__, "время пароля истекло");
                    throw ValidationException::withMessages([
                        'email' => __('auth.failed'),
                    ]);
                }
            }
        }

        $is_attempt = Auth::attempt($credentials);
        
        if (! Auth::attempt($credentials) ) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited()
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey()
    {
        return Str::lower($this->input('email')).'|'.$this->ip();
    }
}
