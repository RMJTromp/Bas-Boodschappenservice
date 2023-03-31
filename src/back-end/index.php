<?php

    use Boodschappenservice\core\Request;
    use Boodschappenservice\core\Route;
    use Boodschappenservice\utilities\API;
    use Boodschappenservice\utilities\File;

    require "init.php";

    // load all routes
    foreach (glob('./src/routes/*.php') as $route) {
        include $route;
    }

    Route::get("/setup", function(Request $request) {
        global $conn; // $conn is al gedefinieerd
        require_once '../tables.php';
        create_tables($conn);
        insert_test_data($conn);
        API::printAndExit("Tables initialized", 200);
    });

//    API::printAndExit([], 404);
