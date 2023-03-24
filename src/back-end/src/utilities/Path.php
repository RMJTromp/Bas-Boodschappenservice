<?php

    namespace Boodschappenservice\utilities;

    class Path {

//        public function __constructor(string ...$path) {
//            $cwd = explode(DIRECTORY_SEPARATOR, getcwd());
//            $path = explode(DIRECTORY_SEPARATOR, self::_resolve(...$path));
//
//            if($cwd[0] !== $path[0]) {
//                $path = realpath(join(DIRECTORY_SEPARATOR, $path));
//                if($path === false) {
//                    // todo
//                }
//                $path = explode(DIRECTORY_SEPARATOR, $path);
//            }
//
//            if($cwd[0] === $path[0]) {
//                //                $i = 0;
//                //                while($cwd[$i] === $)
//            } else throw new \RuntimeException("Unable to resolve file's absolute path");
//
//        }

        public static function resolve(string ...$path) : string {
            return self::_resolve(...$path);
        }

        private static function _resolve(string ...$path) : string {
            // fix separator and split into array
            $path = explode(DIRECTORY_SEPARATOR, self::fixSeparator(join(DIRECTORY_SEPARATOR, $path)));

            // remove .
            while(($i = array_search(".", $path, true)) !== false) array_splice($path, $i, 1);

            // solve ..
            // by removing all folders followed by /..
            {
                $separator = preg_quote(DIRECTORY_SEPARATOR, "/");
                $regex = new RegExp("/(?<={$separator}|^)(?:(?!\.\.?)[^{$separator}]+?){$separator}\.\.(?={$separator}|$)/");

                $i = 1;
                $p = join(DIRECTORY_SEPARATOR, $path);
                while($i > 0) $p = self::fixSeparator($regex->replace("", $p, -1, $i));
                $path = explode(DIRECTORY_SEPARATOR, str_starts_with($p, DIRECTORY_SEPARATOR) ? substr($p, 1) : $p);
            }

            // join array
            $path = join(DIRECTORY_SEPARATOR, $path);
            if(is_dir($path)) $path .= DIRECTORY_SEPARATOR;
            return $path;
        }

        private static function resolveToAbsolute(string $path) : string {
            return self::resolve(getcwd(), $path);
        }

        private static function fixSeparator(string $path) {
            $regex = new RegExp('/[\\\\\/]+/');
            return $regex->replace(DIRECTORY_SEPARATOR, $path);
        }

    }