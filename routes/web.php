<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LiveOddsController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/live-odds', [LiveOddsController::class, 'index']);

require __DIR__.'/api.php';
