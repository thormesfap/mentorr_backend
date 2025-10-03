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
        Route::patch('profile', 'AuthController@profile')->middleware('logged');
        Route::post('profilePicture', 'AuthController@profilePicture')->middleware('logged');
        Route::patch('promote/{user}', 'AuthController@promote')->middleware('isAdmin');
        Route::post('forgot-password', 'Auth\PasswordResetLinkController@store');
        Route::post('test-email', 'Auth\PasswordResetLinkController@testEmail');
        Route::post('reset-password', 'Auth\NewPasswordController@store');
    }
);

Route::group(['prefix' => 'empresa', 'namespace' => 'App\Http\Controllers',  'middleware' => ['isAdmin']], function () {
    Route::get('/', 'EmpresaController@index')->withoutMiddleware('isAdmin');
    Route::get('/{id}', 'EmpresaController@show');
    Route::post('/', 'EmpresaController@store');
    Route::patch('/{id}', 'EmpresaController@update');
    Route::delete('/{id}', 'EmpresaController@destroy');
});

Route::group(['prefix' => 'cargo', 'namespace' => 'App\Http\Controllers', 'middleware' => ['isAdmin']], function () {
    Route::get('/', 'CargoController@index')->withoutMiddleware('isAdmin');
    Route::get('/{id}', 'CargoController@show');
    Route::post('/', 'CargoController@store');
    Route::patch('/{id}', 'CargoController@update');
    Route::delete('/{id}', 'CargoController@destroy');
});

Route::group(['prefix' => 'area', 'namespace' => 'App\Http\Controllers', 'middleware' => ['isAdmin']], function () {
    Route::get('/', 'AreaController@index')->withoutMiddleware('isAdmin');
    Route::get('/{id}', 'AreaController@show');
    Route::post('/', 'AreaController@store');
    Route::patch('/{id}', 'AreaController@update');
    Route::delete('/{id}', 'AreaController@destroy');
});

Route::group(['prefix' => 'habilidade', 'namespace' => 'App\Http\Controllers', 'middleware' => ['isAdmin']], function () {
    Route::get('/', 'HabilidadeController@index')->withoutMiddleware('isAdmin');
    Route::get('/{id}', 'HabilidadeController@show');
    Route::post('/', 'HabilidadeController@store');
    Route::patch('/{id}', 'HabilidadeController@update');
    Route::delete('/{id}', 'HabilidadeController@destroy');
    Route::get('/habilidade/mentor/{idMentor}', 'HabilidadeController@doMentor')->withoutMiddleware('isAdmin');
});

Route::group(['prefix' => 'mentor', 'namespace' => 'App\Http\Controllers'], function () {
    Route::get('/', 'MentorController@index');
    Route::get('/{id}', 'MentorController@show');
    Route::post('/', 'MentorController@store')->middleware('logged');
    Route::patch('/{mentor}', 'MentorController@update')->middleware('logged');
    Route::delete('/{mentor}', 'MentorController@destroy')->middleware('isAdmin');
    Route::patch('/habilidade/{habilidade}', 'MentorController@addHabilidade')->middleware('logged');
    Route::patch('/{mentor}/habilidades', 'MentorController@setHabilidades')->middleware('logged');
    Route::patch('/cargo/{cargo}', 'MentorController@setCargo')->middleware('logged');
    Route::patch('/empresa/{empresa}', 'MentorController@setEmpresa')->middleware('logged');
    Route::get('/minhas', 'MentorController@minhasMentorias')->middleware('logged');
    Route::post('/habilidade/{id}/certificado', 'MentorController@sendCertificado')->middleware('logged');
});

Route::group(['prefix' => 'mentoria', 'namespace' => 'App\Http\Controllers'], function () {
    Route::get('/', 'MentoriaController@index')->middleware('isAdmin');
    Route::get('/usuario', 'MentoriaController@minhasMentorias')->middleware('logged');
    Route::get('/mentor', 'MentoriaController@mentoriasDadas')->middleware('logged');
    Route::get('/{id}', 'MentoriaController@show')->middleware('logged');
    //Route::post('/', 'MentoriaController@store')->middleware('logged');
    Route::patch('/{id}', 'MentoriaController@update')->middleware('logged');
    Route::delete('/{id}', 'MentoriaController@destroy')->middleware('isAdmin');
});

Route::group(['prefix' => 'sessao_mentoria', 'namespace' => 'App\Http\Controllers'], function () {
    Route::get('/', 'SessaoMentoriaController@index')->middleware('isAdmin');
    Route::get('/{id}', 'SessaoMentoriaController@show')->middleware('logged');
    Route::post('/', 'SessaoMentoriaController@store')->middleware('logged');
    Route::patch('/{id}', 'SessaoMentoriaController@update')->middleware('logged');
    Route::delete('/{id}', 'SessaoMentoriaController@destroy')->middleware('isAdmin');
    Route::patch('/{id}/avaliar', 'SessaoMentoriaController@avaliar')->middleware('logged');
});

Route::group(['prefix' => 'solicitacao_mentoria', 'namespace' => 'App\Http\Controllers'], function () {
    Route::post('/', 'SolicitacaoMentoriaController@store')->middleware('logged');
    Route::patch('/{solicitacao}', 'SolicitacaoMentoriaController@update')->middleware('logged');
    Route::get('/mentor', 'SolicitacaoMentoriaController@listarPorMentor')->middleware('logged');
    Route::get('/usuario', 'SolicitacaoMentoriaController@listarPorUsuario')->middleware('logged');
});
