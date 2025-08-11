<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'username' => 'required',
                'password' => 'required|min_length[6]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $model = new UserModel();
            $user = $model->where('username', $this->request->getPost('username'))->first();

            if (!$user || !password_verify($this->request->getPost('password'), $user['password'])) {
                return redirect()->back()->withInput()->with('error', 'Invalid username or password');
            }

            if ($user['role'] !== 'admin') {
                return redirect()->back()->withInput()->with('error', 'Access denied. Admin only.');
            }

            $session = session();
            $session->set([
                'user_id' => $user['user_id'],
                'username' => $user['username'],
                'role' => $user['role'],
                'logged_in' => true
            ]);

            return redirect()->to('/admin/dashboard');
        }

        return view('admin/auth/login');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/admin/login');
    }
}