<?php

    namespace Boodschappenservice\utilities;

    use ReflectionClass;

    class ResponseCode {

        private function __construct() {}

        public const CONTINUE = [100, "Continue"];
        public const SWITCHING_PROTOCOLS = [101, "Switching Protocols"];

        // successful
        public const OK = [200, "OK"];
        public const CREATED = [201, "Created"];
        public const ACCEPTED = [202, "Accepted"];
        public const NON_AUTHORATIVE_INFORMATION = [203, "Non-Authorative Information"];
        public const NO_CONTENT = [204, "No Content"];
        public const RESET_CONTENT = [205, "Reset Content"];
        public const PARTIAL_CONTENT = [206, "Partial Content"];

        // redirection
        public const MULTIPLE_CHOICES = [300, "Multiple Choices"];
        public const MOVED_PERMANENTLY = [301, "Moved Permanently"];
        public const FOUND = [302, "Found"];
        public const SEE_OTHER = [303, "See Other"];
        public const NOT_MODIFIED = [304, "Not Modified"];
        public const USE_PROXY = [305, "Use Proxy"];
        public const UNUSED = [306, "Unused"];
        public const TEMPORARY_REDIRECT = [307, "Temporary Redirect"];
        public const PERMANENT_REDIRECT = [308, "Permanent Redirect"];

        // client error
        public const BAD_REQUEST = [400, "Bad Request"];
        public const UNAUTHORIZED = [401, "Unauthorized"];
        public const PAYMENT_REQUIRED = [402, "Payment Required"];
        public const FORBIDDEN = [403, "Forbidden"];
        public const NOT_FOUND = [404, "Not Found"];
        public const METHOD_NOT_ALLOWED = [405, "Method Not Allowed"];
        public const NOT_ACCEPTABLE = [406, "Not Acceptable"];
        public const PROXY_AUTHENTICATION_REQUIRED = [407, "Proxy Authentication Required"];
        public const REQUEST_TIMEOUT = [408, "Request Timeout"];
        public const CONFLICT = [409, "Conflict"];
        public const GONE = [410, "Gone"];
        public const LENGTH_REQUIRED = [411, "Length Required"];
        public const PRECONDITION_FAILED = [412, "Precondition Failed"];
        public const REQUEST_ENTITY_TOO_LARGE = [413, "Request Entity Too Large"];
        public const UNSUPPORTED_MEDIA_TYPE = [415, "Unsupported Media Type"];
        public const REQUEST_RANGE_NOT_SATISFIED = [416, "Request Range Not Satisfied"];
        public const EXPECTATION_FAILED = [417, "Expectation Failed"];
        public const CLIENT_CLOSED_REQUEST = [499, "Client Closed Request"];

        // server error
        public const INTERNAL_SERVER_ERROR = [500, "Internal Server Error"];
        public const NOT_IMPLEMENTED = [501, "Not Implemented"];
        public const BAD_GATEWAY = [502, "Bad Gateway"];
        public const SERVICE_UNAVAILABLE = [503, "Service Unavailable"];
        public const GATEWAY_TIMEOUT = [504, "Gateway Timeout"];
        public const HTTP_VERSION_NOT_SUPPORTED = [505, "HTTP Version Not Supported"];

        public static function getMessage(int $code) : ?string {
            $oClass = new ReflectionClass(__CLASS__);
            foreach($oClass->getConstants() as $key => $value) {
                if($value[0] === $code) return $value[1];
            }
            return null;
        }

    }