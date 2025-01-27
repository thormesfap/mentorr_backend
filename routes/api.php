<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'auth',
        'namespace' => 'App\Http\Controllers'
    ],
    function () {
        Route::get('me', 'AuthController@me');
        Route::post('login', 'AuthController@login');
        Route::post('logout', 'AuthController@logout');
        Route::post('refresh', 'AuthController@refresh');
        Route::post('register', 'AuthController@register');
        Route::patch('promote/{user}', 'AuthController@promote')->middleware('isAdmin');
    }
);

Route::group(['prefix' => 'empresa', 'namespace' => 'App\Http\Controllers'], function () {
    Route::get('/', 'EmpresaController@index');
    Route::get('/{id}', 'EmpresaController@show');
    Route::post('/', 'EmpresaController@store');
    Route::patch('/{id}', 'EmpresaController@update');
    Route::delete('/{id}', 'EmpresaController@destroy');
});
