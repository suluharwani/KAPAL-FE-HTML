<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Config\Constants;
use Config\Services;

if (!function_exists('generateJWTToken')) {
    /**
     * Generate JWT Token
     *
     * @param int|string $userId
     * @param string $role
     * @return string
     */
    function generateJWTToken($userId, $role): string
    {
        $key = $_ENV['JWT_SECRET_KEY'];
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600; // Token valid for 1 hour

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'sub' => $userId,
            'role' => $role
        ];

        return JWT::encode($payload, $key, 'HS256');
    }
}

if (!function_exists('validateJWTToken')) {
    /**
     * Validate JWT Token
     *
     * @param string $token
     * @return object|false
     */
    function validateJWTToken(string $token)
    {
        try {
            $key = Constants::getSecretKey();
            return JWT::decode($token, new Key($key, 'HS256'));
        } catch (Exception $e) {
            log_message('error', 'JWT Validation Error: ' . $e->getMessage());
            return false;
        }
    }
}