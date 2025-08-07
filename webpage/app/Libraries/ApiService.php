<?php

namespace App\Libraries;

use CodeIgniter\HTTP\CURLRequest;
use Config\Services;

class ApiService
{
    protected $client;
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        $this->client = Services::curlrequest();
        $this->baseUrl = getenv('API_BASE_URL') ?: 'http://localhost:8080';
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function register($data)
    {
        $response = $this->client->post($this->baseUrl . '/api/register', [
            'json' => $data,
            'headers' => [
                'Accept' => 'application/json',
            ]
        ]);

        return json_decode($response->getBody(), true);
    }

    public function login($username, $password)
    {
        $response = $this->client->post($this->baseUrl . '/api/login', [
            'json' => [
                'username' => $username,
                'password' => $password
            ],
            'headers' => [
                'Accept' => 'application/json',
            ]
        ]);

        return json_decode($response->getBody(), true);
    }

    public function getProfile()
    {
        $response = $this->client->get($this->baseUrl . '/api/profile', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/json',
            ]
        ]);

        return json_decode($response->getBody(), true);
    }

    public function updateProfile($data)
    {
        $response = $this->client->put($this->baseUrl . '/api/profile', [
            'json' => $data,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/json',
            ]
        ]);

        return json_decode($response->getBody(), true);
    }

    public function getAllBoats()
    {
        $response = $this->client->get($this->baseUrl . '/api/boats', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/json',
            ]
        ]);

        return json_decode($response->getBody(), true);
    }

    public function createBooking($data)
    {
        $response = $this->client->post($this->baseUrl . '/api/bookings', [
            'json' => $data,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/json',
            ]
        ]);

        return json_decode($response->getBody(), true);
    }

    public function getAllBookings()
    {
        $response = $this->client->get($this->baseUrl . '/api/bookings', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/json',
            ]
        ]);

        return json_decode($response->getBody(), true);
    }
}