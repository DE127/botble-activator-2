<?php

use Botble\Base\Facades\AdminHelper;
use Botble\Base\Facades\BaseHelper;
use Shaqi\BotbleActivator\Http\Controllers\ShaqiActivatorController;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\Setting\Http\Controllers'], function () {
    AdminHelper::registerRoutes(function () {
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::prefix('license')->name('license.')->group(function () {
                Route::get('verify', [
                    'as' => 'verify',
                    'uses' => '\\' . ShaqiActivatorController::class . '@getVerifyLicense',
                    'permission' => false,
                ]);

                Route::post('activate', [
                    'as' => 'activate',
                    'uses' =>  '\\' . ShaqiActivatorController::class . '@activateLicense',
                    'middleware' => 'preventDemo',
                    'permission' => 'core.manage.license',
                ]);

                Route::post('deactivate', [
                    'as' => 'deactivate',
                    'uses' =>  '\\' . ShaqiActivatorController::class . '@deactivateLicense',
                    'middleware' => 'preventDemo',
                    'permission' => 'core.manage.license',
                ]);
            });
        });
    });
});
