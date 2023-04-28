<?php
use App\Http\Controllers\VehiculesController;
use App\Http\Controllers\UserController;
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

Route::get('/', function(){
 return ["Hello" => "API laravel"];
});
 
//ROUTES UTILISATEURS
 Route::post("/user/register", [UserController::class, "register"]);
 Route::post("/user/login", [UserController::class, "login"]);
 Route::get("/user/listUsers", [UserController::class, "listUsers"]);

 //ROUTES VEHICULES
 Route::get('cars', [VehiculesController::class,"index"]);
 Route::post('cars/new', [VehiculesController::class,"store"]);
 Route::get('cars/show/{id}', [VehiculesController::class,"show"]);
 Route::put('cars/edit/{id}', [VehiculesController::class,"update"]);
 Route::delete('cars/delete/{id}', [VehiculesController::class,"destroy"]);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
