<?php

    namespace Boodschappenservice\utilities;

    use Boodschappenservice\exceptions\MalformedPatternException;

    class RegExp {

        private string $pattern;

        public static function quote(string $string, string $delimiter = null) : string {
            return preg_quote($string, $delimiter);
        }

        public static function compile(string $pattern) : RegExp {
            return new RegExp($pattern);
        }

        public function __construct(string $pattern) {
            @preg_match($pattern, "");
            if(preg_last_error() !== 0) throw new MalformedPatternException(preg_last_error_msg(), preg_last_error());
            $this->pattern = $pattern;
        }

        public function test(string $string, int $flags = 0, int $offset = 0) : bool {
            $ignore = [];
            return preg_match($this->pattern, $string, $ignore, $flags, $offset) === 1;
        }

        public function exec(string $string, int $flags = PREG_PATTERN_ORDER, int $offset = 0) : array {
            $matches = [];
            preg_match_all($this->pattern, $string, $matches, $flags, $offset);
            return $matches;
        }

        public function replace(array|string $replacement, array|string $subject, int $limit = -1, int &$count = 0) : ArrayList|string {
            $res = preg_replace($this->pattern, $replacement, $subject, $limit, $count);
            return is_array($res) ? new ArrayList($res) : $res;
        }

        public function split(string $subject, int $limit = -1, int $flags = 0) : ArrayList {
            return new ArrayList(preg_split($this->pattern, $subject, $limit, $flags));
        }

        public function __toString(): string {
            return $this->pattern;
        }

    }