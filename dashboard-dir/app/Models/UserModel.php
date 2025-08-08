<?php namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    
    protected $allowedFields = [
        'username',
        'password',
        'email',
        'full_name',
        'phone',
        'address',
        'role'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];
    
    protected $validationRules = [
        'username' => 'required|min_length[4]|max_length[50]|is_unique[users.username,user_id,{user_id}]',
        'email' => 'required|valid_email|max_length[100]|is_unique[users.email,user_id,{user_id}]',
        'password' => 'required|min_length[6]',
        'password_confirm' => 'matches[password]',
        'full_name' => 'required|max_length[100]',
        'phone' => 'required|max_length[20]',
        'role' => 'required|in_list[admin,customer]'
    ];
    
    protected $validationMessages = [
        'username' => [
            'is_unique' => 'Username sudah digunakan',
            'required' => 'Username harus diisi'
        ],
        'email' => [
            'is_unique' => 'Email sudah digunakan',
            'valid_email' => 'Email tidak valid'
        ],
        'password' => [
            'min_length' => 'Password minimal 6 karakter'
        ]
    ];
    
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }
    
    // Method untuk login
    public function getUserByUsername($username)
    {
        return $this->where('username', $username)->first();
    }
    
    // Method untuk verifikasi password
    public function verifyPassword($password, $hashedPassword)
    {
        return password_verify($password, $hashedPassword);
    }
    
    // Method untuk mendapatkan semua user dengan filter
    public function getAllUsers($role = null, $search = null)
    {
        $builder = $this;
        
        if ($role) {
            $builder->where('role', $role);
        }
        
        if ($search) {
            $builder->groupStart()
                   ->like('username', $search)
                   ->orLike('email', $search)
                   ->orLike('full_name', $search)
                   ->orLike('phone', $search)
                   ->groupEnd();
        }
        
        return $builder->orderBy('created_at', 'DESC')->findAll();
    }
    
    // Method untuk update profile tanpa password
    public function updateProfile($id, $data)
    {
        if (empty($data['password'])) {
            unset($data['password']);
        }
        return $this->update($id, $data);
    }
    
    // Method untuk mengecek duplikat username/email
    public function isUnique($field, $value, $ignoreId = null)
    {
        $builder = $this->where($field, $value);
        
        if ($ignoreId) {
            $builder->where('user_id !=', $ignoreId);
        }
        
        return $builder->countAllResults() === 0;
    }
}