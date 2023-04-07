<?php

    namespace {

        use Boodschappenservice\utilities\API;
        use Boodschappenservice\utilities\ArrayList;
        use Boodschappenservice\utilities\File;
        use Boodschappenservice\utilities\Path;
        use Boodschappenservice\utilities\URL;
        use Dotenv\Dotenv;
        use JetBrains\PhpStorm\NoReturn;

        require_once './vendor/autoload.php';
        require_once './error_handler.php';

        header("Access-Control-Allow-Origin: *");
        !(new File("../../.env"))->exists() and API::printAndExit("Environment file not initialized", 500);
        Dotenv::createImmutable("../../")->load();
        define("BASE_DIRECTORY", Path::resolve("./"));
        $REQUEST_URL = URL::getRequestURL();
        const PASSPHRASE = "XA3BF0p0HLXG7j4dNAPVgmDytDs7WQjeks3zTP7jYi1qHhnv1GRLc9aFkPwy";

        $errors = [];

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $conn = new mysqli(
            hostname: $_ENV["DB_HOST"] ?? "localhost",
            username: $_ENV["DB_USER"] ?? "root",
            password: $_ENV["DB_PASS"] ?? "",
            port: $_ENV["DB_PORT"] ?? 3306
        );
        if($conn->connect_error)
            throw new Exception("Connection failed: " . $conn->connect_error, 500);

        {
            $dbName = $_ENV["DB_NAME"] ?? "boodschappenservice";
            $conn->query("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8 COLLATE utf8_general_ci;");
            $conn->select_db($dbName);

            !$conn->multi_query((new File("tables.sql"))->getContents()) and throw new Exception("Failed to initialize tables: {$conn->error}", 500);
            // close all open statements
            while($conn->more_results() && $conn->next_result()) {
                if($result = $conn->store_result()) $result->free();
            }
        }

        function println(string $string) : void {
            echo $string . PHP_EOL;
        }

        #[NoReturn]
        function printAndExit(File|string $content, string $type = null) : void {
            if($content instanceof File) {
                $type = empty($type) ? $content->getMimeType() : $type;
                header("Content-Disposition: inline; filename=\"{$content->getBaseName()}\"");
                $content = $content->getContents();
            }

            $type = empty($type) ? "text/html; charset=UTF-8" : $type;
            header("Content-Type: $type");
            $length = strlen($content);
            if(str_contains($_SERVER['HTTP_ACCEPT_ENCODING'] ?? "", 'gzip')) {
                header("Content-Encoding: gzip");
                $content = gzencode($content);
                $length = strlen($content);
            }
            header("Content-Length: $length");
            exit($content);
        }

        #[NoReturn]
        function dumpAndExit(...$var) : void {
            printAndExit((new ArrayList($var))
                ->map(fn($var) => var_export($var, true))
                ->join("\n\n"), "text/plain");
        }
    }