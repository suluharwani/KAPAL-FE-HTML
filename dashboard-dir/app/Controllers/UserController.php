<?php namespace App\Controllers;

use App\Models\UserModel;

class UserController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        $role = $this->request->getGet('role');
        $search = $this->request->getGet('search');
        
        $data = [
            'title' => 'User Management',
            'users' => $this->userModel->getAllUsers($role, $search),
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/users/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Add New User',
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/users/create', $data);
    }

    public function store()
    {
        // Use the validation rules from UserModel
        $rules = $this->userModel->getValidationRules();
        $messages = $this->userModel->getValidationMessages();

        // Add password confirmation rule
        $rules['password_confirm'] = 'matches[password]';

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'full_name' => $this->request->getPost('full_name'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'role' => $this->request->getPost('role') ?? 'customer'
        ];

        if ($this->userModel->insert($data)) {
            return redirect()->to('/admin/users')->with('success', 'User added successfully');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }
    }

    public function edit($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found');
        }

        $data = [
            'title' => 'Edit User',
            'user_data' => $user,
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/users/edit', $data);
    }

    public function update($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found');
        }

        // Get validation rules from UserModel and adjust for update
        $rules = $this->userModel->getValidationRules(['user_id' => $id]);
        $messages = $this->userModel->getValidationMessages();

        // Password is optional for update
        unset($rules['password']);
        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[6]';
            $rules['password_confirm'] = 'matches[password]';
        }

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
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

        if ($this->userModel->update($id, $data)) {
            return redirect()->to('/admin/users')->with('success', 'User updated successfully');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }
    }

    public function delete($id)
    {
        // Prevent deleting own account
        if ($id == $this->session->get('user_id')) {
            return redirect()->to('/admin/users')->with('error', 'You cannot delete your own account');
        }

        if ($this->userModel->delete($id)) {
            return redirect()->to('/admin/users')->with('success', 'User deleted successfully');
        } else {
            return redirect()->to('/admin/users')->with('error', 'Failed to delete user');
        }
    }
}