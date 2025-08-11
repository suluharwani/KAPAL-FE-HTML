<?php namespace App\Models;

use CodeIgniter\Model;

class ContactModel extends Model
{
    protected $table = 'contacts';
    protected $primaryKey = 'contact_id';
    protected $allowedFields = [
        'name', 'email', 'phone', 'subject', 'message', 'status'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    public function getContacts($status = null)
    {
        $builder = $this->orderBy('created_at', 'DESC');

        if ($status) {
            $builder->where('status', $status);
        }

        return $builder->findAll();
    }

    public function updateStatus($contactId, $status)
    {
        return $this->update($contactId, ['status' => $status]);
    }

    public function getUnreadCount()
    {
        return $this->where('status', 'unread')->countAllResults();
    }
}