<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/user', [UserController::class, 'get']);

Route::post('/register-public-key', [UserController::class, 'registerPublicKey']);

Route::any('{catchall}', function () {
    response()->json(['Endpoint not found'], 404)->send();
})->where('catchall', '(.*)');

Route::get('/', function() {
    response()->json(['Endpoint not found'], 404)->send();
});
