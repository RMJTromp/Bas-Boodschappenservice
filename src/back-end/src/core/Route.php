<?php

    namespace Boodschappenservice\core;

    use ReflectionFunction;
    use Boodschappenservice\utilities\API;
    use Boodschappenservice\utilities\RegExp;

    register_shutdown_function(function() {
        if(!Route::$handled) API::printAndExit([], 404);
        else if(!in_array(Request::get()->method, Route::$methods, true)) API::printAndExit([], 405);
    });

    class Route {

        private function __construct() {}

        public static bool $handled = false;
        public static array $methods = [];

        /**
         * @param string|RegExp $path
         * @param callable $callback
         * @param string|string[] $method
         */
        private static function _handle(string|RegExp $path, callable $callback, string|array $method = []) : void {
            $request = Request::get();
            $pathname = urldecode($request->url->pathname);
            if((is_string($path) && strtolower($path) === strtolower($pathname)) || ($path instanceof RegExp && $path->test($pathname))) {

                // keep track of all methods
                if(is_string($method)) Route::$methods[] = $method;
                else Route::$methods = array_merge(Route::$methods, $method);

                if((is_string($method) && $method === $request->method) || (is_array($method) && in_array($request->method, $method, true))) {
                    Route::$handled = true;
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