<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use RuntimeException;

class Jwt extends BaseConfig
{
    public string $algorithm = 'HS256';
    public int $expiration = 3600; // 1 hour in seconds
    
    public function getSecretKey(): string
    {
        $key = getenv('JWT_SECRET_KEY');
        if (empty($key)) {
            throw new RuntimeException('JWT_SECRET_KEY is not set in .env file');
        }
        return $key;
    }
}