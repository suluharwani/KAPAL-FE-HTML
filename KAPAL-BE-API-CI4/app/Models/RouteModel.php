<?php namespace App\Models;

use CodeIgniter\Model;

class RouteModel extends Model
{
    protected $table = 'routes';
    protected $primaryKey = 'route_id';
    protected $allowedFields = [
        'departure_island_id', 'arrival_island_id', 
        'estimated_duration', 'distance', 'notes'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    public function getPaginated(array $params)
    {
        $builder = $this->builder();
        $builder->select('routes.*, 
            departure.island_name as departure_island, 
            arrival.island_name as arrival_island')
            ->join('islands as departure', 'departure.island_id = routes.departure_island_id')
            ->join('islands as arrival', 'arrival.island_id = routes.arrival_island_id');

        // Search
        if (!empty($params['search'])) {
            $builder->groupStart()
                ->like('departure.island_name', $params['search'])
                ->orLike('arrival.island_name', $params['search'])
                ->orLike('estimated_duration', $params['search'])
                ->groupEnd();
        }

        // Filter by island
        if (!empty($params['departure_island'])) {
            $builder->where('departure_island_id', $params['departure_island']);
        }
        if (!empty($params['arrival_island'])) {
            $builder->where('arrival_island_id', $params['arrival_island']);
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

    public function getRouteDetails($routeId)
    {
        return $this->builder()
            ->select('routes.*, 
                departure.island_name as departure_island, 
                arrival.island_name as arrival_island')
            ->join('islands as departure', 'departure.island_id = routes.departure_island_id')
            ->join('islands as arrival', 'arrival.island_id = routes.arrival_island_id')
            ->where('route_id', $routeId)
            ->get()
            ->getRowArray();
    }

    public function getPopularRoutes($limit = 5)
    {
        $scheduleModel = new ScheduleModel();
        
        return $this->builder()
            ->select('routes.*, 
                departure.island_name as departure_island, 
                arrival.island_name as arrival_island,
                COUNT(schedules.schedule_id) as schedule_count')
            ->join('schedules', 'schedules.route_id = routes.route_id')
            ->join('islands as departure', 'departure.island_id = routes.departure_island_id')
            ->join('islands as arrival', 'arrival.island_id = routes.arrival_island_id')
            ->groupBy('routes.route_id')
            ->orderBy('schedule_count', 'desc')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
}