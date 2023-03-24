<?php

    namespace Boodschappenservice\core;

    use Boodschappenservice\utilities\API;
    use Boodschappenservice\utilities\JSON;
    use JetBrains\PhpStorm\ArrayShape;
    use Boodschappenservice\utilities\URL;

    /**
     * @property-read URL $url
     * @property-read Headers $headers
     * @property-read string $method
     * @property-read mixed $body
     */
    class Request implements \JsonSerializable {

        private URL $url;
        private Headers $headers;
        private string $method;
        private $body;

        private function __construct() {
            $this->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? "GET");
            $this->url = URL::getRequestURL();
            $this->headers = Headers::get();

            $this->body = file_get_contents("php://input");
            if($this->headers->hasHeader("Content-Type")) {
                try {
                    if(str_contains($this->headers->getHeader("Content-Type"), "application/json")) {
                        $this->body = JSON::decode($this->body, true);
                    } else if(str_contains($this->headers->getHeader("Content-Type"), "application/x-www-form-urlencoded")) {
                        parse_str($this->body, $res);
                        $this->body = $res;
                    }
                } catch(\Exception $e) {
                    API::printAndExit($e, $e->getCode());
                }
            }
        }

        public function __get(string $name) {
            if(isset($this->$name)) return $this->$name;
            return null;
        }

        public function __set(string $name, $value) {
            return null;
        }

        public function __toString(): string {
            return json_encode($this->jsonSerialize());
        }

        private static ?Request $request = null;
        public static function get() : Request {
            if(self::$request === null) self::$request = new Request();
            return self::$request;
        }

        #[ArrayShape(["url" => "\Boodschappenservice\utilities\URL", "method" => "string", "headers" => "\Boodschappenservice\core\Headers", "body" => "mixed"])]
        public function jsonSerialize() : array {
            return [
                "url" => $this->url,
                "method" => $this->method,
                "headers" => $this->headers,
                "body" => $this->body ?? null
            ];
        }
    }