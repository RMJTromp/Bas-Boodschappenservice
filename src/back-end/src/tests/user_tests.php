<?php

use Boodschappenservice\objects\User;
use Boodschappenservice\utilities\API;

function testUserRegistrationAndLogin()
{
    // Sample user data
    $username = 'testuserssss';
    $password = 'testpasswordssss';
    $email = 'testsss@example.com';
    $role = 'user';

    try {
        // Attempt to register a new user
        $newUser = User::register($username, $password, $email, $role);

        // Attempt to log in with the registered user
        $loggedInUser = User::login($username, $password);

        // If both registration and login are successful, return the user information
        API::printAndExit($loggedInUser, 200, 'User registered and logged in successfully.');

    } catch (\Exception $e) {
        // If there was an error during the registration or login, return the error message
        API::printAndExit(null, $e->getCode(), $e->getMessage());
    }
}