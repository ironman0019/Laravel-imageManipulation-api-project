<?php

use App\Http\Controllers\V1\AlbumController;
use App\Http\Controllers\V1\ImageManipulationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function() {

    Route::prefix('v1')->group(function() {
        Route::apiResource('album', AlbumController::class);
        Route::get('image', [ImageManipulationController::class, 'index']);
        Route::get('image/{image}', [ImageManipulationController::class, 'show']);
        Route::get('image/by-album/{album}', [ImageManipulationController::class, 'byAlbum']);
        Route::post('image/resize', [ImageManipulationController::class, 'resize']);
        Route::delete('image/{image}', [ImageManipulationController::class, 'destroy']);
    });

});



