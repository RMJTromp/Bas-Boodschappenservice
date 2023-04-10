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
        if($path === "/favicon.ico") {
            http_response_code(404);
            exit();
        }
        if($path === "/") $path = "/index.html";
        $file = new File("../front-end/dist" . $path);
        if(!$file->isFile()) $file = new File("../front-end/dist/index.html");
        printAndExit($file);
    } else {
        Route::get("/", fn() => API::printAndExit(null));

        // load all routes
        foreach (glob('./src/routes/*.php') as $route) {
            include $route;
        }
    }
