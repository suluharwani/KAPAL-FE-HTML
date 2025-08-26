<?php namespace App\Models;

use CodeIgniter\Model;

class ContactModel extends Model
{
    protected $table = 'contacts';
    protected $primaryKey = 'contact_id';
    protected $allowedFields = ['name', 'email', 'phone', 'subject', 'message', 'status', 'created_at'];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    
    public function __construct()
    {
        parent::__construct();
    }
}