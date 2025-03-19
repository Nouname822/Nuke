<?php

use Common\Helpers\Functions;
use App\Routing\Auth\Route;

require_once Functions::root('@/modules/Wheel/vendor/autoload.php');

Route::register(function () {
    Route::group('api', '/api/', [], function () {
    });
});