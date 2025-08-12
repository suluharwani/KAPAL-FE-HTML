<?php namespace App\Models;

use CodeIgniter\Model;

class ContactModel extends Model
{
    protected $table = 'contacts';
    protected $primaryKey = 'contact_id';
    protected $allowedFields = ['name', 'email', 'phone', 'subject', 'message', 'status'];
    protected $returnType = 'array';
}