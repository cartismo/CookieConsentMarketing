<?php

use Illuminate\Support\Facades\Route;
use Modules\CookieConsentMarketing\Http\Controllers\Admin\SettingsController;

/*
|--------------------------------------------------------------------------
| CookieConsentMarketing Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('modules/marketing/cookie-consent-marketing')->name('admin.marketing.cookie-consent.')->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
});