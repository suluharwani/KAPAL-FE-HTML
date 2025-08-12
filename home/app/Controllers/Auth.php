<?php namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('/boats');
        }
        
        $data = [
            'title' => 'Login - Raja Ampat Boat Services',
            'validation' => \Config\Services::validation()
        ];
        
        $this->render('auth/login', $data);
    }

    public function attemptLogin()
    {
        if (!$this->validate([
            'email' => 'required|valid_email',
            'password' => 'required'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model = new UserModel();
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $model->where('email', $email)->first();

        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Email atau password salah');
        }

        // Set session
        $this->session->set([
            'isLoggedIn' => true,
            'userData' => [
                'user_id' => $user['user_id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'full_name' => $user['full_name'],
                'phone' => $user['phone'],
                'role' => $user['role']
            ]
        ]);

        return redirect()->to('/boats')->with('message', 'Login berhasil');
    }

    public function register()
    {
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('/boats');
        }
        
        $data = [
            'title' => 'Register - Raja Ampat Boat Services',
            'validation' => \Config\Services::validation()
        ];
        
        $this->render('auth/register', $data);
    }

    public function attemptRegister()
    {
        if (!$this->validate([
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'passconf' => 'required|matches[password]',
            'full_name' => 'required',
            'phone' => 'required'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model = new UserModel();

        $data = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'full_name' => $this->request->getPost('full_name'),
            'phone' => $this->request->getPost('phone'),
            'role' => 'customer'
        ];

        $model->save($data);

        return redirect()->to('/auth/login')->with('message', 'Registrasi berhasil. Silakan login.');
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/');
    }
}