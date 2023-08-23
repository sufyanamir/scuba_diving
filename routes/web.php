<?php

use Illuminate\Support\Facades\Route;

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