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
        if(($_GET['confirm'] ?? "false") !== "true") {
            printAndExit(<<<EOF
                <script>
                    window.location.href = new URL('?confirm=' + String(confirm("Are you sure you want to initiate setup?")), window.location.href).href;
                </script>                
            EOF);
        }

        global $conn;
        $file = new File("tables.sql");
        if($conn->multi_query($file->getContents())) API::printAndExit("Tables initialized & populated");
        else throw new Exception("Failed to initialize tables: {$conn->error}", 500);
    });