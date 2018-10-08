<?php

use Illuminate\Http\Request;

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

Route::match(['get', 'post'], '/index', 'employees@index');

Route::match(['get', 'post'], '/graph', 'employees@graph');

Route::match(['get', 'post'], '/test', 'employees@test');
