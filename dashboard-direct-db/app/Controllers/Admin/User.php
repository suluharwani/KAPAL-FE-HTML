<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class User extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $role = $this->request->getGet('role') ?? null;
        
        $data = [
            'users' => $this->userModel->getUsers($role),
            'roleFilter' => $role
        ];

        return view('admin/user/index', $data);
    }

    public function add()
    {
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'username' => 'required|min_length[5]|max_length[50]|is_unique[users.username]',
                'password' => 'required|min_length[6]',
                'password_confirm' => 'required|matches[password]',
                'email' => 'required|valid_email|is_unique[users.email]',
                'full_name' => 'required|min_length[3]|max_length[100]',
                'phone' => 'required|min_length[10]|max_length[20]',
                'role' => 'required|in_list[admin,customer]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $data = [
                'username' => $this->request->getPost('username'),
                'password' => $this->request->getPost('password'),
                'email' => $this->request->getPost('email'),
                'full_name' => $this->request->getPost('full_name'),
                'phone' => $this->request->getPost('phone'),
                'address' => $this->request->getPost('address'),
                'role' => $this->request->getPost('role')
            ];

            if ($this->userModel->save($data)) {
                return redirect()->to('/admin/user')->with('success', 'User added successfully');
            } else {
                return redirect()->back()->with('error', 'Failed to add user');
            }
        }

        return view('admin/user/add');
    }

    public function edit($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/admin/user')->with('error', 'User not found');
        }

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'username' => "required|min_length[5]|max_length[50]|is_unique[users.username,user_id,{$id}]",
                'email' => "required|valid_email|is_unique[users.email,user_id,{$id}]",
                'full_name' => 'required|min_length[3]|max_length[100]',
                'phone' => 'required|min_length[10]|max_length[20]',
                'role' => 'required|in_list[admin,customer]'
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
                'user_id' => $id,
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'full_name' => $this->request->getPost('full_name'),
                'phone' => $this->request->getPost('phone'),
                'address' => $this->request->getPost('address'),
                'role' => $this->request->getPost('role')
            ];

            // Only update password if provided
            if ($this->request->getPost('password')) {
                $data['password'] = $this->request->getPost('password');
            }

            if ($this->userModel->save($data)) {
                return redirect()->to('/admin/user')->with('success', 'User updated successfully');
            } else {
                return redirect()->back()->with('error', 'Failed to update user');
            }
        }

        return view('admin/user/edit', ['user' => $user]);
    }

    public function delete($id)
    {
        // Prevent deleting yourself
        if ($id == session()->get('user_id')) {
            return redirect()->to('/admin/user')->with('error', 'You cannot delete your own account');
        }

        if ($this->userModel->delete($id)) {
            return redirect()->to('/admin/user')->with('success', 'User deleted successfully');
        } else {
            return redirect()->to('/admin/user')->with('error', 'Failed to delete user');
        }
    }
}