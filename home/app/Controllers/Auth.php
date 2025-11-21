<?php namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Email\Email;
use Config\Services;

class Auth extends BaseController
{
    protected $googleClient;

    public function __construct()
    {
        helper('text');
        $this->initGoogleClient();
    }

    protected function initGoogleClient()
    {
        $this->googleClient = new \Google\Client();
        $this->googleClient->setClientId($_ENV['GOOGLE_OAUTH_CLIENT_ID']);
        $this->googleClient->setClientSecret($_ENV['GOOGLE_OAUTH_CLIENT_SECRET']);
        $this->googleClient->setRedirectUri($_ENV['GOOGLE_OAUTH_REDIRECT_URI']);
        $this->googleClient->addScope('email');
        $this->googleClient->addScope('profile');
    }
      public function google()
    {
        $authUrl = $this->googleClient->createAuthUrl();
        return redirect()->to($authUrl);
    }

    public function googleCallback()
    {
        try {
            $code = $this->request->getGet('code');
            
            if (!$code) {
                throw new \Exception('Authorization code not found');
            }

            // Exchange authorization code for access token
            $token = $this->googleClient->fetchAccessTokenWithAuthCode($code);
            
            if (isset($token['error'])) {
                throw new \Exception($token['error_description'] ?? $token['error']);
            }

            $this->googleClient->setAccessToken($token);

            // Get user info from Google
            $googleService = new \Google\Service\Oauth2($this->googleClient);
            $googleUser = $googleService->userinfo->get();

            $model = new UserModel();

            // Check if user already exists
            $user = $model->where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Create new user
                $username = $this->generateUniqueUsername($googleUser->getName(), $model);
                
                $userData = [
                    'username' => $username,
                    'email' => $googleUser->getEmail(),
                    'password' => password_hash(random_string('alnum', 16), PASSWORD_DEFAULT),
                    'full_name' => $googleUser->getName(),
                    'phone' => '',
                    'role' => 'customer',
                    'email_verified' => 1, // Google already verified the email
                    'verification_code' => null,
                    'verification_expires' => null
                ];

                $userId = $model->insert($userData);
                $user = $model->find($userId);
            }

            // Set session
            $this->session->set([
                'isLoggedIn' => true,
                'userData' => [
                    'user_id' => $user['user_id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'full_name' => $user['full_name'],
                    'phone' => $user['phone'],
                    'role' => $user['role']
                ]
            ]);

            return redirect()->to('')->with('message', 'Login dengan Google berhasil');

        } catch (\Exception $e) {
            log_message('error', 'Google OAuth Error: ' . $e->getMessage());
            return redirect()->to('/auth/login')->with('error', 'Login dengan Google gagal: ' . $e->getMessage());
        }
    }

    protected function generateUniqueUsername($name, $model)
    {
        $baseUsername = preg_replace('/[^a-zA-Z0-9_]/', '', strtolower($name));
        $baseUsername = substr($baseUsername, 0, 45); // Ensure it fits in username field
        $username = $baseUsername;
        $counter = 1;

        // Check if username exists and generate unique one
        while ($model->where('username', $username)->first()) {
            $username = $baseUsername . $counter;
            $counter++;
            
            if ($counter > 100) {
                // Fallback if we can't find a unique username
                $username = $baseUsername . '_' . random_string('alnum', 8);
                break;
            }
        }

        return $username;
    }
    public function login()
    {
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('/boats');
        }
        
        $data = [
            'title' => 'Login - Raja Ampat Boat Services',
            'validation' => Services::validation()
        ];
        $this->render('auth/login', $data);
    }

    public function attemptLogin()
    {
        if (!$this->validate([
            'email' => 'required|valid_email',
            'password' => 'required'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model = new UserModel();
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $model->where('email', $email)->first();

        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Email atau password salah');
        }

        if (!$user['email_verified']) {
            return redirect()->back()->withInput()->with('error', 'Email belum diverifikasi. Silakan cek email Anda.');
        }

        // Set session
        $this->session->set([
            'isLoggedIn' => true,
            'userData' => [
                'user_id' => $user['user_id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'full_name' => $user['full_name'],
                'phone' => $user['phone'],
                'role' => $user['role']
            ]
        ]);

        return redirect()->to('')->with('message', 'Login berhasil');
    }

    public function register()
    {
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('');
        }
        
        $data = [
            'title' => 'Register - Raja Ampat Boat Services',
            'validation' => Services::validation()
        ];
        
        $this->render('auth/register', $data);
    }

    public function attemptRegister()
    {
        if (!$this->validate([
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'passconf' => 'required|matches[password]',
            'full_name' => 'required',
            'phone' => 'required'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model = new UserModel();

        $data = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'full_name' => $this->request->getPost('full_name'),
            'phone' => $this->request->getPost('phone'),
            'role' => 'customer'
        ];

        if ($model->save($data)) {
            $user = $model->where('email', $data['email'])->first();
            $this->sendVerificationEmail($user);
            return redirect()->to('/auth/login')->with('message', 'Registrasi berhasil. Silakan cek email Anda untuk verifikasi.');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal melakukan registrasi');
    }

    protected function sendVerificationEmail($user)
    {
        $email = Services::email();
        
        $email->setTo($user['email']);
        $email->setSubject('Verifikasi Email - Raja Ampat Boat Services');
        
        $verificationLink = base_url("auth/verify/{$user['verification_code']}");
        
        $message = view('emails/verification', [
            'user' => $user,
            'verificationLink' => $verificationLink
        ]);
        
        $email->setMessage($message);
        
        return $email->send();
    }

    public function verify($code)
    {
        $model = new UserModel();
        
        if ($model->verifyUser($code)) {
            return redirect()->to('/auth/login')->with('message', 'Email berhasil diverifikasi. Silakan login.');
        }
        
        return redirect()->to('/auth/login')->with('error', 'Kode verifikasi tidak valid atau sudah kadaluarsa.');
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/');
    }
    // Di dalam Auth controller
public function check()
{
    return $this->response->setJSON([
        'isLoggedIn' => session()->get('isLoggedIn') ?? false
    ]);
}

}