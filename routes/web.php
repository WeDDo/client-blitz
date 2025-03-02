<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\FileRipperController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

Route::get('', [HomeController::class, 'index'])
    ->name('home');

//Route::get('files', [FileController::class, 'index'])
//    ->name('files.index');
//Route::delete('files/mass-delete', [FileController::class, 'massDelete'])
//    ->name('files.massDelete');
//Route::delete('files', [FileController::class, 'destroy'])
//    ->name('files.delete');
//Route::post('files/add-to-favorites', [FileController::class, 'addToFavorites'])
//    ->name('files.addToFavorites');
//
//Route::get('file-ripper', [FileRipperController::class, 'index'])
//    ->name('fileripper.index');
//
//Route::post('file-ripper/rip', [FileRipperController::class, 'rip'])
//    ->name('fileripper.rip');

Route::post('set-locale', [LocaleController::class, 'setLocale'])
    ->name('set-locale');
