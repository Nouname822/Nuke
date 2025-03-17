<?php

define('START_TIME', microtime(true));

require_once "../vendor/autoload.php";

use App\Kernel;

(new Kernel())::index();
