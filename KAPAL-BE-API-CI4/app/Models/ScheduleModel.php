<?php namespace App\Models;

use CodeIgniter\Model;

class ScheduleModel extends Model
{
    protected $table = 'schedules';
    protected $primaryKey = 'schedule_id';
    protected $allowedFields = [
        'route_id', 'boat_id', 'departure_time', 'departure_date',
        'available_seats', 'status'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getPaginated(array $params)
    {
        $builder = $this->builder();
        $builder->select('schedules.*, 
            routes.route_id, departure.island_name as departure_island, 
            arrival.island_name as arrival_island, 
            boats.boat_name, boats.boat_type, boats.capacity, boats.price_per_trip')
            ->join('routes', 'routes.route_id = schedules.route_id')
            ->join('boats', 'boats.boat_id = schedules.boat_id')
            ->join('islands as departure', 'departure.island_id = routes.departure_island_id')
            ->join('islands as arrival', 'arrival.island_id = routes.arrival_island_id');

        // Filter by date
        if (!empty($params['date_from'])) {
            $builder->where('departure_date >=', $params['date_from']);
        }
        if (!empty($params['date_to'])) {
            $builder->where('departure_date <=', $params['date_to']);
        }

        // Filter by route
        if (!empty($params['route_id'])) {
            $builder->where('schedules.route_id', $params['route_id']);
        }

        // Filter by status
        if (!empty($params['status'])) {
            $builder->where('schedules.status', $params['status']);
        }

        // Filter available seats
        if (!empty($params['min_seats'])) {
            $builder->where('available_seats >=', $params['min_seats']);
        }

        // Sort
        if (!empty($params['sort'])) {
            $builder->orderBy($params['sort'], $params['order']);
        } else {
            $builder->orderBy('departure_date', 'asc')
                   ->orderBy('departure_time', 'asc');
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

    public function getBoatForSchedule($scheduleId)
    {
        return $this->builder()
            ->select('boats.*')
            ->join('boats', 'boats.boat_id = schedules.boat_id')
            ->where('schedule_id', $scheduleId)
            ->get()
            ->getRowArray();
    }
}