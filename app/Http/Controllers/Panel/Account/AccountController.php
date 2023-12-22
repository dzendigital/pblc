<?php

namespace App\Http\Controllers\Panel\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Account;

use App\Models\Account\Payment;
use App\Models\Account\Report;

use App\Http\Requests\Panel\AccountRequest;
use App\Repositories\Panel\AccountRepository as ItemRepository;


# работа с файлами
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Hash;

use Illuminate\Auth\Events\Registered;

class AccountController extends Controller
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
    public function index()
    {
        $response = [
            'items' => $this->itemRepository->all(),
            'roles' => Role::all()
        ];
        # 26.10.2022 - запрашиваем только записи, которые можно изменять
        return view("panel/account/index", $response);
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
    public function store(AccountRequest $request)
    {
        # валидация входящих полей
        $validatedData = $request->validated();


        # привязка к роли
        $role = Role::where('slug', "account")->first();


        # создание объекта с данными
        $item = new User($validatedData);
        $item['email_verified_at'] = now();
        # $item['email_verified_at'] = null;
        $item['password'] = Hash::make($validatedData['password']);

        
        #сохраняем привязку роли
        $status = $item->save();
        $item->roles()->attach($role);
        
        # создаем профиль Account
        $account_model = array(
            "user_id" => $item->id,
            "title" => $item->email,
            "phone" => null,
            "city" => null,
            "email" => $item->email,
            "is_sms" => null,
            "is_email" => null,
            "is_visible" => 1,
            "sort" => null,
        );
        $account_a = new Account($account_model);
        $account_a->save();

        # отправляем письмо подтверждение
        event(new Registered($item));
        // $request->user()->sendEmailVerificationNotification();

        # после создания и сохранения отношений - запросим новосозданный объект и вернем его для добавления во view
        $response = array(
            'result' => array(
                'status' => $status,
                'items' => $this->itemRepository->all(),
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
        $validatedData = $request->only(["id", "email", "password", "report", "payment"]);

        $item = $this->itemRepository->byId($id);
        $user = $item->user;
        $item['email'] = $validatedData['email'];
        $user['email'] = $validatedData['email'];
        if( $validatedData['password'] != null ){
            $user['password'] = Hash::make($validatedData['password']);
        }
        $user->save();


        # удаление всех репортор
        foreach ( $item->report as $key => $report ) {
            # $report = Report::where("id", $value['id'])->first();
            $is_exists = is_null($report) ? null : Storage::exists($report->url);
            if ( $is_exists ) 
            {
                $result_delete = Storage::delete($report->url);
            }
            $report->delete();
        }
        # сохранение файлов: report
        if ( $request->input('report') != null ) {
            # dd(__METHOD__, 123);
            # добавляем к $item ссылку на gallery && сохраняем relation gallery
            foreach ( $request->input("report") as $key => $value ) {
                if( !isset($value['id']) ){
                }
                # в базе нет (id в запросе отсутствует) - добавляем
                $tmp = new Report($value);
                # сохраняем
                $item->report()->save($tmp);   
            }
        }
        # удаление всех payment
        foreach ( $item->payment as $key => $payment ) {
            # $payment = Report::where("id", $value['id'])->first();
            $is_exists = is_null($payment) ? null : Storage::exists($payment->url);
            if ( $is_exists ) 
            {
                $result_delete = Storage::delete($payment->url);
            }
            $payment->delete();
        }
        # сохранение файлов: payment
        if ( $request->input('payment') != null ) {
            # добавляем к $item ссылку на gallery && сохраняем relation gallery
            foreach ( $request->input("payment") as $key => $value ) {
                if( !isset($value['id']) ){
                }
                # в базе нет (id в запросе отсутствует) - добавляем
                $tmp = new Payment($value);
                # сохраняем
                $item->payment()->save($tmp);   
            }
        }


        # сохранение: пользователь, аккаунт
        $status = $item->save();

        $response = array(
            'result' => array(
                'status' => $status,
                'items' => $this->itemRepository->all(),
            ),
        );
        
        return $response;
    }
    /**
     * Approve auto of account.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function auto(Request $request)
    {
        $validated = $request->only(["auto_id", "is_approved", "account_id"]);
        $auto = $this->itemRepository->accountAuto($validated['account_id'], $validated['auto_id']);

        $auto->is_approved = $validated["is_approved"];
        if ( $validated["is_approved"] == 1 ) {
            $auto->approve_created_at = date("Y-m-d H:i:s");
        }else{
            $auto->approve_created_at = null;
        }

        # сохранение: пользователь, аккаунт
        $status = $auto->save();
        
        $response = array(
            'result' => array(
                'status' => $status,
                'items' => $this->itemRepository->all(),
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
        # $result = Account::find($id)->delete();
        $item = Account::with("user")->find($id);
        $result = $item->user->delete();

        $response = array(
            'result' => array(
                'status' => $result,
            ),
        );
        
        return $response;
    }
}
