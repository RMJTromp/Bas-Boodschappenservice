<?php

    namespace Boodschappenservice\core;

    use Boodschappenservice\utilities\API;
    use ReflectionFunction;
    use Boodschappenservice\utilities\RegExp;

    class Route {

        private function __construct() {}

        public static bool $handled = true;

        /**
         * @param string|RegExp $path
         * @param callable $callback
         * @param string|string[] $method
         */
        private static function _handle(string|RegExp $path, callable $callback, string|array $method = []) : void {
            $request = Request::get();
            $pathname = urldecode($request->url->pathname);
            if((is_string($path) && strtolower($path) === strtolower($pathname)) || ($path instanceof RegExp && $path->test($pathname))) {
                self::$handled = true;
                if((is_string($method) && $method === $request->method) || (is_array($method) && in_array($request->method, $method, true))) {
                    $refFunc = new ReflectionFunction($callback);
                    try {
                        if($path instanceof RegExp && $refFunc->getNumberOfParameters() === 2) $callback($request, $path->exec($pathname));
                        else $callback($request);
                    } catch(\Exception $e) {
                        API::printAndExit([], $e->getCode() === 0 ? 500 : $e->getCode(), null, [
                            "exception" => $e->getMessage()
                        ]);
                    }
                }
            }
        }

        /**
         * @param string|RegExp $path
         * @param callable $callback
         * @param string|string[] $methods
         */
        public static function handle(string|RegExp $path, callable $callback, string|array $methods = ["GET", "POST", "PUT", "PATCH", "DELETE", "HEAD"]) : void {
            self::_handle($path, $callback, $methods);
        }

        public static function get(string|RegExp $path, callable $callback) : void {
            self::_handle($path, $callback, RequestMethod::GET);
        }

        public static function post(string|RegExp $path, callable $callback) : void {
            self::_handle($path, $callback, RequestMethod::POST);
        }

        public static function put(string|RegExp $path, callable $callback) : void {
            self::_handle($path, $callback, RequestMethod::PUT);
        }

        public static function patch(string|RegExp $path, callable $callback) : void {
            self::_handle($path, $callback, RequestMethod::PATCH);
        }

        public static function delete(string|RegExp $path, callable $callback) : void {
            self::_handle($path, $callback, RequestMethod::DELETE);
        }

        public static function head(string|RegExp $path, callable $callback) : void {
            self::_handle($path, $callback, RequestMethod::HEAD);
        }

    }