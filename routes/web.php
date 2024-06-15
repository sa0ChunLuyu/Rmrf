<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
$admin_path = 'Admin';
Route::post("api/WanLiu/Upload/file", [\App\Http\Controllers\WanLiuController::class, 'upload']);
Route::post("api/$admin_path/WanLiu/token", [\App\Http\Controllers\WanLiuController::class, 'token']);
Route::post("api/$admin_path/Admin/reset_password", [\App\Http\Controllers\AdminController::class, 'reset_password']);
Route::post("api/$admin_path/Config/create", [\App\Http\Controllers\ConfigController::class, 'create']);
Route::post("api/$admin_path/Config/update", [\App\Http\Controllers\ConfigController::class, 'update']);
Route::post("api/$admin_path/Config/delete", [\App\Http\Controllers\ConfigController::class, 'delete']);
Route::post("api/$admin_path/Config/list", [\App\Http\Controllers\ConfigController::class, 'list']);
Route::post("api/$admin_path/AdminAuth/select", [\App\Http\Controllers\AdminAuthController::class, 'select']);
Route::post("api/$admin_path/AdminAuth/create", [\App\Http\Controllers\AdminAuthController::class, 'create']);
Route::post("api/$admin_path/AdminAuth/update", [\App\Http\Controllers\AdminAuthController::class, 'update']);
Route::post("api/$admin_path/AdminAuth/delete", [\App\Http\Controllers\AdminAuthController::class, 'delete']);
Route::post("api/$admin_path/AdminAuth/list", [\App\Http\Controllers\AdminAuthController::class, 'list']);
Route::post("api/$admin_path/RequestLog/txt", [\App\Http\Controllers\RequestLogController::class, 'txt']);
Route::post("api/$admin_path/RequestLog/list", [\App\Http\Controllers\RequestLogController::class, 'list']);
Route::post("api/$admin_path/Upload/list", [\App\Http\Controllers\UploadController::class, 'list']);
Route::post("api/$admin_path/Upload/delete", [\App\Http\Controllers\UploadController::class, 'delete']);
Route::post("api/$admin_path/Upload/search", [\App\Http\Controllers\UploadController::class, 'search']);
Route::post("api/$admin_path/Admin/create", [\App\Http\Controllers\AdminController::class, 'create']);
Route::post("api/$admin_path/Admin/update", [\App\Http\Controllers\AdminController::class, 'update']);
Route::post("api/$admin_path/Admin/delete", [\App\Http\Controllers\AdminController::class, 'delete']);
Route::post("api/$admin_path/Admin/list", [\App\Http\Controllers\AdminController::class, 'list']);
Route::post("api/$admin_path/AdminAuthGroup/select", [\App\Http\Controllers\AdminAuthGroupController::class, 'select']);
Route::post("api/$admin_path/AdminAuthGroup/create", [\App\Http\Controllers\AdminAuthGroupController::class, 'create']);
Route::post("api/$admin_path/AdminAuthGroup/update", [\App\Http\Controllers\AdminAuthGroupController::class, 'update']);
Route::post("api/$admin_path/AdminAuthGroup/delete", [\App\Http\Controllers\AdminAuthGroupController::class, 'delete']);
Route::post("api/$admin_path/AdminAuthGroup/list", [\App\Http\Controllers\AdminAuthGroupController::class, 'list']);
Route::post("api/$admin_path/AdminAuth/choose", [\App\Http\Controllers\AdminAuthController::class, 'choose']);
Route::post("api/$admin_path/Upload/image", [\App\Http\Controllers\UploadController::class, 'image']);
Route::post("api/$admin_path/Admin/update_self", [\App\Http\Controllers\AdminController::class, 'update_self']);
Route::post("api/$admin_path/AdminAccount/change_password", [\App\Http\Controllers\AdminAccountController::class, 'change_password']);
Route::post("api/$admin_path/AdminAuth/menu", [\App\Http\Controllers\AdminAuthController::class, 'menu']);
Route::post("api/$admin_path/Admin/quit", [\App\Http\Controllers\AdminController::class, 'quit']);
Route::post("api/$admin_path/Admin/status", [\App\Http\Controllers\AdminController::class, 'status']);
Route::post("api/$admin_path/Admin/info", [\App\Http\Controllers\AdminController::class, 'info']);
Route::post("api/$admin_path/Admin/login", [\App\Http\Controllers\AdminController::class, 'login']);
Route::post("api/$admin_path/ImageCaptcha/create", [\App\Http\Controllers\ImageCaptchaController::class, 'create']);
Route::post("api/$admin_path/Config/get", [\App\Http\Controllers\ConfigController::class, 'get']);
Route::post("api/get", [\App\Http\Controllers\ApiMapController::class, 'get']);
Route::any('api/yo', \App\Http\Controllers\YoController::class);
Route::get('/', function () {
  return view('welcome');
});
