<?php namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $allowedFields = [
        'username', 'email', 'password', 'full_name', 
        'phone', 'address', 'role', 'email_verified', 
        'verification_code', 'verification_expires'
    ];
    
    protected $beforeInsert = ['hashPassword', 'setVerification'];
    protected $beforeUpdate = ['hashPassword'];
    
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }
    
    protected function setVerification(array $data)
    {
        if (isset($data['data']['email'])) {
            $data['data']['email_verified'] = 0;
            $data['data']['verification_code'] = bin2hex(random_bytes(16));
            $data['data']['verification_expires'] = date('Y-m-d H:i:s', time() + 3600); // 1 hour expiration
        }
        return $data;
    }
    
    public function verifyUser($code)
    {
        $user = $this->where('verification_code', $code)
                     ->where('verification_expires >', date('Y-m-d H:i:s'))
                     ->first();
        
        if ($user) {
            return $this->update($user['user_id'], [
                'email_verified' => 1,
                'verification_code' => null,
                'verification_expires' => null
            ]);
        }
        
        return false;
    }
    
    public function getUserByEmail($email)
    {
        return $this->where('email', $email)->first();
    }
    // UserModel.php - tambahkan method untuk guest
public function createGuestAccount($userData)
{
    // Untuk guest, kita tidak perlu hash password karena tidak akan login
    $data = [
        'username' => $userData['username'],
        'email' => $userData['email'],
        'password' => $userData['password'], // Plain text
        'full_name' => $userData['full_name'],
        'phone' => $userData['phone'],
        'role' => 'customer',
        'email_verified' => 1,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    return $this->insert($data);
}
}