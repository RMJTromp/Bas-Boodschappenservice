<?php

    namespace Boodschappenservice\utilities;

    use ArithmeticError;
    use DivisionByZeroError;

    /**
     * Utility class which contains all PHP math functions and constants
     */
    class Math {

        const E = 2.7182818284590452354;
        const EULER = 0.57721566490153286061;
        const INFINITE = INF;
        const LN2 = 0.69314718055994530942;
        const LN10 = 2.30258509299404568402;
        const LNPI = 1.14472988584940017414;
        const LOG2E = 1.4426950408889634074;
        const LOG10E = 0.43429448190325182765;
        const NAN = NAN;
        const PI = 3.14159265358979323846;
        const SQRT1_2 = 0.7071067811865476;
        const SQRT2 = 1.4142135623730951;

        private function __construct() {}

        public static function constraintToRange(int|float $value, int|float $min, int|float $max) : int|float {
            return Math::max(Math::min($value, $max), $min);
        }

        /**
         * Absolute value
         * @link https://php.net/manual/en/function.abs.php
         * @param int|float $num <p>
         * The numeric value to process
         * </p>
         * @return float|int The absolute value of number. If the
         * argument number is
         * of type float, the return type is also float,
         * otherwise it is integer (as float usually has a
         * bigger value range than integer).
         */
        public static function abs(float|int $num) : float|int {
            return abs($num);
        }

        /**
         * Arc cosine
         * @link https://php.net/manual/en/function.acos.php
         * @param float $num <p>
         * The argument to process
         * </p>
         * @return float The arc cosine of arg in radians.
         */
        public static function acos(float $num) : float {
            return acos($num);
        }

        /**
         * Inverse hyperbolic cosine
         * @link https://php.net/manual/en/function.acosh.php
         * @param float $num <p>
         * The value to process
         * </p>
         * @return float The inverse hyperbolic cosine of arg
         */
        public static function acosh(float $num) : float {
            return acosh($num);
        }

        /**
         * Arc sine
         * @link https://php.net/manual/en/function.asin.php
         * @param float $num <p>
         * The argument to process
         * </p>
         * @return float The arc sine of arg in radians
         */
        public static function asin(float $num) : float {
            return asin($num);
        }

        /**
         * Inverse hyperbolic sine
         * @link https://php.net/manual/en/function.asinh.php
         * @param float $num <p>
         * The argument to process
         * </p>
         * @return float The inverse hyperbolic sine of arg
         */
        public static function asinh(float $num) : float {
            return asinh($num);
        }

        /**
         * Arc tangent
         * @link https://php.net/manual/en/function.atan.php
         * @param float $num <p>
         * The argument to process
         * </p>
         * @return float The arc tangent of arg in radians.
         */
        public static function atan(float $num) : float {
            return atan($num);
        }

        /**
         * Arc tangent of two variables
         * @link https://php.net/manual/en/function.atan2.php
         * @param float $y <p>
         * Dividend parameter
         * </p>
         * @param float $x <p>
         * Divisor parameter
         * </p>
         * @return float The arc tangent of y/x
         * in radians.
         */
        public static function atan2(float $y, float $x) : float {
            return atan2($y, $x);
        }

        /**
         * Inverse hyperbolic tangent
         * @link https://php.net/manual/en/function.atanh.php
         * @param float $num <p>
         * The argument to process
         * </p>
         * @return float Inverse hyperbolic tangent of arg
         */
        public static function atanh(float $num) : float {
            return atanh($num);
        }

        /**
         * Convert a number between arbitrary bases
         * @link https://php.net/manual/en/function.base-convert.php
         * @param string $num <p>
         * The number to convert
         * </p>
         * @param int $from <p>
         * The base number is in
         * </p>
         * @param int $to <p>
         * The base to convert number to
         * </p>
         * @return string number converted to base tobase
         */
        public static function baseConvert(string $num, int $from, int $to) : string {
            return base_convert($num, $from, $to);
        }

        /**
         * Binary to decimal
         * @link https://php.net/manual/en/function.bindec.php
         * @param string $binaryString <p>
         * The binary string to convert
         * </p>
         * @return int|float The decimal value of binary_string
         */
        public static function bindec(string $binaryString) : float|int {
            return bindec($binaryString);
        }

        /**
         * Round fractions up
         * @link https://php.net/manual/en/function.ceil.php
         * @param int|float $num <p>
         * The value to round
         * </p>
         * @return float|false value rounded up to the next highest
         * integer.
         * The return value of ceil is still of type
         * float as the value range of float is
         * usually bigger than that of integer.
         */
        public static function ceil(float|int $num) : float {
            return ceil($num);
        }

        /**
         * Cosine
         * @link https://php.net/manual/en/function.cos.php
         * @param float $num <p>
         * An angle in radians
         * </p>
         * @return float The cosine of arg
         */
        public static function cos(float $num) : float {
            return cos($num);
        }

        /**
         * Hyperbolic cosine
         * @link https://php.net/manual/en/function.cosh.php
         * @param float $num <p>
         * The argument to process
         * </p>
         * @return float The hyperbolic cosine of arg
         */
        public static function cosh(float $num) : float {
            return cosh($num);
        }

        /**
         * Decimal to binary
         * @link https://php.net/manual/en/function.decbin.php
         * @param int $num <p>
         * Decimal value to convert
         * </p>
         * <table>
         * Range of inputs on 32-bit machines
         * <tr valign="top">
         * <td>positive number</td>
         * <td>negative number</td>
         * <td>return value</td>
         * </tr>
         * <tr valign="top">
         * <td>0</td>
         * <td></td>
         * <td>0</td>
         * </tr>
         * <tr valign="top">
         * <td>1</td>
         * <td></td>
         * <td>1</td>
         * </tr>
         * <tr valign="top">
         * <td>2</td>
         * <td></td>
         * <td>10</td>
         * </tr>
         * <tr valign="top">
         * ... normal progression ...</td>
         * </tr>
         * <tr valign="top">
         * <td>2147483646</td>
         * <td></td>
         * <td>1111111111111111111111111111110</td>
         * </tr>
         * <tr valign="top">
         * <td>2147483647 (largest signed integer)</td>
         * <td></td>
         * <td>1111111111111111111111111111111 (31 1's)</td>
         * </tr>
         * <tr valign="top">
         * <td>2147483648</td>
         * <td>-2147483648</td>
         * <td>10000000000000000000000000000000</td>
         * </tr>
         * <tr valign="top">
         * ... normal progression ...</td>
         * </tr>
         * <tr valign="top">
         * <td>4294967294</td>
         * <td>-2</td>
         * <td>11111111111111111111111111111110</td>
         * </tr>
         * <tr valign="top">
         * <td>4294967295 (largest unsigned integer)</td>
         * <td>-1</td>
         * <td>11111111111111111111111111111111 (32 1's)</td>
         * </tr>
         * </table>
         * <table>
         * Range of inputs on 64-bit machines
         * <tr valign="top">
         * <td>positive number</td>
         * <td>negative number</td>
         * <td>return value</td>
         * </tr>
         * <tr valign="top">
         * <td>0</td>
         * <td></td>
         * <td>0</td>
         * </tr>
         * <tr valign="top">
         * <td>1</td>
         * <td></td>
         * <td>1</td>
         * </tr>
         * <tr valign="top">
         * <td>2</td>
         * <td></td>
         * <td>10</td>
         * </tr>
         * <tr valign="top">
         * ... normal progression ...</td>
         * </tr>
         * <tr valign="top">
         * <td>9223372036854775806</td>
         * <td></td>
         * <td>111111111111111111111111111111111111111111111111111111111111110</td>
         * </tr>
         * <tr valign="top">
         * <td>9223372036854775807 (largest signed integer)</td>
         * <td></td>
         * <td>111111111111111111111111111111111111111111111111111111111111111 (31 1's)</td>
         * </tr>
         * <tr valign="top">
         * <td></td>
         * <td>-9223372036854775808</td>
         * <td>1000000000000000000000000000000000000000000000000000000000000000</td>
         * </tr>
         * <tr valign="top">
         * ... normal progression ...</td>
         * </tr>
         * <tr valign="top">
         * <td></td>
         * <td>-2</td>
         * <td>1111111111111111111111111111111111111111111111111111111111111110</td>
         * </tr>
         * <tr valign="top">
         * <td></td>
         * <td>-1</td>
         * <td>1111111111111111111111111111111111111111111111111111111111111111 (64 1's)</td>
         * </tr>
         * </table>
         * @return string Binary string representation of number
         */
        public static function decbin(int $num) : string {
            return decbin($num);
        }

        /**
         * Decimal to octal
         * @link https://php.net/manual/en/function.decoct.php
         * @param int $num <p>
         * Decimal value to convert
         * </p>
         * @return string Octal string representation of number
         */
        public static function decoct(int $num) : string {
            return decoct($num);
        }

        /**
         * Converts the number in degrees to the radian equivalent
         * @link https://php.net/manual/en/function.deg2rad.php
         * @param float $num <p>
         * Angular value in degrees
         * </p>
         * @return float The radian equivalent of number
         */
        public static function deg2rad(float $num) : float {
            return deg2rad($num);
        }

        /**
         * Calculates the exponent of <constant>e</constant>
         * @link https://php.net/manual/en/function.exp.php
         * @param float $num <p>
         * The argument to process
         * </p>
         * @return float 'e' raised to the power of arg
         */
        public static function exp(float $num) : float {
            return exp($num);
        }

        /**
         * Returns exp(number) - 1, computed in a way that is accurate even
         * when the value of number is close to zero
         * @link https://php.net/manual/en/function.expm1.php
         * @param float $num <p>
         * The argument to process
         * </p>
         * @return float 'e' to the power of arg minus one
         */
        public static function expm1(float $num) : float {
            return expm1($num);
        }

        /**
         * Performs a floating-point division under
         * IEEE 754 semantics. Division by zero is considered well-defined and
         * will return one of Inf, -Inf or NaN.
         * @param float $num1
         * @param float $num2
         * @return float
         * @since 8.0
         */
        public static function fdiv(float $num1, float $num2) : float {
            return fdiv($num1, $num2);
        }

        /**
         * Round fractions down
         * @link https://php.net/manual/en/function.floor.php
         * @param int|float $num <p>
         * The numeric value to round
         * </p>
         * @return float|false value rounded to the next lowest integer.
         * The return value of floor is still of type
         * float because the value range of float is
         * usually bigger than that of integer.
         */
        public static function floor(int|float $num) : float {
            return floor($num);
        }

        /**
         * Returns the floating point remainder (modulo) of the division
         * of the arguments
         * @link https://php.net/manual/en/function.fmod.php
         * @param float $num1 <p>
         * The dividend
         * </p>
         * @param float $num2 <p>
         * The divisor
         * </p>
         * @return float The floating point remainder of
         * x/y
         */
        public static function fmod(float $num1, float $num2) : float {
            return fmod($num1, $num2);
        }

        /**
         * Show largest possible random value
         * @link https://php.net/manual/en/function.getrandmax.php
         * @return int The largest possible random value returned by rand
         */
        public static function getRandMax() : int {
            return getrandmax();
        }

        /**
         * Hexadecimal to decimal
         * @link https://php.net/manual/en/function.hexdec.php
         * @param string $hexString <p>
         * The hexadecimal string to convert
         * </p>
         * @return int|float The decimal representation of hex_string
         */
        public static function hexdec(string $hexString) : float|int {
            return hexdec($hexString);
        }

        /**
         * Calculate the length of the hypotenuse of a right-angle triangle
         * @link https://php.net/manual/en/function.hypot.php
         * @param float $x <p>
         * Length of first side
         * </p>
         * @param float $y <p>
         * Length of second side
         * </p>
         * @return float Calculated length of the hypotenuse
         */
        public static function hypot(float $x, float $y) : float {
            return hypot($x, $y);
        }

        /**
         * Integer division
         * @link https://php.net/manual/en/function.intdiv.php
         * @param int $num1 <p>Number to be divided.</p>
         * @param int $num2 <p>Number which divides the <b><i>dividend</i></b></p>
         * @return int
         * @since 7.0
         * @throws DivisionByZeroError <p>if divisor is 0</p>
         * @throws ArithmeticError <p>if the <b><i>dividend</i></b> is <b>PHP_INT_MIN</b> and the <b><i>divisor</i></b> is -1</p>
         */
        public static function intdiv(int $num1, int $num2) : int {
            return intdiv($num1, $num2);
        }

        /**
         * Finds whether a value is a legal finite number
         * @link https://php.net/manual/en/function.is-finite.php
         * @param float $num <p>
         * The value to check
         * </p>
         * @return bool true if val is a legal finite
         * number within the allowed range for a PHP float on this platform,
         * else false.
         */
        public static function isFinite(float $num) : bool {
            return is_finite($num);
        }

        /**
         * Finds whether a value is infinite
         * @link https://php.net/manual/en/function.is-infinite.php
         * @param float $num <p>
         * The value to check
         * </p>
         * @return bool true if val is infinite, else false.
         */
        public static function isInfinite(float $num) : bool {
            return is_infinite($num);
        }

        /**
         * Finds whether a value is not a number
         * @link https://php.net/manual/en/function.is-nan.php
         * @param float $num <p>
         * The value to check
         * </p>
         * @return bool true if val is 'not a number',
         * else false.
         */
        public static function isNaN(float $num) : bool {
            return is_nan($num);
        }

        /**
         * Combined linear congruential generator
         * @link https://php.net/manual/en/function.lcg-value.php
         * @return float A pseudo random float value in the range of (0, 1)
         */
        public static function lcg_value() : float {
            return lcg_value();
        }

        /**
         * Natural logarithm
         * @link https://php.net/manual/en/function.log.php
         * @param float $num <p>
         * The value to calculate the logarithm for
         * </p>
         * @param float $base [optional] <p>
         * The optional logarithmic base to use
         * (defaults to 'e' and so to the natural logarithm).
         * </p>
         * @return float The logarithm of arg to
         * base, if given, or the
         * natural logarithm.
         */
        public static function log(float $num, float $base = M_E) {
            return log($num, $base);
        }

        /**
         * Base-10 logarithm
         * @link https://php.net/manual/en/function.log10.php
         * @param float $num <p>
         * The argument to process
         * </p>
         * @return float The base-10 logarithm of arg
         */
        public static function log10(float $num) : float {
            return log10($num);
        }

        /**
         * Returns log(1 + number), computed in a way that is accurate even when
         * the value of number is close to zero
         * @link https://php.net/manual/en/function.log1p.php
         * @param float $num <p>
         * The argument to process
         * </p>
         * @return float log(1 + number)
         */
        public static function log1p(float $num) : float {
            return log1p($num);
        }

        /**
         * Find highest value
         * @link https://php.net/manual/en/function.max.php
         * @param float|int|float[]|int[] $value Array to look through or first value to compare
         * @param float|int|float[]|int[] ...$values [optional] any comparable value
         * @return float|int max returns the numerically highest of the
         * parameter values, either within a arg array or two arguments.
         */
        public static function max(float|int|array $value, float|int|array ...$values) : float|int {
            return max($value, ...$values);
        }

        /**
         * Find lowest value
         * @link https://php.net/manual/en/function.min.php
         * @param float|int|float[]|int[] $value Array to look through or first value to compare
         * @param float|int|float[]|int[] ...$values [optional] any comparable value
         * @return float|int min returns the numerically lowest of the
         * parameter values.
         */
        public static function min(float|int|array $value, float|int|array ...$values) : float|int {
            return min($value, ...$values);
        }

        /**
         * Show largest possible random value
         * @link https://php.net/manual/en/function.mt-getrandmax.php
         * @return int the maximum random value returned by mt_rand
         */
        public static function mt_getrandmax() : int {
            return mt_getrandmax();
        }

        /**
         * Generate a random value via the Mersenne Twister Random Number Generator
         * @link https://php.net/manual/en/function.mt-rand.php
         * @param int $min [optional] <p>
         * Optional lowest value to be returned (default: 0)
         * </p>
         * @param int $max [optional] <p>
         * Optional highest value to be returned (default: mt_getrandmax())
         * </p>
         * @return int A random integer value between min (or 0)
         * and max (or mt_getrandmax, inclusive)
         */
        public static function mt_rand(int $num, int $max = null) : int {
            return mt_rand($num, $max ?? mt_getrandmax());
        }

        /**
         * Seeds the Mersenne Twister Random Number Generator
         * @link https://php.net/manual/en/function.mt-srand.php
         * @param int $seed [optional] <p>
         * An optional seed value
         * </p>
         * @param int $mode [optional] <p>
         * Use one of the following constants to specify the implementation of the algorithm to use.
         * </p>
         * @return void
         */
        public static function mt_srand(int $seed, int $mode = MT_RAND_MT19937) : void {
            mt_srand($seed, $mode);
        }

        /**
         * Octal to decimal
         * @link https://php.net/manual/en/function.octdec.php
         * @param string $octalString <p>
         * The octal string to convert
         * </p>
         * @return int|float The decimal representation of octal_string
         */
        public static function octdec($octalString) {
            return octdec($octalString);
        }

        /**
         * Exponential expression
         * @link https://php.net/manual/en/function.pow.php
         * @param mixed $num <p>
         * The base to use
         * </p>
         * @param mixed $exponent <p>
         * The exponent
         * </p>
         * @return float|int base raised to the power of exp.
         * If the result can be represented as integer it will be returned as type
         * integer, else it will be returned as type float.
         * If the power cannot be computed false will be returned instead.
         */
        public static function pow(float|int $num, object|float|int $exponent) : float|int {
            return pow($num, $exponent);
        }

        /**
         * Converts the radian number to the equivalent number in degrees
         * @link https://php.net/manual/en/function.rad2deg.php
         * @param float $num <p>
         * A radian value
         * </p>
         * @return float The equivalent of number in degrees
         */
        public static function rad2deg(float $num) : float {
            return rad2deg($num);
        }

        /**
         * Generate a random integer
         * @link https://php.net/manual/en/function.rand.php
         * @param int $min [optional]
         * @param int $max [optional]
         * @return int A pseudo random value between min
         * (or 0) and max (or Math::getRandMax(), inclusive).
         */
        public static function rand(int $min = 0, int $max = null) : int {
            return rand($min, $max === null ? Math::getRandMax() : $max);
        }

        /**
         * Returns the rounded value of val to specified precision (number of digits after the decimal point).
         * precision can also be negative or zero (default).
         * Note: PHP doesn't handle strings like "12,300.2" correctly by default. See converting from strings.
         * @link https://php.net/manual/en/function.round.php
         * @param int|float $num <p>
         * The value to round
         * </p>
         * @param int $precision [optional] <p>
         * The optional number of decimal digits to round to.
         * </p>
         * @param int $mode [optional] <p>
         * One of PHP_ROUND_HALF_UP,
         * PHP_ROUND_HALF_DOWN,
         * PHP_ROUND_HALF_EVEN, or
         * PHP_ROUND_HALF_ODD.
         * </p>
         * @return float The rounded value
         */
        public static function round(int|float $num, int $precision = 0, int $mode = PHP_ROUND_HALF_UP) : float {
            return round($num, $precision, $mode);
        }

        /**
         * Sine
         * @link https://php.net/manual/en/function.sin.php
         * @param float $num <p>
         * A value in radians
         * </p>
         * @return float The sine of arg
         */
        public static function sin(float $num) : float {
            return sin($num);
        }

        /**
         * Hyperbolic sine
         * @link https://php.net/manual/en/function.sinh.php
         * @param float $num <p>
         * The argument to process
         * </p>
         * @return float The hyperbolic sine of arg
         */
        public static function sinh(float $num) : float {
            return sinh($num);
        }

        /**
         * Square root
         * @link https://php.net/manual/en/function.sqrt.php
         * @param float $num <p>
         * The argument to process
         * </p>
         * @return float The square root of arg
         * or the special value NAN for negative numbers.
         */
        public static function sqrt(float $num) : float {
            return sqrt($num);
        }

        /**
         * Seed the random number generator
         * <p><strong>Note</strong>: As of PHP 7.1.0, {@see srand()} has been made
         * an alias of {@see mt_srand()}.
         * </p>
         * @link https://php.net/manual/en/function.srand.php
         * @param int $seed [optional] <p>
         * Optional seed value
         * </p>
         * @param int $mode [optional] <p>
         * Use one of the following constants to specify the implementation of the algorithm to use.
         * </p>
         * @return void
         */
        public static function srand(int $seed, int $mode = MT_RAND_MT19937) : void{
            srand($seed, $mode);
        }

        /**
         * Tangent
         * @link https://php.net/manual/en/function.tan.php
         * @param float $num <p>
         * The argument to process in radians
         * </p>
         * @return float The tangent of arg
         */
        public static function tan(float $num) : float {
            return tan($num);
        }

        /**
         * Hyperbolic tangent
         * @link https://php.net/manual/en/function.tanh.php
         * @param float $num <p>
         * The argument to process
         * </p>
         * @return float The hyperbolic tangent of arg
         */
        public static function tanh(float $num) : float {
            return tanh($num);
        }

    }