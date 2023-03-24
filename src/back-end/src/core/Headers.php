<?php

    namespace Boodschappenservice\core;

    class Headers extends \ArrayObject {

        private function __construct() {
            parent::__construct(apache_request_headers());
        }

        public function hasHeader(string $header) : bool {
            return self::getHeader($header) !== null;
        }

        public function getHeader(string $header) : ?string {
            foreach ($this as $key => $value) {
                if(strtolower($header) === strtolower($key)) return $value;
            }
            return null;
        }

        private static ?Headers $headers = null;
        public static function get() : Headers {
            if(self::$headers === null) self::$headers = new Headers();
            return self::$headers;
        }

    }