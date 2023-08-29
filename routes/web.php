<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\TestController;
Route::match(['post'], 'company/store', [TestController::class, 'addCompany']);

// Route::match(['post'], 'company/store', [CompanyController::class, 'addCompany']);

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
    return view('login');
});
Route::get('/register',function(){
    return view('register');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/company', function () {
    return view('company');
});

Route::get('/product', function () {
    return view('products');
});

Route::get('/staff', function () {
    return view('staff');
});

Route::get('/services', function () {
    return view('services');
});

Route::get('/customers', function () {
    return view('customers');
});