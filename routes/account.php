<?php 


# Панель управления
use Illuminate\Support\Facades\Route;


# Личный кабинет пользователя: главная
use App\Http\Controllers\Client\Account\ProfileController as ClientProfileController;

# Личный кабинет пользователя: добавление блока
use App\Http\Controllers\Client\Account\BlogController as AccountBlogController;

# Личный кабинет пользователя: магазин
use App\Http\Controllers\Client\Account\ShopController as AccountShopController;

use App\Http\Controllers\Client\Account\ReportController as ClientProfileReportController;
use App\Http\Controllers\Client\Account\UploadController as ClientProfileUploadController;
use App\Http\Controllers\Client\Account\PaymentController as ClientProfilePaymentController;



Route::middleware(['auth', 'verified', 'role:account'])->group(function () {
    Route::get("/account", function () {
        return Redirect::to("/account/profile", 301); 
    });
    Route::get("/account/filesys", function () {
        return view("client/account/filesys/index");
    });
    Route::resource("/account/profile", ClientProfileController::class);
    Route::post("/account/profile/removeimage", [ClientProfileController::class, "removeimage"]);
    Route::post("/account/profile/passwordupdate", [ClientProfileController::class, "passwordupdate"]);

    Route::resource("/account/shop", AccountShopController::class);
    
    Route::resource("/account/blog", AccountBlogController::class);
    Route::post("/account/blog/filesetup", [AccountBlogController::class, "filesetup"]);
    Route::post("/account/blog/filedetach", [AccountBlogController::class, "filedetach"]);
    Route::post("/account/blog/refreshgallerytemplate", [AccountBlogController::class, "refreshgallerytemplate"]);


    Route::post("/account/upload/blog", function (\Illuminate\Http\Request $request){
        $fileName = $request->file('file')->getClientOriginalName();
        $path = $request->file('file')->storeAs('blog', uniqid(). '-' . $fileName);
        return response()->json(['location'=>"/storage/app/$path"]); 
    });
    
});
