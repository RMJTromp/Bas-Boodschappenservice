<?php

    namespace {

        use Boodschappenservice\utilities\API;
        use Boodschappenservice\utilities\File;
        use Boodschappenservice\utilities\Path;
        use Boodschappenservice\utilities\URL;
        use Dotenv\Dotenv;
        use JetBrains\PhpStorm\NoReturn;

        require_once './vendor/autoload.php';
        require_once './error_handler.php';

        Dotenv::createImmutable("../../")->load();
        define("BASE_DIRECTORY", Path::resolve("./"));
        $REQUEST_URL = URL::getRequestURL();
        const PASSPHRASE = "XA3BF0p0HLXG7j4dNAPVgmDytDs7WQjeks3zTP7jYi1qHhnv1GRLc9aFkPwy";

        $errors = [];

        $conn = new mysqli(
            $_ENV["DB_HOST"] ?? "localhost",
            $_ENV["DB_USER"] ?? "root",
            $_ENV["DB_PASS"] ?? "",
            $_ENV["DB_NAME"] ?? "boodschappenservice",
            $_ENV["DB_PORT"] ?? 3306
        );
        if($conn->connect_error)
            throw new Exception("Connection failed: " . $conn->connect_error, 500);

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