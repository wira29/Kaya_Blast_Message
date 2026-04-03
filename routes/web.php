<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\BlastController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\InsightController;

Route::get('/', function () {
    return to_route('login');
});

Auth::routes();

Route::get('/home', function() {
    return view('layouts.layout');
})->name('home');

Route::middleware('auth')->group(function () {
    Route::resource('brands', BrandController::class);
    Route::post('campaigns/import-affiliate', [CampaignController::class, 'importAffiliate'])->name('campaigns.import-affiliate');
    Route::get('campaigns/download-template', [CampaignController::class, 'downloadTemplate'])->name('campaigns.download-template');
    Route::resource('campaigns', CampaignController::class);
    Route::resource('blasts', BlastController::class);
    Route::resource('insights', InsightController::class, ['only' => ['index']]);
    Route::post('insights/{brand}/download', [InsightController::class, 'downloadInsight'])->name('insights.download');
    Route::get('settings/message', [SettingController::class, 'messageSetting'])->name('settings.message');
    Route::post('settings/message', [SettingController::class, 'updateMessageSetting'])->name('settings.message.update');
    Route::get('settings/number-key', [SettingController::class, 'numberKeySetting'])->name('settings.number-key');
    Route::post('settings/number-key', [SettingController::class, 'updateNumberKeySetting'])->name('settings.number-key.update');
});
