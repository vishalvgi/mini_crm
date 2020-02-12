<?php

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

Auth::routes(['register' => false]);

Route::resource('home', 'CompanyController');

Route::resource('company.employee', 'EmployeeController');


Route::get('/lang/{locale}', function ($locale, Request $request) {
    Session::put('locale', $locale);
//    session(['locale' => $locale]);
    return redirect()->back();
});
