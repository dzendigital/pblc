<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

use App\Http\Requests\Auth\ResetPasswordRequest;

use App\Models\SmscodeModel;
use App\Models\User as RootUser;
use App\Models\Account as Account;
use App\Models\User\Item as PlatformUser;
use App\Models\Specialist\Item as PlatformSpec;
use App\Models\Role;



class MangoOfficeController extends Controller
{
    /**
     * login 
     */
    public function index(Request $request)
    {

        # валидация наличия телефона
        $validated = $request->validate([
            'phone' => 'required',
            'type' => 'required',
        ], [
            'phone.required' => 'Номер телефона обязателен для заполнения.',
            'type.required' => 'Не хватает критических данных для выполнения запроса, попробуйте позже.',
        ]);
        $phone_raw = $request->input("phone"); 
        $phone = preg_replace('/[^0-9]/', '', $phone_raw);


        if ( strlen($phone) != 11 ) 
        {
            return response()->json(array('errors'=> array("Ошибка в формате номера телефона.")), 422);
        }
        
        $phone_format = ltrim($phone, '7');

        $ac = substr($phone_format, 0, 3);
        $prefix = substr($phone_format, 3, 3);
        $suffix = substr($phone_format, 6, 2);
        $subsuffix = substr($phone_format, 8, 2);

        # создать из номера варианты:
        # raw: 79140050000
        # a) 89140050000
        # b) +79140050000
        # c) +7 (914) 005-24-41

        $phone_array = array();
        $phone_array[] = "7" . $phone_format;
        $phone_array[] = "8" . $phone_format;
        $phone_array[] = "+7" . $phone_format;
        $phone_array[] = "+7 ({$ac}) {$prefix}-{$suffix}-{$subsuffix}";
        $phone_array[] = "8 ({$ac}) {$prefix}-{$suffix}-{$subsuffix}";

        $user = RootUser::whereIn('phone', $phone_array)->first();

        # в случае если пользователь не найден
        if ( is_null($user) ) 
        {
            return response()->json(array('errors'=> array("Аккаунт с указанным номером телефона не найден, необходима регистрация.")), 422);
        }

        # generate sms code for login
        $code = rand(1000, 9999);

        $model_raw = array(
            "user_id" => $user->id,
            "phone" => $phone,
            "code" => $code,
        );
        $smscode = new SmscodeModel($model_raw);
        $is_save = $smscode->save();

        if ( !@$is_save ) 
        {
            return response()->json(array('errors'=> array("Достигнуто ограничение по запросам, попробуйте через <span class='nowrap'>1 минуту</span>.")), 422);
        }

        $content = array(
            "number" => $smscode->phone,
            "textarea" => "Ваш код авторизации yapodbor.ru: {$smscode->code}",
        );
        $config = array(
            "webhook" => "https://integration-webhook.mango-office.ru/webhookapp/common?code=03f7ffb5-7fb0-4475-95f5-67b5f7fef801&Source=Other&API_key=s22j8l3x9gmkzzf6vi2mb331xfosdgmu&Action=Send_SMS&EmployeeNUM=118&TelNumbr=({$content['number']})&SMSText=({$content['textarea']})",
            "endpoint" => "https://integration-webhook.mango-office.ru/webhookapp/common",
        );
        $this->sms($config, $content);

        return response()->json(array('messages'=> array("Смс сформировано и отправлено, укажите код в форме на странице для авторизации.")), 200);
    }
    /**
     * registration 
     */
    public function registration(Request $request)
    {
        # валидация наличия телефона
        $validated = $request->validate([
            'phone' => 'required',
            'type' => 'required',
        ], [
            'phone.required' => 'Номер телефона обязателен для заполнения.',
            'type.required' => 'Не хватает критических данных для выполнения запроса, попробуйте позже.',
        ]);
        $phone_raw = $request->input("phone"); 
        $phone = preg_replace('/[^0-9]/', '', $phone_raw);


        if ( strlen($phone) != 11 ) 
        {
            return response()->json(array('errors'=> array("Ошибка в формате номера телефона.")), 422);
        }
        
        $phone_format = ltrim($phone, '7');

        $ac = substr($phone_format, 0, 3);
        $prefix = substr($phone_format, 3, 3);
        $suffix = substr($phone_format, 6, 2);
        $subsuffix = substr($phone_format, 8, 2);

        # создать из номера варианты:
        # raw: 79140050000
        # a) 89140050000
        # b) +79140050000
        # c) +7 (914) 005-24-41

        $phone_array = array();
        $phone_array[] = "7" . $phone_format;
        $phone_array[] = "8" . $phone_format;
        $phone_array[] = "+7" . $phone_format;
        $phone_array[] = "+7 ({$ac}) {$prefix}-{$suffix}-{$subsuffix}";
        $phone_array[] = "8 ({$ac}) {$prefix}-{$suffix}-{$subsuffix}";

        $user_raw = RootUser::whereIn('phone', $phone_array)->first();
        
        if ( !is_null($user_raw) ) 
        {
            return response()->json(array('errors'=> array("Аккаунт с таким номером телефона уже зарегистрирован.")), 422);
        }

        // ---------------
        # определяем роль 
        # определяем маршрут редиректа 
        $role = null;
        $redirect = null;
        # ADMIN ACCOUNT SPECIALIST USER MANAGER
        $is_verified = null;

        # generate sms code for login
        $code = rand(1000, 9999);
        
        # создаем пользователя
        $model = array(
            'phone' => $phone,
            'password' => Hash::make($code),
            'password_verified_at' => Carbon::now()->addHours(24),
        );

        $user = new RootUser($model);
        $user->password_verified_at = $model['password_verified_at'];
        $user->save();

        # после создания пользователя использовать attach(), чтобы назначить Role User - доступ в личный кабинет пользователя
        switch ( $request->input('type') ) 
        {
            case 'manager':
                $role = Role::where('slug', 'manager')->first();
                $redirect = RouteServiceProvider::MANAGER;
            break;

            case 'specialist':
                $role = Role::where('slug', 'specialist')->first();
                $redirect = RouteServiceProvider::SPECIALIST;
                $user_account = array(
                    'title' => time(),
                    'phone' => $phone,
                    'is_sms' => 1,
                    'is_email' => null,
                    'user_id' => $user->id,
                    
                );
                $account = new PlatformSpec($user_account);
                $account->save();

            break;
            
            case 'account':
                $role = Role::where('slug', 'account')->first();
                $redirect = RouteServiceProvider::ACCOUNT;                

                $user_account = array(
                    'title' => time(),
                    'phone' => $phone,
                    'email' => null,
                    'city' => null,
                    'is_sms' => 1,
                    'is_email' => null,
                    'user_id' => $user->id,
                    
                );
                $account = new Account($user_account);
                $account->save();
            break;
            default:
                dd(__METHOD__, "Usertype not define.");
            break;
        }

        # добавляем роль
        $user->roles()->attach( $role );


        # отправка смс
        $model_raw = array(
            "user_id" => $user->id,
            "phone" => $phone,
            "code" => $code,
        );
        $smscode = new SmscodeModel($model_raw);
        $is_save = $smscode->save();

        if ( !@$is_save ) 
        {
            return response()->json(array('errors'=> array("Достигнуто ограничение по запросам смс, попробуйте через <span class='nowrap'>1 минуту</span>.")), 422);
        }

        $content = array(
            "number" => $smscode->phone,
            "textarea" => "Ваш код авторизации yapodbor.ru: {$smscode->code}",
        );
        $config = array(
            "webhook" => "https://integration-webhook.mango-office.ru/webhookapp/common?code=03f7ffb5-7fb0-4475-95f5-67b5f7fef801&Source=Other&API_key=s22j8l3x9gmkzzf6vi2mb331xfosdgmu&Action=Send_SMS&EmployeeNUM=118&TelNumbr=({$content['number']})&SMSText=({$content['textarea']})",
            "endpoint" => "https://integration-webhook.mango-office.ru/webhookapp/common",
        );

        $this->sms($config, $content);

        return response()->json(array('messages'=> array("Смс сформировано и отправлено, укажите код в форме на странице для авторизации.")), 200);
    }
    /**
     * функция обновления пароля через полученный в смс код 
     */
    public function forgotpassword(Request $request)
    {
        # валидация наличия телефона
        $validated = $request->validate([
            'phone' => 'required',
            'smscode' => 'required',
            'type' => 'required',
        ], [
            'smscode.required' => 'Смс код обязателен для авторизации.',
            'type.required' => 'Не хватает критических данных для выполнения запроса, попробуйте позже.',
        ]);
        $smscode = $request->input("smscode"); 
        $phone_raw = $request->input("phone"); 
        $phone = preg_replace('/[^0-9]/', '', $phone_raw);

        $phone_format = ltrim($phone, '7');

        $ac = substr($phone_format, 0, 3);
        $prefix = substr($phone_format, 3, 3);
        $suffix = substr($phone_format, 6, 2);
        $subsuffix = substr($phone_format, 8, 2);

        # создать из номера варианты:
        # raw: 79140050000
        # a) 89140050000
        # b) +79140050000
        # c) +7 (914) 005-24-41

        $phone_array = array();
        $phone_array[] = "7" . $phone_format;
        $phone_array[] = "8" . $phone_format;
        $phone_array[] = "+7" . $phone_format;
        $phone_array[] = "+7 ({$ac}) {$prefix}-{$suffix}-{$subsuffix}";
        $phone_array[] = "8 ({$ac}) {$prefix}-{$suffix}-{$subsuffix}";
        
        # своя модель для каждого типа пользователя
        $where = array();
        $where[] = array("phone", $phone);

        $user = RootUser::whereIn('phone', $phone_array)->first();
        
        if ( is_null($user) ) 
        {
            return response()->json(array('errors'=> array("Аккаунт с таким номером телефона не зарегистрирован, требуется регистрация.")), 422);
        }

        # confirm if sms code is correct
        $where = array();
        $where[] = array("phone", $phone);
        $where[] = array("code", $smscode);
        $where[] = array("user_id", $user->id);
        
        # проверка срока жизни, код живет 5 минут, после считается не активным
        $created_at_limit = null;
        $where[] = array("created_at", ">", Carbon::now()->subMinutes(4));

        $smscode_exist = SmscodeModel::where($where)->first();

        if ( is_null($smscode_exist) ) 
        {
            return response()->json(array('errors'=> array("Код не найден или просрочен.")), 422);
        }


        # сохраняем пароль пользователя
        $model = array(
            'phone' => $phone,
            'password' => Hash::make($smscode),
            'password_verified_at' => Carbon::now()->addHours(24),
        );

        $user->password = $model['password'];
        $user->password_verified_at = $model['password_verified_at'];
        $user->save();

        # код принят, выполняем авторизацию пользователя
        Auth::login($user);

        # код одноразовый, будет удален
        $is_delete = $smscode_exist->delete();

        return response()->json(array('messages'=> array("Вход выполнен, страница будет перезагружена.")), 200);
    }
    /**
     * подтверждение входа и авторизация
     */
    public function confirm(Request $request)
    {
        # валидация наличия телефона
        $validated = $request->validate([
            'phone' => 'required',
            'smscode' => 'required',
            'type' => 'required',
        ], [
            'smscode.required' => 'Смс код обязателен для авторизации.',
            'type.required' => 'Не хватает критических данных для выполнения запроса, попробуйте позже.',
        ]);


        $smscode = $request->input("smscode"); 
        $phone_raw = $request->input("phone"); 
        $phone = preg_replace('/[^0-9]/', '', $phone_raw);

        $phone_format = ltrim($phone, '7');

        $ac = substr($phone_format, 0, 3);
        $prefix = substr($phone_format, 3, 3);
        $suffix = substr($phone_format, 6, 2);
        $subsuffix = substr($phone_format, 8, 2);

        # создать из номера варианты:
        # raw: 79140050000
        # a) 89140050000
        # b) +79140050000
        # c) +7 (914) 005-24-41

        $phone_array = array();
        $phone_array[] = "7" . $phone_format;
        $phone_array[] = "8" . $phone_format;
        $phone_array[] = "+7" . $phone_format;
        $phone_array[] = "+7 ({$ac}) {$prefix}-{$suffix}-{$subsuffix}";
        $phone_array[] = "8 ({$ac}) {$prefix}-{$suffix}-{$subsuffix}";
        
        $user = RootUser::whereIn('phone', $phone_array)->first();

        if ( is_null($user) ) 
        {
            return response()->json(array('errors'=> array("Аккаунт с указанным номером телефона не найден, необходима регистрация.")), 422);
        }

        # confirm if sms code is correct
        $where = array();
        $where[] = array("phone", $phone);
        $where[] = array("code", $smscode);
        $where[] = array("user_id", $user->id);
        # проверка срока жизни, код живет 5 минут, после считается не активным
        $created_at_limit = null;
        $where[] = array("created_at", ">", Carbon::now()->subMinutes(4));
        $smscode_exist = SmscodeModel::where($where)->first();

        if ( is_null($smscode_exist) ) 
        {
            return response()->json(array('errors'=> array("Код не найден или просрочен.")), 422);
        }

        # код принят, выполняем авторизацию пользователя
        Auth::login($user);

        # код одноразовый, будет удален
        $is_delete = $smscode_exist->delete();

        return response()->json(array('messages'=> array("Вход выполнен, страница будет перезагружена.")), 200);
    }
    /**
     * подтверждение регистрации 
     */
    public function verify(Request $request)
    {
        # валидация наличия телефона
        $validated = $request->validate([
            'phone' => 'required',
            'smscode' => 'required',
            'type' => 'required',
        ], [
            'smscode.required' => 'Смс код обязателен для авторизации.',
            'type.required' => 'Не хватает критических данных для выполнения запроса, попробуйте позже.',
        ]);
        $smscode = $request->input("smscode"); 
        $phone_raw = $request->input("phone"); 
        $phone = preg_replace('/[^0-9]/', '', $phone_raw);

        $phone_format = ltrim($phone, '7');

        $ac = substr($phone_format, 0, 3);
        $prefix = substr($phone_format, 3, 3);
        $suffix = substr($phone_format, 6, 2);
        $subsuffix = substr($phone_format, 8, 2);

        # создать из номера варианты:
        # raw: 79140050000
        # a) 89140050000
        # b) +79140050000
        # c) +7 (914) 005-24-41

        $phone_array = array();
        $phone_array[] = "7" . $phone_format;
        $phone_array[] = "8" . $phone_format;
        $phone_array[] = "+7" . $phone_format;
        $phone_array[] = "+7 ({$ac}) {$prefix}-{$suffix}-{$subsuffix}";
        $phone_array[] = "8 ({$ac}) {$prefix}-{$suffix}-{$subsuffix}";
        
        # своя модель для каждого типа пользователя
        $where = array();
        $where[] = array("phone", $phone);

        $user = RootUser::whereIn('phone', $phone_array)->first();

        if ( is_null($user) ) 
        {
            return response()->json(array('errors'=> array("Аккаунт с указанным номером телефона не найден, необходима регистрация.")), 422);
        }

        # confirm if sms code is correct
        $where = array();
        $where[] = array("phone", $phone);
        $where[] = array("code", $smscode);
        $where[] = array("user_id", $user->id);
        # проверка срока жизни, код живет 5 минут, после считается не активным
        $created_at_limit = null;
        $where[] = array("created_at", ">", Carbon::now()->subMinutes(4));
        $smscode_exist = SmscodeModel::where($where)->first();

        if ( is_null($smscode_exist) ) 
        {
            return response()->json(array('errors'=> array("Код не найден или просрочен.")), 422);
        }

        # код принят, выполняем авторизацию пользователя
        Auth::login($user);

        # код одноразовый, будет удален
        $is_delete = $smscode_exist->delete();

        return response()->json(array('messages'=> array("Аккаунт создан, вы будете перенаправлены в личный кабинет.")), 200);
    }
    private function sms($config, $content)
    {
        return;
        # request to mango
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $config['endpoint'], array(
            'query' => array(
                'code' => "03f7ffb5-7fb0-4475-95f5-67b5f7fef801", 
                'Source' => "Other", 
                'API_key' => "s22j8l3x9gmkzzf6vi2mb331xfosdgmu", 
                'Action' => "Send_SMS", 
                'EmployeeNUM' => 118, 
                'TelNumbr' => "({$content['number']})",
                'SMSText' => $content['textarea'],
            )
        ));

        $statusCode = $response->getStatusCode();
        $content = $response->getBody();

        return;
    }

}
