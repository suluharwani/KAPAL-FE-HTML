<?php namespace App\Controllers;

use App\Models\ContactModel;

class Contact extends BaseController
{
    protected $contactModel;
    protected $validation;
    
    public function __construct()
    {
        $this->contactModel = new ContactModel();
        $this->validation = \Config\Services::validation();
    }
    
    public function index()
    {
        $data = [
            'title' => 'Kontak Kami - Raja Ampat Boat Services',
            'active' => 'contact',
            'validation' => $this->validation
        ];
        
        return $this->render('contact/index', $data);
    }
    
    public function submit()
    {
        // Check if it's a POST request
        if (!$this->request->is('post')) {
            return redirect()->to('/contact');
        }
        
        // Validation rules
        $rules = [
            'name' => [
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'Nama harus diisi',
                    'min_length' => 'Nama minimal 3 karakter',
                    'max_length' => 'Nama maksimal 100 karakter'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|max_length[100]',
                'errors' => [
                    'required' => 'Email harus diisi',
                    'valid_email' => 'Format email tidak valid',
                    'max_length' => 'Email maksimal 100 karakter'
                ]
            ],
            'phone' => [
                'rules' => 'permit_empty|min_length[10]|max_length[15]',
                'errors' => [
                    'min_length' => 'Nomor telepon minimal 10 digit',
                    'max_length' => 'Nomor telepon maksimal 15 digit'
                ]
            ],
            'subject' => [
                'rules' => 'required|min_length[5]|max_length[255]',
                'errors' => [
                    'required' => 'Subjek harus diisi',
                    'min_length' => 'Subjek minimal 5 karakter',
                    'max_length' => 'Subjek maksimal 255 karakter'
                ]
            ],
            'message' => [
                'rules' => 'required|min_length[10]',
                'errors' => [
                    'required' => 'Pesan harus diisi',
                    'min_length' => 'Pesan minimal 10 karakter'
                ]
            ]
        ];
        
        // Validate input
        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        try {
            // Prepare data
            $data = [
                'name' => $this->request->getPost('name', FILTER_SANITIZE_STRING),
                'email' => $this->request->getPost('email', FILTER_SANITIZE_EMAIL),
                'phone' => $this->request->getPost('phone', FILTER_SANITIZE_STRING),
                'subject' => $this->request->getPost('subject', FILTER_SANITIZE_STRING),
                'message' => $this->request->getPost('message', FILTER_SANITIZE_STRING),
                'status' => 'unread'
            ];
            
            // Save to database using model method
            $result = $this->contactModel->saveContact($data);
            
            if ($result['success']) {
                // Optional: Send notification email
                $this->sendNotificationEmail($data);
                
                return redirect()->to('/contact')
                    ->with('success', 'Pesan Anda telah terkirim. Kami akan segera menghubungi Anda.');
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $result['errors'])
                    ->with('error', 'Terjadi kesalahan saat mengirim pesan.');
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Contact Controller Error: ' . $e->getMessage());
            log_message('error', 'Trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi nanti.');
        }
    }
    
    /**
     * Kirim email notifikasi ke admin (opsional)
     */
    private function sendNotificationEmail($contactData)
    {
        try {
            $email = \Config\Services::email();
            
            $config = [
                'protocol' => 'smtp',
                'SMTPHost' => 'smtp.gmail.com',
                'SMTPUser' => 'your-email@gmail.com',
                'SMTPPass' => 'your-password',
                'SMTPPort' => 587,
                'SMTPCrypto' => 'tls',
                'mailType' => 'text'
            ];
            
            $email->initialize($config);
            
            $email->setFrom('noreply@rajaampatboats.com', 'Raja Ampat Boat Services');
            $email->setTo('admin@rajaampatboats.com');
            
            $email->setSubject('Pesan Kontak Baru: ' . $contactData['subject']);
            
            $message = "PESAN KONTAK BARU\n";
            $message .= "================\n\n";
            $message .= "Nama: " . $contactData['name'] . "\n";
            $message .= "Email: " . $contactData['email'] . "\n";
            $message .= "Telepon: " . ($contactData['phone'] ?: 'Tidak diisi') . "\n";
            $message .= "Subjek: " . $contactData['subject'] . "\n\n";
            $message .= "Pesan:\n";
            $message .= str_repeat("-", 50) . "\n";
            $message .= $contactData['message'] . "\n";
            $message .= str_repeat("-", 50) . "\n\n";
            $message .= "Tanggal: " . date('d/m/Y H:i:s') . "\n";
            $message .= "IP Address: " . $this->request->getIPAddress() . "\n";
            
            $email->setMessage($message);
            
            if (!$email->send()) {
                log_message('error', 'Email Error: ' . $email->printDebugger(['headers']));
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Email Notification Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Debug method untuk testing
     */
    public function test()
    {
        // Test database connection
        try {
            $db = \Config\Database::connect();
            $db->query('SELECT 1');
            echo "Database connection: OK<br>";
        } catch (\Exception $e) {
            echo "Database connection failed: " . $e->getMessage() . "<br>";
        }
        
        // Test table exists
        try {
            $tables = $db->listTables();
            if (in_array('contacts', $tables)) {
                echo "Table 'contacts' exists<br>";
            } else {
                echo "Table 'contacts' does NOT exist<br>";
            }
        } catch (\Exception $e) {
            echo "Table check failed: " . $e->getMessage() . "<br>";
        }
        
        // Test model
        try {
            $model = new ContactModel();
            echo "Model loaded successfully<br>";
        } catch (\Exception $e) {
            echo "Model load failed: " . $e->getMessage() . "<br>";
        }
    }
}