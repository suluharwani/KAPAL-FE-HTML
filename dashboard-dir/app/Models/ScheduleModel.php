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
    
    public function getSchedulesWithDetails()
    {
        return $this->select('schedules.*, 
                            boats.boat_name, boats.capacity,
                            routes.estimated_duration,
                            departure.island_name as departure_island,
                            arrival.island_name as arrival_island')
                   ->join('boats', 'boats.boat_id = schedules.boat_id')
                   ->join('routes', 'routes.route_id = schedules.route_id')
                   ->join('islands as departure', 'departure.island_id = routes.departure_island_id')
                   ->join('islands as arrival', 'arrival.island_id = routes.arrival_island_id')
                   ->orderBy('schedules.departure_date', 'ASC')
                   ->orderBy('schedules.departure_time', 'ASC')
                   ->findAll();
    }
    
    public function getScheduleDetails($id)
    {
        return $this->select('schedules.*, 
                            boats.boat_name, boats.capacity, boats.price_per_trip,
                            routes.*,
                            departure.island_name as departure_island,
                            arrival.island_name as arrival_island')
                   ->join('boats', 'boats.boat_id = schedules.boat_id')
                   ->join('routes', 'routes.route_id = schedules.route_id')
                   ->join('islands as departure', 'departure.island_id = routes.departure_island_id')
                   ->join('islands as arrival', 'arrival.island_id = routes.arrival_island_id')
                   ->where('schedules.schedule_id', $id)
                   ->first();
    }
}