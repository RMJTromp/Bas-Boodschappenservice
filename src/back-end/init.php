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

//      this is needed for setup_db
        try {
            $test = $_ENV["INSERT_TEST_DATA"] ?? "true";

            require_once '../tables.php';

            create_tables($conn);

            if ($test === "TRUE") insert_test_data($conn);

            print('Tables successfully created');
        } catch (Exception $e) {
            print($e->getMessage());
        }


        /**
         * Get request header value or null if none is present
         * @param string $key The header key
         * @return string|null The header value or null
         */
        function getHeader(string $key) : ?string {
            $headers = apache_request_headers();
            foreach($headers as $header => $value) {
                if(strtolower($header) == strtolower($key)) return $value;
            }
            return null;
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
//            if(str_contains(getHeader("Accept-Encoding"), "gzip")) {
//                header("Content-Encoding: gzip");
//                $content = gzencode($content);
//                $length = strlen($content);
//            }
            header("Content-Length: $length");
            exit($content);
        }
    }