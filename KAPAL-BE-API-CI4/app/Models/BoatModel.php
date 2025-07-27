<?php namespace App\Models;

use CodeIgniter\Model;

class BoatModel extends Model
{
    protected $table = 'boats';
    protected $primaryKey = 'boat_id';
    protected $allowedFields = [
        'boat_name', 'boat_type', 'capacity', 'description',
        'price_per_trip', 'image_url', 'facilities'
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
                ->like('boat_name', $params['search'])
                ->orLike('description', $params['search'])
                ->orLike('facilities', $params['search'])
                ->groupEnd();
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