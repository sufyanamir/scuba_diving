<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\TestController;


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

Route::match(['post'], 'company/store', [CompanyController::class, 'addCompany']);
Route::match(['post'], 'staff/store', [StaffController::class, 'addStaff']);


Route::get('/', function () {
    return view('login');
});
Route::get('/register',function(){
    return view('register');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/company', [CompanyController::class,'index']);


Route::get('/product', function () {
    return view('products');
});

Route::get('/staff', [StaffController::class,'index']);
Route::get('/staff/delete/{id}', [StaffController::class,'destroy'])->name('staff.delete');

Route::get('/services', function () {
    return view('services');
});

Route::get('/customers', function () {
    return view('customers');
});