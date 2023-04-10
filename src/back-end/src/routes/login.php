<?php

use Boodschappenservice\core\Route;
use Boodschappenservice\objects\User;
use Boodschappenservice\utilities\API;
use Boodschappenservice\core\Request;

Route::post('/register', function(Request $request) {
    $username = $request->body['username'] ?? 'user';
    $password = $request->body['password'] ?? 'user';
    $email = $request->body['email'] ?? 'user@user.com';

    try {
        $user = User::register($username, $password, $email);
        API::printAndExit($user);
    } catch (\Exception $e) {
        API::printAndExit([], $e->getCode(), $e->getMessage());
    }
});

Route::post('/login', function(Request $request) {
    $username = $request->body['username'] ?? 'user';
    $password = $request->body['password'] ?? 'user';

    try {
        $user = User::login($username, $password);
        API::printAndExit($user);
    } catch (\Exception $e) {
        API::printAndExit([], $e->getCode(), $e->getMessage());
    }
});
