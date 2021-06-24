<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/user/{username}', [UserController::class, 'get']);


Route::get('/users', [UserController::class, 'list']);

Route::any('{catchall}', function () {
    response()->json(['Endpoint not found'], 404)->send();
})->where('catchall', '(.*)');

Route::get('/', function() {
    response()->json(['Endpoint not found'], 404)->send();
});
