<?php namespace App\Models;

use CodeIgniter\Model;

class ContactModel extends Model
{
    protected $table = 'contacts';
    protected $primaryKey = 'contact_id';
    protected $allowedFields = ['name', 'email', 'phone', 'subject', 'message', 'status'];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    // Validation rules
    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[100]',
        'email' => 'required|valid_email|max_length[100]',
        'phone' => 'permit_empty|min_length[10]|max_length[15]',
        'subject' => 'required|min_length[5]|max_length[255]',
        'message' => 'required|min_length[10]'
    ];
    
    protected $validationMessages = [
        'name' => [
            'required' => 'Nama harus diisi',
            'min_length' => 'Nama minimal 3 karakter',
            'max_length' => 'Nama maksimal 100 karakter'
        ],
        'email' => [
            'required' => 'Email harus diisi',
            'valid_email' => 'Format email tidak valid',
            'max_length' => 'Email maksimal 100 karakter'
        ],
        'phone' => [
            'min_length' => 'Nomor telepon minimal 10 digit',
            'max_length' => 'Nomor telepon maksimal 15 digit'
        ],
        'subject' => [
            'required' => 'Subjek harus diisi',
            'min_length' => 'Subjek minimal 5 karakter',
            'max_length' => 'Subjek maksimal 255 karakter'
        ],
        'message' => [
            'required' => 'Pesan harus diisi',
            'min_length' => 'Pesan minimal 10 karakter'
        ]
    ];
    
    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }
    
    /**
     * Simpan pesan kontak dengan error handling
     */
    public function saveContact($data)
    {
        try {
            // Validasi data
            if (!$this->validate($data)) {
                return [
                    'success' => false,
                    'errors' => $this->errors()
                ];
            }
            
            // Pastikan status selalu ada
            if (!isset($data['status'])) {
                $data['status'] = 'unread';
            }
            
            // Simpan ke database
            $result = $this->insert($data);
            
            if ($result) {
                return [
                    'success' => true,
                    'id' => $result
                ];
            } else {
                return [
                    'success' => false,
                    'errors' => ['Gagal menyimpan data ke database']
                ];
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error saving contact: ' . $e->getMessage());
            return [
                'success' => false,
                'errors' => ['Terjadi kesalahan sistem: ' . $e->getMessage()]
            ];
        }
    }
    
    /**
     * Get all contacts with pagination
     */
    public function getAllContacts($perPage = 10)
    {
        return $this->orderBy('created_at', 'DESC')
                   ->paginate($perPage);
    }
}