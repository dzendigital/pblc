<?php
use Illuminate\Support\Facades\Route;




# маршруты модуля контент страница: в текущий метод попадают страницы, которые соответствуют условиям.
use App\Http\Controllers\Client\Page\PageController as ClientPageController;
use App\Http\Controllers\Client\Docs\DocsController;
use App\Models\Servey\Anketa;
use App\Models\Blog\Item as Blog;

Route::get('/{url}', [ClientPageController::class, 'view'])->where('url', '^(?!(admin|component|logout|login|register|verify-email|forgot-password|reset-password|confirm-password|storage|servey|account|article)(\/|$))[A-Za-z0-9+-_\/]+');

Route::get("/", function () {
	return view("/client/index");
});
Route::get("/article/{blog:slug}", function (Blog $blog) {
	return view("/client/article", [
		'item' => $blog
	]);
});
Route::get("/policy", function () {
	return view("/client/index");
});
Route::get("/servey/{id}", function ($id) {
	$anketa = Anketa::where("id", $id)->with(['document'])->firstOrFail();
	return view("/client/anketa", [
		'anketa' => $anketa
	]);
});

Route::get("/storage/{id}", [DocsController::class, 'storage']);

require __DIR__.'/admin.php';
require __DIR__.'/account.php';
require __DIR__.'/auth.php';
require __DIR__.'/form.php';
