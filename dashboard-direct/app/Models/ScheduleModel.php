<?php namespace App\Models;

use CodeIgniter\Model;

class ScheduleModel extends Model
{
    protected $table = 'schedules';
    protected $primaryKey = 'schedule_id';
    protected $allowedFields = [
        'route_id', 'boat_id', 'departure_time', 'departure_date', 
        'available_seats', 'status', 'is_open_trip'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getSchedulesWithDetails($perPage = 10)
    {
        return $this->select('schedules.*, boats.boat_name, routes.departure_island_id, routes.arrival_island_id, 
                            di.island_name as departure_island, ai.island_name as arrival_island')
            ->join('boats', 'boats.boat_id = schedules.boat_id')
            ->join('routes', 'routes.route_id = schedules.route_id')
            ->join('islands di', 'di.island_id = routes.departure_island_id')
            ->join('islands ai', 'ai.island_id = routes.arrival_island_id')
            ->orderBy('departure_date', 'ASC')
            ->orderBy('departure_time', 'ASC')
            ->paginate($perPage);
    }
}