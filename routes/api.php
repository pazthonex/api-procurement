<?php

use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/test', function(){
    return 'test';
}); 


Route::post('login', [UsersController::class, 'login']);
Route::post('loginbygoogle', [UsersController::class, 'login_using_google']);

Route::post('users/store', [UsersController::class, 'store']);
Route::post('createtoken', [UsersController::class, 'createtoken']);

Route::middleware(['auth:sanctum','authemployee'])->group(function (){
  #for employee access api
  Route::post('/employee' ,function(){
   return 'employee';
   });
});


Route::middleware(['auth:sanctum','authsuperadmin'])->group(function (){
   #superadmin access api
   Route::post('/superadmin' ,function(){
      return 'superadmin';
      });
 
 });
Route::middleware(['auth:sanctum'])->group(function (){

   
   // Route::post('createtoken/', [UsersController::class, 'createtoken']);
   Route::post('logout', [UsersController::class, 'logout']);
   Route::post('logout', [UsersController::class, 'logout']);

});






