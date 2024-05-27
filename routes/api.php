<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\UserAuthController;
use App\Http\Controllers\TransactionController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// api for the register, login , logout 
Route::post('register',[UserAuthController::class,'register']);
Route::post('login',[UserAuthController::class,'login']);
Route::post('logout',[UserAuthController::class,'logout'])
 ->middleware('auth:sanctum');

//list user 
Route::middleware('auth:sanctum')->get('users', [UserAuthController::class, 'listUsers']);
Route::middleware('auth:sanctum')->post('profile/image', [UserAuthController::class, 'updateProfileImage']);

Route::middleware('auth:sanctum')->group(function () {
    // Transaction routes
    Route::post('/transaction', [TransactionController::class, 'makeTransaction']);
});