<?php

namespace App\Http\Controllers\Client\Domoi\Home;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Http\Requests\Client\CallbackFormRequest;

use App\Mail\CallbackForm;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;

use App\Events\NewUserEvent;
use App\Models\Customers;
use App\Models\CustomerStatus;


class HomePageController extends Controller
{
    public function index()
    {
        # я не понял, где используется эта переменная:
        $current_category_path = '/';

        $fakeDataSlides = array(
            array(
                'name'  => 'Иван Иванов',
                'link'  => '#',
                'image' => '/public/files/domoi/img/user/avatar_1.jpg',
                'text'  => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book...",
            ),
            array(
                'name'  => 'Ольга Сидорова',
                'link'  => '#',
                'image' => '/public/files/domoi/img/user/avatar_2.jpg',
                'text'  => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book...",
            ),
            array(
                'name'  => 'Татьяна Новикова',
                'link'  => '#',
                'image' => '/public/files/domoi/img/user/avatar_3.jpg',
                'text'  => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book...",
            ),
        );
	    return view('client/domoi/home/index', [
            'fakeDataSlides' => $fakeDataSlides,
        ]);
    }


    
    public function landing()
    {
        return view('client/domoi/home/index');
    }

    public function web()
    {
	    return view('client/domoi/home/web');
    }
    
    public function callbackform1(CallbackFormRequest $request)
    {
        dd(__METHOD__, $request->all());
     //    $user = new Customers($request->validated());
     //    $user->status_id = CustomerStatus::select('id')->where('status', 'новый')->first()->id;
     //    event(new NewUserEvent($user));
    	// return redirect("/")->with("success", "Ваше письмо отправлено. Ожидайте ответа.");
    



    }   

    public function callbackform(CallbackFormRequest $request)
    {
        try {
            // $user = Customers::create($request->validated());
            $user = new Customers($request->validated());
            $user->status_id = CustomerStatus::select('id')->where('status', 'Контакт')->first()->id;
            #event(new NewUserEvent($user));

        } catch (\Illuminate\Validation\ValidationException $e ) {
        
            /**
             * Validation failed
             * Tell the end-user why
             */
            $arrError = $e->errors(); // Useful method - thank you Laravel
            /**
             * Compile a string of error-messages
             */
            foreach ($arrValid as $key=>$value ) {
                $arrImplode[] = implode( ', ', $arrError[$key] );
            }
            $message = implode(', ', $arrImplode);
            /**
             * Populate the respose array for the JSON
             */
            $arrResponse = array(
                'result' => 0,
                'reason' => $message,
                'data' => array(),
                'statusCode' => $e->status,
            );

        } catch (\Exception $ex) {

            $arrResponse = array(
                'result' => 0,
                'reason' => $ex->getMessage(),
                'data' => array(),
                'statusCode' => 404
            );

        } finally {
            if( isset($arrResponse) ){
                $response = response()->json([
                    'result' => false,
                    'reason' => $arrResponse['reason'],
                    'statusCode' => $arrResponse['statusCode'],
                    'data' => $arrResponse['data'],
                ]);
            }else{
                $response = response()->json([
                    'result' => true,
                    'messages' => [
                        'Сообщение отправлено.',
                        'Мы свяжемся с вами по указанным контактам.'
                    ],
                ]);
            }

            return isset($response) ? $response : null;
        }
    
        


    }
}
