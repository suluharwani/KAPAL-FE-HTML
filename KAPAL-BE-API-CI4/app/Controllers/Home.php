<?php

namespace App\Controllers;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;
use App\Models\SettingModel;
use PhpParser\Node\Stmt\Echo_;

class Home extends BaseController
{
        use ResponseTrait;
    
        protected $userModel;
    
        protected $settingModel;
        public function __construct()
    {
        $this->userModel = new UserModel();
        $this->settingModel = new SettingModel();
        helper('jwt');
    }
    public function index()
    {

    $username = 'user';
    $password = 'admin123';
    
    $user = $this->userModel->where('username', $username)->first();
    
    if (!$user) {
        echo "User not found.";
    }
    
    $verify = password_verify( $password, $user['password']);
    if ($verify) {
        echo "$password";
        echo $user['password'];
        echo "Password verification successful.";
        // You can proceed with further actions, like generating a JWT token or redirecting the user.
        
        // Example of generating a JWT token
     } else {
        echo "$password";
        echo $user['password'];
        echo "Password verification failed.";

    }
}
}
