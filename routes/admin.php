<?php

# Панель управления
use Illuminate\Support\Facades\Route;
# раздел "Контент страница"
use App\Http\Controllers\Panel\Page\PageController as PanelPageController;
use App\Http\Controllers\Panel\Page\MenuController as PanelMenuController;
# раздел "Пользователи"
use App\Http\Controllers\Panel\Users\UsersController;
# раздел "Договора для специалистов"
use App\Http\Controllers\Panel\Specialist\SpecialistDocumentController;
# раздел "Аккаунты пользователей"
use App\Http\Controllers\Panel\Account\AccountController;
# раздел "Формы и настройки сайта"
use App\Http\Controllers\Panel\Settings\ItemController as SettingsController;
use App\Http\Controllers\Panel\Settings\CityController as SettingCityController;
# раздел "Галерея (фото)"
use App\Http\Controllers\Panel\Gallery\FileController;
# раздел "Галерея (фото)"
use App\Http\Controllers\Panel\Gallery\GalleryController;
# раздел "Галерея (аудио)"
use App\Http\Controllers\Panel\Gallery\AudioController;
# раздел "Галерея (видео)"
use App\Http\Controllers\Panel\Gallery\VideoController;
# раздел "Анкета"
use App\Http\Controllers\Panel\Docs\DocsController;

# раздел "Услуги"
use App\Http\Controllers\Panel\Service\ItemController as PanelServiceController;
# раздел "Портрет"
use App\Http\Controllers\Panel\Service\PortraitController as PanelPortraitController;

# controllers: yapodbor.dzen-digital.ru

# раздел "База проверенных автомобилей"
use App\Http\Controllers\Panel\Baseauto\ItemController as PanelAutoController;
use App\Http\Controllers\Panel\Baseauto\CategoryController as PanelAutoCategoryController;

# раздел "Блог"
use App\Http\Controllers\Panel\Blog\ItemController as PanelBlogController;
use App\Http\Controllers\Panel\Blog\CategoryController as PanelBlogCategoryController;

# раздел "Влог"
// use App\Http\Controllers\Panel\Vlog\ItemController as PanelVlogController;
// use App\Http\Controllers\Panel\Vlog\CategoryController as PanelVlogCategoryController;

# раздел "Отзывы"
use App\Http\Controllers\Panel\Review\ItemController as PanelReviewController;

# раздел "Оплаты" (тестовый)
use App\Http\Controllers\Panel\Payment\PayKeeperController;

# раздел "Контакты"
use App\Http\Controllers\Panel\Contacts\ContactsController as PanelContactsController;


Route::middleware(['auth', 'verified', 'role:admin,manager'])->group(function () {

    # разделы: base
    Route::get('/admin', function () {
        # панель управления: главная
        return view('/panel/index/index');
    })->name('panel');  


    # раздел "Анкета"
    Route::resource('/component/docs', DocsController::class);
    Route::post('/component/docs/listdoc/{id}', [DocsController::class, 'listdoc']);
    Route::post('/component/docs/createdoc', [DocsController::class, 'createdoc']);
    Route::delete('/component/docs/destroy/{id}', [DocsController::class, 'destroydoc']);
    

    Route::middleware(['role:admin'])->group(function () {
        # модуль "Контент страница"
        Route::resource('/component/page', PanelPageController::class)->names([
            'index' => 'component.page',
            'store' => 'component.page.store',
            'update' => 'component.page.update',
        ]);
        Route::resource('/component/menu', PanelMenuController::class)->names([
            'index' => 'component.menu',
            'store' => 'component.menu.store',
            'update' => 'component.menu.update',
        ]);
        Route::post('/component/menu/sort', [PanelMenuController::class, 'sort']); # маршрут для сортировки пунктов меню

        # модуль "Настройки"
        Route::resource('/component/settings', SettingsController::class);
        
        # Раздел "Контакты"
        Route::resource('/component/contacts', PanelContactsController::class);
    
        # модуль "Пользователи"
        Route::resource('/component/users', UsersController::class)->names([
            'index' => 'component.users',
            'update' => 'component.users.update',
        ]);
        Route::post('/component/users/role/{id}', [UsersController::class, 'role']); # маршрут сохранения привязки роли
    });



    # модуль "Договора для специалистов"
    Route::resource('/component/specialistdocs', SpecialistDocumentController::class)->names([
        'index' => 'component.specialistdocs',
        'update' => 'component.specialistdocs.update',
    ]);

    # модуль "Аккаунты пользователей"
    Route::resource('/component/account', AccountController::class)->names([
        'index' => 'component.account',
        'update' => 'component.account.update',
    ]);
    Route::post('/component/account/auto', [AccountController::class, 'auto']);

    # Раздел "База проверенных автомобилей"
    Route::resource('/component/baseauto', PanelAutoController::class);
    Route::post('/component/baseauto/parameter', [PanelAutoController::class, "parameter"]);
    Route::post('/component/baseauto/model', [PanelAutoController::class, "model"]);
    Route::post('/component/baseauto/generation', [PanelAutoController::class, "generation"]);
    Route::post('/component/baseauto/body', [PanelAutoController::class, "body"]);
    Route::put('/component/baseauto/parameter/{id}', [PanelAutoController::class, "parameterUpdate"]);
    Route::post('/component/baseauto/gallery', [PanelAutoController::class, 'gallery']);

    # Раздел "Блог"
    Route::resource('/component/blog', PanelBlogController::class);
    Route::post('/component/blog/video', [PanelBlogController::class, 'video']);
    Route::post('/component/blog/gallery', [PanelBlogController::class, 'gallery']);
    Route::post('/component/blog/sort', [PanelBlogController::class, 'sort']); # маршрут для сортировки
    Route::resource('/component/blog/category', PanelBlogCategoryController::class);
    Route::post('/component/blog/category/sort', [PanelBlogCategoryController::class, 'sort']); # маршрут для сортировки

    # Раздел "Влог"
    // Route::resource('/component/vlog', PanelVlogController::class);
    // Route::post('/component/vlog/video', [PanelVlogController::class, 'video']);
    // Route::post('/component/vlog/gallery', [PanelVlogController::class, 'gallery']);
    # маршрут для сортировки
    // Route::post('/component/vlog/sort', [PanelVlogController::class, 'sort']); 
    // Route::resource('/component/vlog/category', PanelVlogCategoryController::class);
    # маршрут для сортировки
    // Route::post('/component/vlog/category/sort', [PanelVlogCategoryController::class, 'sort']); 

    # Раздел "Отзывы"
    Route::resource('/component/reviews', PanelReviewController::class);

    # Раздел "Оплата" (тестовый раздел)
    Route::resource('/component/payment', PayKeeperController::class);

    # раздел "Галерея"
    Route::resource('/component/file', FileController::class);
    Route::resource('/component/gallery', GalleryController::class);
    Route::resource('/component/video', VideoController::class);
    Route::resource('/component/audio', AudioController::class);

});