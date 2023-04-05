<?php

    use Boodschappenservice\core\Route;
    use Boodschappenservice\utilities\API;
    use Boodschappenservice\utilities\File;

    require "init.php";
    require "src/tests/user_tests.php";


// load all routes
    foreach (glob('./src/routes/*.php') as $route) {
        include $route;
    }

    Route::get("/", fn() => API::printAndExit([]));

    Route::get("/setup", function() {
        switch ($_GET['confirm'] ?? "") {
            case "true":
                global $conn;
                $file = new File("tables.sql");
                if($conn->multi_query($file->getContents())) API::printAndExit("Tables initialized");
                else throw new Exception("Failed to initialize tables: {$conn->error}", 500);
            case "false":
                API::printAndExit("Setup aborted", 499);
            default:
                printAndExit(<<<EOF
                    <script>
                        window.location.href = new URL('?confirm=' + String(confirm("Are you sure you want to initiate setup?")), window.location.href).href;
                    </script>                
                EOF);
        }
    });

    Route::get("/register-test", function() {
        $result = testUserRegistrationAndLogin();
        API::printAndExit($result);
    });
