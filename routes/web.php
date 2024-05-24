<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\User_webAuthController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});



// api for the register, login , logout 
Route::post('/register',[User_webAuthController::class,'register']);
Route::post('login',[User_webAuthController::class,'login']);
Route::post('logout',[User_webAuthController::class,'logout'])
 ->middleware('auth:sanctum');
