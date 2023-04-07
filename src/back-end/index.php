<?php

    use Boodschappenservice\core\Request;
    use Boodschappenservice\core\Route;
    use Boodschappenservice\utilities\API;
    use Boodschappenservice\utilities\File;

    require "init.php";

    global $REQUEST_URL;

    if($REQUEST_URL->hostname === "boodschappenservice.loc") {
        Route::$handled = true;
        Route::$methods = [ Request::get()->method ];

        $path = $REQUEST_URL->pathname;
        if($path === "/") $path = "/index.html";
        printAndExit(new File("../front-end/dist" . $path));
    } else {
        Route::get("/", fn() => API::printAndExit(null));

        // load all routes
        foreach (glob('./src/routes/*.php') as $route) {
            include $route;
        }
    }
