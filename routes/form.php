<?php

use Illuminate\Support\Facades\Route;

# request
use App\Http\Requests\Client\Form\SurveyFormRequest;

# repository
use App\Repositories\ServeyRepository;

use Illuminate\Support\Facades\Http;

Route::post("/form/anketa", function (SurveyFormRequest $request, ServeyRepository $repository) 
{   
    $validated = $request->validated();   
    if ( !1 ) {
        # после основной валидации проверка captcha
        $_recaptchatoken = $request->input("recaptcha-token");
        $responsecaptcha = Http::get("https://www.google.com/recaptcha/api/siteverify",[
            'secret' => config('app.GOOGLE_RECAPTCHA_SECRET'),
            'response' => $_recaptchatoken
        ]); 
        if ( !$responsecaptcha->json()['success'] ) {
            # captcha fail
            throw \Illuminate\Validation\ValidationException::withMessages([
               'googlecaptcha' => ["Ошибка Google re-captcha, обновите страницу и попробуйте еще раз."],
            ]);
        }
    }

    $is_save = $repository->save($validated);
    $response = array(
        "template" => "
                <h1>Анкета отправлена. Спасибо!</h1>
                <h3>Вашей анкете присвоен номер #{$is_save}, вы можете посмотреть статус анкеты на странице <a target='_blank' href='/servey/{$is_save}'>статус&nbsp;анкеты</a></h3>

        ",
    );
    return Response::json($response, 200);
});