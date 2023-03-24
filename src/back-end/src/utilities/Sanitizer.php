<?php

    namespace Boodschappenservice\utilities;

    class Sanitizer {

        private function __construct() {}

        public static function sanitizeString(string $string) : string {
            return trim(htmlspecialchars($string));
        }

    }