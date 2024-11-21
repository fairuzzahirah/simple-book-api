<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookController;


Route::middleware('throttle:api')->group(function () {
    Route::apiResource('books', BookController::class);
});


