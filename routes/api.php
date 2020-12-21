<?php

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// user
Route::post('register', 'UserController@register');
Route::post('login', 'UserController@login');
Route::put('update', 'UserController@update')->middleware('jwt.verify');
Route::delete('delete', 'UserController@destroy')->middleware('jwt.verify');
Route::get('book', 'BookController@book');
Route::post('logout', 'UserController@logout');

Route::get('bookall', 'BookController@bookAuth')->middleware('jwt.verify');
Route::get('user', 'UserController@getAuthenticatedUser')->middleware('jwt.verify');


// halaman utama
Route::get('produk', 'ProdukController@index');
Route::get('produk/{produk}', 'ProdukController@show');
Route::get('produk/{produk}/tag', 'ProdukController@getbytag_id');
Route::get('produk/{produk}/cari', 'ProdukController@cari');


// lapak penjual
Route::get('member', 'MemberController@index')->middleware('jwt.verify');
Route::get('member/{member}', 'MemberController@show')->middleware('jwt.verify');
Route::post('member', 'MemberController@store')->middleware('jwt.verify');
Route::put('member/{member}', 'MemberController@update')->middleware('jwt.verify');
Route::delete('member/{member}', 'MemberController@destroy')->middleware('jwt.verify');


// keranjang
Route::get('keranjang', 'PesananController@index')->middleware('jwt.verify');
Route::post('keranjang/{keranjang}', 'PesananController@keranjang')->middleware('jwt.verify');
Route::delete('keranjang/{keranjang}', 'PesananController@hapus')->middleware('jwt.verify');


// chect out
Route::get('checkout', 'PesananController@checkout')->middleware('jwt.verify');
Route::post('checkout', 'PesananController@konfirmasi')->middleware('jwt.verify');

// konfirmasi penjual
Route::get('konfirmasi', 'PesananController@showKonfirmasiPenjual')->middleware('jwt.verify');
Route::get('konfirmasi/{konfirmasi}', 'PesananController@konfirmasiPenjual')->middleware('jwt.verify');

// konfirmasi pembeli
Route::get('konfirmasiPembeli/{konfirmasi}', 'PesananController@konfirmasiPembeli')->middleware('jwt.verify');

// history
Route::get('history', 'PesananController@history')->middleware('jwt.verify');;
Route::get('history/{history}', 'PesananController@showhistory')->middleware('jwt.verify');;

// chat
Route::get('message', 'MessageController@index')->middleware('jwt.verify');
Route::get('message/{message}', 'MessageController@getMessage')->middleware('jwt.verify');
Route::post('message/{message}', 'MessageController@sendMessage')->middleware('jwt.verify');
Route::get('message/{message}/cari', 'MessageController@cari')->middleware('jwt.verify');
