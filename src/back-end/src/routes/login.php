<?php

    use Boodschappenservice\core\Route;
    use Boodschappenservice\objects\User;
    use Boodschappenservice\utilities\API;
    use Boodschappenservice\core\Request;
    use Boodschappenservice\utilities\ResponseCode;
    use Firebase\JWT\JWT;

    /**
     * @author Joran
     */

    function saveSession(User $user) {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $_SESSION['user'] = $user->userId;
    }

    Route::post('/register', function(Request $request) {
        $username = $request->body['username'] ?? 'user';
        $password = $request->body['password'] ?? 'user';
        $email = $request->body['email'] ?? 'user@user.com';

        $user = User::register($username, $password, $email);
        saveSession($user);

        API::printAndExit($user);
    });

    Route::post('/login', function(Request $request) {
        $username = $request->body['username'] ?? 'user';
        $password = $request->body['password'] ?? 'user';

        $user = User::login($username, $password);
        saveSession($user);

        API::printAndExit($user);
    });

    Route::get('/me', function(Request $request) {
        $id = $_SESSION['user'] ?? null;
        if (!$id) API::printAndExit(null, ResponseCode::UNAUTHORIZED[0]);

        $user = User::get($id);
        API::printAndExit($user);
    });

    Route::post('/logout', function() {
        unset($_SESSION['user']);
        session_destroy();
        API::printAndExit(null);
    });