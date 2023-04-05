<?php

    use Boodschappenservice\core\Route;
    use Boodschappenservice\utilities\API;

    require "init.php";

    // load all routes
    foreach (glob('./src/routes/*.php') as $route) {
        include $route;
    }

    Route::get("/", fn() => API::printAndExit([]));