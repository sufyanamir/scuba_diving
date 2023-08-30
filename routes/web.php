<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\CustomerController;
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

// company
Route::match(['post'], 'company/store', [CompanyController::class, 'addCompany']);
Route::get('/company', [CompanyController::class, 'index']);
// company

// staff
Route::match(['post'], 'staff/store', [StaffController::class, 'addStaff']);



Route::get('/register',function(){
    return view('register');
});

Route::get('/dashboard',[TestController::class,'index']);
// Route::get('/company', function () {
//     return view('company');
// });

// Route::get('/', [CompanyController::class,'index']);


Route::get('/product', function () {
    return view('products');
});

Route::get('/services', function () {
    return view('services');
});

// Route::get('/customers', function () {
//     return view('customers');
// });

Route::get('/staff', [StaffController::class,'index']);
Route::get('/staff/delete/{id}', [StaffController::class,'destroy'])->name('staff.delete');
// staff

// Customer
Route::match(['post'], 'customers/store', [CustomerController::class, 'addCustomer']);
Route::get('/customers', [CustomerController::class,'index']);
Route::get('/customers/delete/{id}', [CustomerController::class,'destroy'])->name('customer.delete');
// Customer
Route::get('/', [LoginController::class,'index']);

Route::post('/dashboard', [LoginController::class,'login']);
