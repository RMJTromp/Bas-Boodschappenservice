<?php

use Boodschappenservice\objects\User;
use Boodschappenservice\utilities\API;

function testRegisterUser()
{
    // Sample user data
    $username = 'testuserssss';
    $password = 'testpasswordssss';
    $email = 'testsss@example.com';
    $role = 'user';

    try {
        // Attempt to register a new user
        $newUser = User::register($username, $password, $email, $role);

        // If the registration is successful, return the user information
        API::printAndExit($newUser, 200, 'User registered successfully.');

    } catch (\Exception $e) {
        // If there was an error during the registration, return the error message
        API::printAndExit(null, $e->getCode(), $e->getMessage());
    }
}
