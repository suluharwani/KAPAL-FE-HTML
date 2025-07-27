<?php namespace App\Models;

use CodeIgniter\Model;

class ContactModel extends Model
{
    protected $table = 'contacts';
    protected $primaryKey = 'contact_id';
    protected $allowedFields = [
        'name', 'email', 'phone', 'subject', 
        'message', 'status'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    public function getPaginated(array $params)
    {
        $builder = $this->builder();

        // Search
        if (!empty($params['search'])) {
            $builder->groupStart()
                ->like('name', $params['search'])
                ->orLike('email', $params['search'])
                ->orLike('subject', $params['search'])
                ->groupEnd();
        }

        // Filter by status
        if (!empty($params['status'])) {
            $builder->where('status', $params['status']);
        }

        // Sort
        if (!empty($params['sort'])) {
            $builder->orderBy($params['sort'], $params['order']);
        } else {
            $builder->orderBy('created_at', 'desc');
        }

        // Pagination
        $total = $builder->countAllResults(false);
        $page = $params['page'];
        $perPage = $params['per_page'];
        $offset = ($page - 1) * $perPage;

        $data = $builder->get($perPage, $offset)->getResultArray();

        return [
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($total / $perPage)
            ]
        ];
    }
}