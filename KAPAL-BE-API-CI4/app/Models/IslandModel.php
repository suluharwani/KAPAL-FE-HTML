<?php namespace App\Models;

use CodeIgniter\Model;

class IslandModel extends Model
{
    protected $table = 'islands';
    protected $primaryKey = 'island_id';
    protected $allowedFields = [
        'island_name', 'description', 'image_url'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    public function getPaginated(array $params)
    {
        $builder = $this->builder();

        // Search
        if (!empty($params['search'])) {
            $builder->groupStart()
                ->like('island_name', $params['search'])
                ->orLike('description', $params['search'])
                ->groupEnd();
        }

        // Sort
        if (!empty($params['sort'])) {
            $builder->orderBy($params['sort'], $params['order']);
        } else {
            $builder->orderBy('island_name', 'asc');
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

    public function getIslandsWithRoutes()
    {
        $islands = $this->findAll();
        $routeModel = new RouteModel();

        foreach ($islands as &$island) {
            $island['departure_routes'] = $routeModel->where('departure_island_id', $island['island_id'])->countAllResults();
            $island['arrival_routes'] = $routeModel->where('arrival_island_id', $island['island_id'])->countAllResults();
        }

        return $islands;
    }
}