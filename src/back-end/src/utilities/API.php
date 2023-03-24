<?php

    namespace Boodschappenservice\utilities;

    use JetBrains\PhpStorm\NoReturn;

    class API {

        #[NoReturn]
        public static function printAndExit(mixed $response, int $code = 200, string $message = null, array $meta = []): void {
            http_response_code($code);
            $_meta = [
                "status" => [
                    "code" => $code,
                    "message" => is_string($message) && !empty($message) ? $message : (ResponseCode::getMessage($code) ?? "")
                ],
                "interval" => (Math::round(microtime(true) * 1000) - Math::round($_SERVER['REQUEST_TIME_FLOAT'] * 1000)) . "ms"
            ];
            JSON::printAndExit([
                "meta" => array_merge($_meta, $meta, $_meta), // doing it this way makes sure that the primary meta is at top and can't be overridden
                "response" => $response === "{}" ? [] : $response
            ]);
        }

    }