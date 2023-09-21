<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\ApiController;

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


Route::middleware(['api.auth'])->group(function () {
    Route::get('/getUser', [ApiController::class, 'gteUserDetails']);
    
});
//customer APIs
Route::post('/addCustomer', [ApiController::class, 'addCustomer']);
Route::post('/customer/update/{id}', [ApiController::class, 'updateCustomer']);
Route::match(['post', 'get'], '/customer/delete/{id}', [ApiController::class, 'deleteCustomer']);
//customer APIs

//staff APIs
Route::post('/addStaff', [ApiController::class, 'addStaff']);
Route::post('/staff/update/{id}', [ApiController::class, 'updateStaff']);
Route::match(['post', 'get'], '/staff/delete/{id}', [ApiController::class, 'deleteStaff']);
Route::get('/getStaff', [ApiController::class, 'getStaff']);
//staff APIs

//staff APIs
Route::post('/addService', [ApiController::class, 'addService']);
Route::post('/service/update/{id}', [ApiController::class, 'updateService']);
Route::match(['post', 'get'], '/service/delete/{id}', [ApiController::class, 'deleteService']);
Route::get('/getService', [ApiController::class, 'getService']);
//staff APIs


Route::get('/adminDashboard', [ApiController::class, 'adminDashboard']);

//authentication
Route::match(['post'], '/login', [ApiController::class, 'login']);
Route::match(['post'], '/register', [ApiController::class, 'makeRequest']);
//authentication