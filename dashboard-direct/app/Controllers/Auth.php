<?php namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/');
        }

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

            $session = session();
            $session->set([
                'user_id' => $user['user_id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'full_name' => $user['full_name'],
                'role' => $user['role'],
                'logged_in' => true
            ]);

            return redirect()->to('/');
        }

        return view('auth/login');
    }

    public function register()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/');
        }

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'username' => 'required|min_length[5]|max_length[50]|is_unique[users.username]',
                'email' => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[6]',
                'password_confirm' => 'required|matches[password]',
                'full_name' => 'required|min_length[3]|max_length[100]',
                'phone' => 'required|min_length[10]|max_length[20]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $model = new UserModel();
            $data = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'),
                'full_name' => $this->request->getPost('full_name'),
                'phone' => $this->request->getPost('phone'),
                'address' => $this->request->getPost('address'),
                'role' => 'customer'
            ];

            if ($model->save($data)) {
                return redirect()->to('/login')->with('success', 'Registration successful. Please login.');
            } else {
                return redirect()->back()->with('error', 'Failed to register. Please try again.');
            }
        }

        return view('auth/register');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }

    public function profile()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please login to view profile');
        }

        $model = new UserModel();
        $user = $model->find(session()->get('user_id'));

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'full_name' => 'required|min_length[3]|max_length[100]',
                'phone' => 'required|min_length[10]|max_length[20]',
                'address' => 'permit_empty'
            ];

            // Only validate password if provided
            if ($this->request->getPost('password')) {
                $rules['password'] = 'required|min_length[6]';
                $rules['password_confirm'] = 'required|matches[password]';
            }

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $data = [
                'user_id' => session()->get('user_id'),
                'full_name' => $this->request->getPost('full_name'),
                'phone' => $this->request->getPost('phone'),
                'address' => $this->request->getPost('address')
            ];

            // Only update password if provided
            if ($this->request->getPost('password')) {
                $data['password'] = $this->request->getPost('password');
            }

            if ($model->save($data)) {
                // Update session
                session()->set([
                    'full_name' => $data['full_name'],
                    'phone' => $data['phone']
                ]);

                return redirect()->to('/profile')->with('success', 'Profile updated successfully');
            } else {
                return redirect()->back()->with('error', 'Failed to update profile');
            }
        }

        return view('auth/profile', ['user' => $user]);
    }

    public function bookings()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please login to view bookings');
        }

        $bookingModel = new BookingModel();
        $bookings = $bookingModel->where('user_id', session()->get('user_id'))
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('auth/bookings', ['bookings' => $bookings]);
    }
}