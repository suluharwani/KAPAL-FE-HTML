<?php namespace App\Models;

use CodeIgniter\Model;

class FaqModel extends Model
{
    protected $table = 'faqs';
    protected $primaryKey = 'faq_id';
    protected $allowedFields = [
        'question', 'answer', 'category', 
        'is_featured', 'display_order'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getPaginated(array $params)
    {
        $builder = $this->builder();

        // Search
        if (!empty($params['search'])) {
            $builder->groupStart()
                ->like('question', $params['search'])
                ->orLike('answer', $params['search'])
                ->groupEnd();
        }

        // Filter by category
        if (!empty($params['category'])) {
            $builder->where('category', $params['category']);
        }

        // Filter featured
        if (isset($params['featured'])) {
            $builder->where('is_featured', $params['featured'] ? 1 : 0);
        }

        // Sort
        if (!empty($params['sort'])) {
            $builder->orderBy($params['sort'], $params['order']);
        } else {
            $builder->orderBy('display_order', 'asc')
                   ->orderBy('created_at', 'desc');
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

    public function getFeatured()
    {
        return $this->where('is_featured', 1)
                   ->orderBy('display_order', 'asc')
                   ->findAll();
    }

    public function getByCategory($category)
    {
        return $this->where('category', $category)
                   ->orderBy('display_order', 'asc')
                   ->findAll();
    }
}