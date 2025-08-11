<?php namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $allowedFields = [
        'username', 'password', 'email', 'full_name', 'phone', 'address', 'role'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    public function getUsers($role = null)
    {
        $builder = $this->orderBy('created_at', 'DESC');

        if ($role) {
            $builder->where('role', $role);
        }

        return $builder->findAll();
    }

    public function getAdmins()
    {
        return $this->where('role', 'admin')->findAll();
    }

    public function getCustomers()
    {
        return $this->where('role', 'customer')->findAll();
    }
}