<?php

    use Boodschappenservice\utilities\API;

    require "init.php";

    // load all routes
    foreach (glob('./src/routes/*.php') as $route) {
        include $route;
    }

//    API::printAndExit([], 404);
