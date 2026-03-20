<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\BlastController;
use App\Http\Controllers\SettingController;

Route::get('/', function () {
    return to_route('login');
});

Auth::routes();

Route::get('/home', function() {
    return view('layouts.layout');
})->name('home');

Route::middleware('auth')->group(function () {
    Route::resource('brands', BrandController::class);
    Route::resource('campaigns', CampaignController::class);
    Route::resource('blasts', BlastController::class);
    Route::get('settings/message', [SettingController::class, 'messageSetting'])->name('settings.message');
    Route::post('settings/message', [SettingController::class, 'updateMessageSetting'])->name('settings.message.update');
});
