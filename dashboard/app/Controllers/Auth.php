<?php namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login()
    {
        if ($this->request->getMethod() === 'post') {
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');

            // Panggil API login
            $apiUrl = getenv('API_BASE_URL') . '/api/login';
            $client = \Config\Services::curlrequest();
            
            try {
                $response = $client->post($apiUrl, [
                    'json' => [
                        'username' => $username,
                        'password' => $password
                    ]
                ]);

                $result = json_decode($response->getBody(), true);
                
                if (isset($result['token'])) {
                    // Simpan token di session
                    session()->set([
                        'token' => $result['token'],
                        'isLoggedIn' => true,
                        'userData' => $result['user']
                    ]);

                    // Redirect ke dashboard admin
                    return redirect()->to('/admin/dashboard');
                } else {
                    return redirect()->back()->with('error', 'Login gagal. Periksa kembali username dan password.');
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        }

        return view('auth/login');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}