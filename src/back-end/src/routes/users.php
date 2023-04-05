<?php

use Boodschappenservice\core\Request;
use Boodschappenservice\core\Route;
use Boodschappenservice\objects\User;
use Boodschappenservice\utilities\API;
use Boodschappenservice\utilities\ResponseCode;
use Boodschappenservice\utilities\RegExp;

Route::get("/users", function(Request $request) {
    $users = User::getAll();
    API::printAndExit($users);
});

Route::handle(RegExp::compile("/^\/user\/(\d+)$/"), function(Request $request, array $matches) {
    $userId = intval($matches[1][0]);
    $user = User::get($userId);

    switch($request->method) {
        case "GET":
            API::printAndExit($user);
        case "DELETE":
            $user->delete();
            API::printAndExit([], ResponseCode::OK[0]);
        case "PATCH":
            $_PATCH = $request->body;
            $user->username = !empty($_PATCH['username']) ? $_PATCH['username'] : $user->username;
            $user->password = !empty($_PATCH['password']) ? password_hash($_PATCH['password'], PASSWORD_DEFAULT) : $user->password;
            $user->email = !empty($_PATCH['email']) ? $_PATCH['email'] : $user->email;
            $user->role = !empty($_PATCH['role']) ? $_PATCH['role'] : $user->role;
            $user->save();
            API::printAndExit($user);
        default:
            API::printAndExit([], ResponseCode::METHOD_NOT_ALLOWED[0]);
    }
});

Route::post("/user", function(Request $request) {
    try {
        API::printAndExit(User::create(
            $request->body['username'],
            $request->body['password'],
            $request->body['email'],
            $request->body['role']));
    } catch(\Exception $e) {
        API::printAndExit($e->getMessage(), $e->getCode());
    }
});
