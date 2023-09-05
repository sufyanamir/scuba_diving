<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LanguageController;


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
Route::middleware(['customauth'])->group(function () {

// company
Route::match(['post'], 'company/store', [CompanyController::class, 'addCompany']);
Route::get('/company', [CompanyController::class, 'index']);
Route::get('/company/delete/{id}', [CompanyController::class,'destroy'])->name('company.delete');
// company

// staff
Route::match(['post'], 'staff/store', [StaffController::class, 'addStaff']);
Route::get('/staff', [StaffController::class,'index']);
Route::get('/staff/delete/{id}', [StaffController::class,'destroy'])->name('staff.delete');
Route::post('/staff/update/{id}', [StaffController::class, 'update']);
// staff

// Customer
Route::match(['post'], 'customers/store', [CustomerController::class, 'addCustomer']);
Route::get('/customers', [CustomerController::class,'index']);
Route::get('/customers/delete/{id}', [CustomerController::class,'destroy'])->name('customer.delete');
// Customer


Route::get('/dashboard', [DashboardController::class, 'index']);

Route::match(['post','get'],'/lang_change', [LanguageController::class, 'lang_change']);

});


Route::get('/register',function(){
    return view('register');
});

Route::get('/product', function () {
    return view('products');
});

Route::get('/services', function () {
    return view('services');
});

//Auth
Route::get('/', [LoginController::class,'index']);
Route::post('/', [LoginController::class,'login']);
Route::match(['get', 'post'], '/logout', [LoginController::class,'logout']);
//Auth

