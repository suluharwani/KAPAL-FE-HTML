<?php namespace App\Models;

use CodeIgniter\Model;

class BoatModel extends Model
{
    protected $table = 'boats';
    protected $primaryKey = 'boat_id';
    protected $allowedFields = ['boat_name', 'boat_type', 'capacity', 'description', 'price_per_trip', 'image_url', 'facilities', 'is_featured'];
    protected $returnType = 'array';
}

class IslandModel extends Model
{
    protected $table = 'islands';
    protected $primaryKey = 'island_id';
    protected $allowedFields = ['island_name', 'description', 'image_url'];
    protected $returnType = 'array';
}

class RouteModel extends Model
{
    protected $table = 'routes';
    protected $primaryKey = 'route_id';
    protected $allowedFields = ['departure_island_id', 'arrival_island_id', 'estimated_duration', 'distance', 'notes'];
    protected $returnType = 'array';
    
    public function getRoutesWithIslands()
    {
        return $this->select('routes.*, 
                            departure.island_name as departure_island_name, 
                            arrival.island_name as arrival_island_name')
                   ->join('islands departure', 'departure.island_id = routes.departure_island_id')
                   ->join('islands arrival', 'arrival.island_id = routes.arrival_island_id')
                   ->findAll();
    }
}

class ScheduleModel extends Model
{
    protected $table = 'schedules';
    protected $primaryKey = 'schedule_id';
    protected $allowedFields = ['route_id', 'boat_id', 'departure_time', 'departure_date', 'available_seats', 'status', 'is_open_trip'];
    protected $returnType = 'array';
    
    public function getSchedulesWithDetails()
    {
        return $this->select('schedules.*, 
                            routes.estimated_duration, 
                            departure.island_name as departure_island, 
                            arrival.island_name as arrival_island,
                            boats.boat_name, boats.capacity, boats.price_per_trip')
                   ->join('routes', 'routes.route_id = schedules.route_id')
                   ->join('islands departure', 'departure.island_id = routes.departure_island_id')
                   ->join('islands arrival', 'arrival.island_id = routes.arrival_island_id')
                   ->join('boats', 'boats.boat_id = schedules.boat_id')
                   ->findAll();
    }
    
    public function getAvailableSchedules($fromIsland, $toIsland, $date, $passengers)
    {
        return $this->select('schedules.*, 
                            routes.estimated_duration, 
                            departure.island_name as departure_island, 
                            arrival.island_name as arrival_island,
                            boats.boat_name, boats.capacity, boats.price_per_trip')
                   ->join('routes', 'routes.route_id = schedules.route_id')
                   ->join('islands departure', 'departure.island_id = routes.departure_island_id')
                   ->join('islands arrival', 'arrival.island_id = routes.arrival_island_id')
                   ->join('boats', 'boats.boat_id = schedules.boat_id')
                   ->where('routes.departure_island_id', $fromIsland)
                   ->where('routes.arrival_island_id', $toIsland)
                   ->where('schedules.departure_date', $date)
                   ->where('schedules.available_seats >=', $passengers)
                   ->where('schedules.status', 'available')
                   ->findAll();
    }
}

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $allowedFields = ['username', 'password', 'email', 'full_name', 'phone', 'address', 'role'];
    protected $returnType = 'array';
    
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];
    
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }
}