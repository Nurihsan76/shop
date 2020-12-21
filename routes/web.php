<?php

use Illuminate\Support\Facades\Auth;
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
    return view('welcome');
});

Auth::routes();
// Route::post('/login', )

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/produk', 'web\\ProdukController@index');
Route::get('/produk/{produk}', 'web\\ProdukController@show');
Route::post('/produk/kategori/', 'web\\ProdukController@kategori');
Route::delete('/produk/{produk}', 'web\\ProdukController@destroy');

Route::get('/user', 'web\\UserController@index');
Route::get('/user/{user}', 'web\\UserController@show');
Route::get('/user/{user}/edit', 'web\\UserController@edit')->name('user_edit');
Route::put('/user/{user}', 'web\\UserController@update');
Route::delete('/user/{user}', 'web\\UserController@destroy');

Route::get('/admin/{user}', 'web\\UserController@showAdmin');

Route::get('/tag', 'web\\TagController@index');
Route::get('/tag/create', 'web\\TagController@create');
Route::post('/tag', 'web\\TagController@store');
Route::get('/tag/{tag}/edit', 'web\\TagController@edit');
Route::put('/tag/{tag}', 'web\\TagController@update');
Route::delete('/tag/{tag}', 'web\\TagController@destroy');
