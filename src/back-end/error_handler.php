<?php

    use Boodschappenservice\utilities\API;

    function log_error( $num, $str, $file, $line, $context = null ) {
        log_exception( new ErrorException( $str, 500, $num, $file, $line ) );
    }

    function log_exception(Error|Exception $e) {
        global $errors;
        $errors[] = [
            "type" => get_class( $e ),
            "code" => $e->getCode(),
            "message" => $e->getMessage(),
            "file" => $e->getFile(),
            "line" => $e->getLine(),
            "trace" => $e->getTrace()
        ];
    }

    function check_for_fatal() {
        $error = error_get_last();
        if ($error["type"] ?? null == E_ERROR) {
            log_exception(new ErrorException($error["message"], 500, $error["type"], $error["file"], $error["line"]));
        }

        global $errors;
        if (!empty($errors)) {
            ob_clean();
            $lastError = $errors[count($errors) - 1];
            API::printAndExit([], $lastError["code"] == 0 || $lastError['type'] === "mysqli_sql_exception" ? 500 : $lastError["code"], null, strtolower($_ENV["DEBUG"]) === "true" ? ["stacktrace" => $errors] : []);
        }

        ob_end_flush();
    }

    ob_start();
    register_shutdown_function( "check_for_fatal" );
    set_error_handler( "log_error" );
    set_exception_handler( "log_exception" );
    ini_set( "display_errors", "on" );
    error_reporting( E_ALL );