<?php

namespace App\Controllers;

use App\Libraries\ApiService;

class Auth extends BaseController
{
    protected $apiService;

    public function __construct()
    {
        $this->apiService = new ApiService();
        helper(['form', 'url']);
    }

    public function login()
    {
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'username' => 'required',
                'password' => 'required|min_length[6]',
            ];

            if (!$this->validate($rules)) {
                return view('auth/login', ['validation' => $this->validator]);
            }

            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');

            try {
                $response = $this->apiService->login($username, $password);
                
                if (isset($response['token'])) {
                    $userData = [
                        'user_id' => $response['user']['id'] ?? null,
                        'username' => $username,
                        'email' => $response['user']['email'] ?? '',
                        'full_name' => $response['user']['full_name'] ?? '',
                        'token' => $response['token'],
                        'isLoggedIn' => true,
                    ];

                    session()->set($userData);
                    return redirect()->to('/');
                } else {
                    session()->setFlashdata('error', 'Login gagal. Silakan coba lagi.');
                }
            } catch (\Exception $e) {
                session()->setFlashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }

            return redirect()->to('/login');
        }

        return view('auth/login');
    }

public function register()
{
    return view('auth/register');
}
public function reg(){
        $rules = [
            'username' => 'required|min_length[3]|max_length[50]',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]',
            'email' => 'required|valid_email',
            'full_name' => 'required|min_length[3]|max_length[100]',
            'phone' => 'required|min_length[10]|max_length[20]',
        ];
        
        if (!$this->validate($rules)) {

            return view('auth/register', [
                'validation' => $this->validator,
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'password' => $this->request->getPost('password'),
            'email' => $this->request->getPost('email'),
            'full_name' => $this->request->getPost('full_name'),
            'phone' => $this->request->getPost('phone'),
        ];

        try {
            $response = $this->apiService->register($data);
 
            if ($response && !isset($response['error'])) {
                session()->setFlashdata('success', 'Registrasi berhasil! Silakan login.');
                return redirect()->to('/login');
            } else {
                return view('auth/register', [
                    'errors' => ['api_error' => $response['message'] ?? 'Registrasi gagal. Silakan coba lagi.'],
                    'old' => $this->request->getPost()
                ]);
            }
        } catch (\Exception $e) {
            
            return view('auth/register', [
                'errors' => ['exception' => 'Terjadi kesalahan: ' . $e->getMessage()],
                'old' => $this->request->getPost()
            ]);
        }
}
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}