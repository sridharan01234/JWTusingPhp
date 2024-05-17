<?php
require 'vendor/autoload.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

$key = "s3cr3tK3y123!@#";
$issuedAt = time();
$expirationTime = $issuedAt + 3600;  // jwt valid for 1 hour from the issued time
$payload = [
    'iat' => $issuedAt,
    'exp' => $expirationTime,
    'data' => [
        'userId' => 456, // example user id
        'username' => 'randomUser'
    ]
];

$jwt = JWT::encode($payload, $key, 'HS256');
echo "JWT: " . $jwt . "<br>";

function getBearerToken() {
    $headers = apache_request_headers();
    if (isset($headers['Authorization'])) {
        $matches = [];
        if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
            return $matches[1];
        }
    }
    return null;
}

function verifyJWT($jwt, $key) {
    try {
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        return (array) $decoded;
    } catch (Exception $e) {
        echo 'Caught exception: ', $e->getMessage(), "\n";
        return null;
    }
}

// Example usage
$token = getBearerToken();
if ($token) {
    $decodedPayload = verifyJWT($token, $key);
    if ($decodedPayload) {
        echo "JWT is valid. Payload:\n";
        print_r($decodedPayload);
    } else {
        echo "JWT is invalid.\n";
    }
} else {
    echo "No JWT provided in the Authorization header.\n";
}