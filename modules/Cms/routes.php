<?php

use Common\Helpers\Functions;
use App\Routing\Auth\Route;
use Cms\Controllers\DataGroupController;

require_once Functions::root('@/modules/Cms/vendor/autoload.php');

Route::register(function () {
    Route::group('api', '/api/', [], function () {
        Route::group('admin', '/admin/', [], function () {
            Route::group('data', '/data/', [], function () {
                $controller = DataGroupController::class;
                Route::post('add', [$controller, 'add']);
                Route::put('set/{id}', [$controller, 'set']);
                Route::delete('del/{id}', [$controller, 'del']);
                Route::post('recovery/{id}', [$controller, 'recovery']);
                Route::get('get/{id}', [$controller, 'get']);
            });
        });
    });
});
