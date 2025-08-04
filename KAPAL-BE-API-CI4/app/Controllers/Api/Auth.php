<?php namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;
use App\Models\SettingModel;

class Auth extends BaseApiController
{
    use ResponseTrait;

    /**
     * @var UserModel
     */
    protected $userModel;
    
    /**
     * @var SettingModel
     */
    protected $settingModel;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->settingModel = new SettingModel();
        helper('jwt');
    }

    /**
     * Register a new user with secure password hashing
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function register()
    {
        $rules = [
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'password' => 'required|min_length[8]|max_length[72]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'full_name' => 'required|min_length[3]|max_length[100]',
            'phone' => 'required|min_length[10]|max_length[20]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }
        $hashedPassword = $this->hashPassword($this->request->getVar('password'));
        $data = [
            'username' => $this->request->getVar('username'),
            'password' =>   $hashedPassword,
            'email' => $this->request->getVar('email'),
            'full_name' => $this->request->getVar('full_name'),
            'phone' => $this->request->getVar('phone'),
            'role' => 'customer',
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->userModel->save($data)) {
            return $this->respondCreated([
                'status' => 201,
                'message' => 'User registered successfully',
                'data' => [
                    'user_id' => $this->userModel->getInsertID(),
                    'hash' => $hashedPassword,
                ]
            ]);
        } else {
            return $this->failServerError('Failed to register user');
        }
    }

    /**
     * Authenticate a user and return JWT token
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function login()
    {
        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        $user = $this->userModel->where('username', $username)->first();

        if (!$user) {
            return $this->failUnauthorized("Invalid username");
        }

        if (!$this->verifyPassword($password, $user['password'])) {
            return $this->failUnauthorized("Invalid username or password");
        }

        $token = generateJWTToken($user['user_id'], $user['role']);

        return $this->respond([
            'status' => 200,
            'message' => 'Login successful',
            'data' => [
                'token' => $token,
                'user' => [
                    'user_id' => $user['user_id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'full_name' => $user['full_name'],
                    'role' => $user['role']
                ]
            ]
        ]);
    }

    /**
     * Securely hash password using PHP password_hash()
     * 
     * @param string $password Plain text password
     * @return string Hashed password
     */
    protected function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Verify password against hash using PHP password_verify()
     * 
     * @param string $password Plain text password
     * @param string $hash Hashed password
     * @return bool True if password matches hash
     */
    protected function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Check if hash needs rehashing
     * 
     * @param string $hash Hashed password
     * @return bool True if hash needs rehashing
     */
    protected function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_DEFAULT);
    }

    /**
     * Get current user profile
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function profile()
    {
        $userId = $this->request->user->user_id;
        $user = $this->userModel->find($userId);

        if (!$user) {
            return $this->failNotFound('User not found');
        }

        unset($user['password']);

        return $this->respond([
            'status' => 200,
            'data' => $user
        ]);
    }

    /**
     * Update user profile
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function updateProfile()
    {
        $userId = $this->request->user->user_id;
        $user = $this->userModel->find($userId);

        if (!$user) {
            return $this->failNotFound('User not found');
        }

        $rules = [
            'full_name' => 'permit_empty|min_length[3]|max_length[100]',
            'phone' => 'permit_empty|min_length[10]|max_length[20]',
            'address' => 'permit_empty'
        ];

        if ($this->request->getVar('email') && $this->request->getVar('email') !== $user['email']) {
            $rules['email'] = 'valid_email|is_unique[users.email]';
        }

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'full_name' => $this->request->getVar('full_name') ?? $user['full_name'],
            'phone' => $this->request->getVar('phone') ?? $user['phone'],
            'address' => $this->request->getVar('address') ?? $user['address']
        ];

        if ($this->request->getVar('email')) {
            $data['email'] = $this->request->getVar('email');
        }

        if ($this->userModel->update($userId, $data)) {
            return $this->respond([
                'status' => 200,
                'message' => 'Profile updated successfully'
            ]);
        } else {
            return $this->failServerError('Failed to update profile');
        }
    }

    /**
     * Change password
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function changePassword()
    {
        $userId = $this->request->user->user_id;
        $user = $this->userModel->find($userId);

        if (!$user) {
            return $this->failNotFound('User not found');
        }

        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[8]|max_length[72]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        if (!$this->verifyPassword($this->request->getVar('current_password'), $user['password'])) {
            return $this->failUnauthorized('Current password is incorrect');
        }

        $newPassword = $this->request->getVar('new_password');
        
        // Prevent password reuse
        if ($this->verifyPassword($newPassword, $user['password'])) {
            return $this->fail('New password must be different from current password', 400);
        }

        $data = [
            'password' => $this->hashPassword($newPassword),
            'password_changed_at' => date('Y-m-d H:i:s')
        ];

        if ($this->userModel->update($userId, $data)) {
            return $this->respond([
                'status' => 200,
                'message' => 'Password changed successfully'
            ]);
        } else {
            return $this->failServerError('Failed to change password');
        }
    }

    /**
     * Refresh JWT token
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function refreshToken()
    {
        $userId = $this->request->user->user_id;
        $user = $this->userModel->find($userId);
        
        if (!$user) {
            return $this->failUnauthorized('User not found');
        }

        $newToken = generateJWTToken($user['user_id'], $user['role']);

        return $this->respond([
            'status' => 200,
            'message' => 'Token refreshed',
            'data' => [
                'token' => $newToken
            ]
        ]);
    }

    /**
     * Test password hashing and verification (for debugging)
     * 
     * @param string $password Password to test
     * @return \CodeIgniter\HTTP\Response
     */
    public function testHash($password = 'test123')
    {
        $hash = $this->hashPassword($password);
        $verify = $this->verifyPassword($password, $hash);
        $needsRehash = $this->needsRehash($hash);
        
        return $this->respond([
            'input' => $password,
            'hash' => $hash,
            'verify' => $verify,
            'needs_rehash' => $needsRehash,
            'hash_info' => password_get_info($hash)
        ]);
    }
}