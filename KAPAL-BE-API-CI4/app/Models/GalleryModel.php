<?php namespace App\Models;

use CodeIgniter\Model;

class GalleryModel extends Model
{
    protected $table = 'gallery';
    protected $primaryKey = 'gallery_id';
    protected $allowedFields = [
        'title', 'image_url', 'thumbnail_url', 
        'category', 'description', 'is_featured'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    public function getPaginated(array $params)
    {
        $builder = $this->builder();

        // Search
        if (!empty($params['search'])) {
            $builder->groupStart()
                ->like('title', $params['search'])
                ->orLike('description', $params['search'])
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

    public function getFeatured()
    {
        return $this->where('is_featured', 1)
                   ->orderBy('created_at', 'desc')
                   ->findAll();
    }

    public function getCategories()
    {
        return [
            'kapal' => 'Kapal',
            'wisata' => 'Wisata',
            'penumpang' => 'Penumpang',
            'pulau' => 'Pulau'
        ];
    }
}