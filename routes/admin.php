<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\LoginController;

Route::post('login', [LoginController::class, 'login']);

Route::group(['middleware' => 'auth:admin'], function () {
    Route::post('logout', [LoginController::class, 'logout']);
    Route::post('refresh', [LoginController::class, 'refresh']);
    Route::post('addUsers', [LoginController::class, 'addUsers']);
    Route::get('getUserInfo', [UserController::class, 'getUserInfo']);
});
