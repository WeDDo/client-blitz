<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\EmailInboxSettingController;
use App\Http\Controllers\EmailSettingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

Route::get('', [HomeController::class, 'index'])
    ->name('home');

Route::get('login', [AuthController::class, 'loginShow'])
    ->name('login');

Route::post('login', [AuthController::class, 'login'])
    ->name('auth.login');

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])
        ->name('auth.logout');
});

Route::get('registration', [AuthController::class, 'registrationShow'])
    ->name('registration');

Route::post('registration', [AuthController::class, 'registration'])
    ->name('auth.registration');

Route::middleware('auth.check')->group(function () {
    Route::get('logout', [AuthController::class, 'logout'])
        ->name('auth.logout');

    Route::get('dashboard', function () {
        return inertia('Dashboard');
    })->name('dashboard');

    Route::get('emails', [EmailController::class, 'index'])
        ->name('modules.emails.index');

    Route::prefix('email-settings')->group(function () {
        Route::get('', [EmailSettingController::class, 'index'])
            ->name('modules.email-settings.index');
        Route::get('create', [EmailSettingController::class, 'create'])
            ->name('modules.email-settings.create');
        Route::post('', [EmailSettingController::class, 'store'])
            ->name('modules.email-settings.store');


        Route::prefix('{emailSetting}')->group(function () {
            Route::get('', [EmailSettingController::class, 'show'])
                ->name('modules.email-settings.show');
            Route::get('check-connection', [EmailSettingController::class, 'checkConnection'])
                ->name('modules.email-settings.check-connection');
            Route::put('', [EmailSettingController::class, 'update'])
                ->name('modules.email-settings.update');
        });
    });

    Route::prefix('email-inbox-settings')->group(function () {
        Route::get('', [EmailInboxSettingController::class, 'index'])
            ->name('modules.email-inbox-settings.index');
        Route::get('create', [EmailInboxSettingController::class, 'create'])
            ->name('modules.email-inbox-settings.create');
        Route::post('', [EmailInboxSettingController::class, 'store'])
            ->name('modules.email-inbox-settings.store');

        Route::prefix('{emailInboxSetting}')->group(function () {
            Route::get('', [EmailInboxSettingController::class, 'show'])
                ->name('modules.email-inbox-settings.show');
            Route::put('', [EmailInboxSettingController::class, 'update'])
                ->name('modules.email-inbox-settings.update');

            Route::get('get-inboxes-imap', [EmailInboxSettingController::class, 'getInboxesImap']);
            Route::post('create-inboxes', [EmailInboxSettingController::class, 'createInboxes']);
        });
    });
});
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
