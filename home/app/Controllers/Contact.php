<?php namespace App\Controllers;

use App\Models\ContactModel;

class Contact extends BaseController
{
    protected $contactModel;
    
    public function __construct()
    {
        $this->contactModel = new ContactModel();
    }
    
    public function index()
    {
        $data = [
            'title' => 'Kontak Kami - Raja Ampat Boat Services',
            'active' => 'contact',
            'validation' => \Config\Services::validation()
        ];
        
        return $this->render('contact/index', $data);
    }
    
    public function submit()
    {
        // Validasi input
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
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'Email harus diisi',
                    'valid_email' => 'Format email tidak valid'
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
        
        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        try {
            // Data untuk disimpan
            $data = [
                'name' => $this->request->getPost('name'),
                'email' => $this->request->getPost('email'),
                'phone' => $this->request->getPost('phone'),
                'subject' => $this->request->getPost('subject'),
                'message' => $this->request->getPost('message'),
                'status' => 'unread',
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            // Simpan ke database
            if ($this->contactModel->save($data)) {
                // Opsional: Kirim email notifikasi ke admin
                $this->sendNotificationEmail($data);
                
                return redirect()->to('/contact')
                    ->with('success', 'Pesan Anda telah terkirim. Kami akan segera menghubungi Anda.');
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Terjadi kesalahan saat mengirim pesan. Silakan coba lagi.');
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error sending contact message: ' . $e->getMessage());
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
            
            $email->setFrom('noreply@rajaampatboats.com', 'Raja Ampat Boat Services');
            $email->setTo('admin@rajaampatboats.com'); // Ganti dengan email admin
            
            $email->setSubject('Pesan Kontak Baru: ' . $contactData['subject']);
            
            $message = "Anda memiliki pesan kontak baru:\n\n";
            $message .= "Nama: " . $contactData['name'] . "\n";
            $message .= "Email: " . $contactData['email'] . "\n";
            $message .= "Telepon: " . ($contactData['phone'] ?: 'Tidak diisi') . "\n";
            $message .= "Subjek: " . $contactData['subject'] . "\n";
            $message .= "Pesan:\n" . $contactData['message'] . "\n\n";
            $message .= "Tanggal: " . date('d/m/Y H:i');
            
            $email->setMessage($message);
            
            return $email->send();
            
        } catch (\Exception $e) {
            log_message('error', 'Failed to send notification email: ' . $e->getMessage());
            return false;
        }
    }
}