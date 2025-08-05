<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Services;

class Login extends Controller
{
    public function index()
    {
        return view('login_view');
    }

public function process()
{
    $validation = Services::validation();
    $session = session();

    // Set validation rules
    $validation->setRules([
        'username' => 'required',
        'password' => 'required'
    ]);

    if (!$validation->withRequest($this->request)->run()) {
        return redirect()->back()->withInput()->with('errors', $validation->getErrors());
    }

    $username = $this->request->getPost('username');
    $password = $this->request->getPost('password');

    // Prepare API request
    $client = \Config\Services::curlrequest();
    $apiUrl = getenv('API_BASE_URL') . '/api/login';

    try {
        $response = $client->post($apiUrl, [
            'json' => [
                'username' => $username,
                'password' => $password
            ],
            'http_errors' => false
        ]);
        $body = json_decode($response->getBody(), true);

         // Stop execution to inspect the output
        if ($body['status'] == 200 && isset($body['data']['token'])) {
            
            // Login successful - Debugging step
            log_message('info', 'Login successful for user: ' . $username);
            log_message('debug', 'Token received: ' . $body['data']['token']);
            // var_dump($body['data']['user']['role']);
            // die(); // Debugging output
            // Store session data
            $ses = [
                'isLoggedIn' => true,
                'token' => $body['data']['token'],
                'username' => $username,
                'role'=> $body['data']['user']['role'],
            ];
            $session->set($ses);

            // Verify the session was set
            log_message('debug', 'Session data: ' . print_r($session->get(), true));

            // Debugging redirect
            log_message('info', 'Attempting redirect to dashboard');
            return redirect()->to(base_url('dashboard'))->with('success', 'Login successful');
        } else {
            $error = $body['message'] ?? 'Login failed. Please try again.';
            log_message('error', 'Login failed: ' . $error);
            return redirect()->back()->withInput()->with('error', $error);
        }
    } catch (\Exception $e) {
        log_message('error', 'Login exception: ' . $e->getMessage());
        return redirect()->back()->withInput()->with('error', 'Connection error: ' . $e->getMessage());
    }
}
    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/login');
    }
}