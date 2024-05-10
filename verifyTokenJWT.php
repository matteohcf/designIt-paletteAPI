<?php

// Include the Firebase JWT library
include_once("vendor/autoload.php");

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

// Enable error reporting for debugging
/* ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); */

/**
 * Function to verify the authenticity of a JWT token
 *
 * @param string $token The JWT token to be verified
 * @return mixed The decoded payload if the token is valid, false otherwise
 */

function verifyToken($token) {
    $key = "FFGGDDKSJ344";
    /* echo $token; */
    try {
        // Decode the JWT token using the secret key and specified algorithm
        $decodedToken = JWT::decode($token, new Key($key, 'HS256'));


        // If the token is valid, return the decoded payload
        return $decodedToken;
    } catch (Exception $e) {
        // Handle any exceptions that occur during decoding (invalid token, etc.)
        // Return false to indicate an invalid token
        return false;
    }
}
