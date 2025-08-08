<?php namespace App\Controllers;

use CodeIgniter\Controller;
class Auth extends Controller
{
    
    public function __construct()
    {

        helper(['form', 'url']);
        $this->session = \Config\Services::session();
    }
// Di Auth controller
 public function login()
    {
        
        // Jika sudah login, redirect ke dashboard
        if (session()->has('user_id')) {
            return redirect()->to('/admin/dashboard');
        }
        $method = $this->request->getMethod();
        if ( $method == 'POST') {
            
            $rules = [
                'username' => 'required',
                'password' => 'required|min_length[6]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $model = new \App\Models\UserModel();
            $user = $model->where('username', $this->request->getPost('username'))->first();

            if ($user && password_verify($this->request->getPost('password'), $user['password'])) {
                // Set session
                $userData = [
                    'user_id' => $user['user_id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'full_name' => $user['full_name'],
                    'role' => $user['role'],
                    'isLoggedIn' => true
                ];
                session()->set($userData);

                return redirect()->to('/admin/dashboard');
            }

            return redirect()->back()->withInput()->with('error', 'Username atau password salah');
        }
        

        return view('auth/login');
    }

    public function logout()
    {
        // Hapus semua data session
        session()->destroy();
        return redirect()->to('/login');
    }
    // Di controller
public function register()
{
    $userModel = new \App\Models\UserModel();
    
    $data = [
        'username' => $this->request->getPost('username'),
        'email' => $this->request->getPost('email'),
        'password' => $this->request->getPost('password'),
        'full_name' => $this->request->getPost('full_name'),
        'phone' => $this->request->getPost('phone'),
        'role' => 'customer' // Default role
    ];
    
    if ($userModel->save($data)) {
        return redirect()->to('/login')->with('success', 'Registrasi berhasil!');
    } else {
        return redirect()->back()->withInput()->with('errors', $userModel->errors());
    }
}

}