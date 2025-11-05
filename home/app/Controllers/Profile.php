<?php namespace App\Controllers;

use App\Models\UserModel;

class Profile extends BaseController
{
    public function index()
    {
        // Pastikan user sudah login
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        
        $userModel = new UserModel();
        $user_id = $_SESSION['userData']['user_id'];
        $user = $userModel->find($user_id);
        
        $data = [
            'user_id'=>$user_id,
            'title' => 'Profil Saya',
            'user' => $user,
            'active' => 'profile'
        ];
        
        return $this->render('profile/index', $data);
    }
    
    public function update()
    {
        // Pastikan user sudah login
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        
        $validation = \Config\Services::validation();
        $validation->setRules([
            'full_name' => 'required|min_length[3]|max_length[100]',
            'phone' => 'required|min_length[10]|max_length[20]',
            'address' => 'permit_empty|max_length[255]'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        $userModel = new UserModel();
        $user_id = $_SESSION['userData']['user_id'];
        
        $data = [
            'full_name' => $this->request->getPost('full_name'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($userModel->update($user_id, $data)) {
            return redirect()->to('/profile')->with('success', 'Profil berhasil diperbarui');
        } else {
            return redirect()->back()->with('error', 'Gagal memperbarui profil');
        }
    }
    
    public function changePassword()
    {
        // Pastikan user sudah login
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        
        $validation = \Config\Services::validation();
        $validation->setRules([
            'current_password' => 'required',
            'new_password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        $userModel = new UserModel();
        $user_id = $_SESSION['userData']['user_id'];
        $user = $userModel->find($user_id);
        
        // Verifikasi password saat ini
        if (!password_verify($this->request->getPost('current_password'), $user['password'])) {
            return redirect()->back()->with('error', 'Password saat ini tidak sesuai');
        }
        
        // Update password baru
        $data = [
            'password' => password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($userModel->update($user_id, $data)) {
            return redirect()->to('/profile')->with('success', 'Password berhasil diubah');
        } else {
            return redirect()->back()->with('error', 'Gagal mengubah password');
        }
    }
}