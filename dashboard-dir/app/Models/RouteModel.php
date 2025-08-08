<?php namespace App\Models;

use CodeIgniter\Model;

class RouteModel extends Model
{
    protected $table = 'routes';
    protected $primaryKey = 'route_id';
    protected $allowedFields = ['departure_island_id', 'arrival_island_id', 'estimated_duration', 'distance', 'notes'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    
    public function getRoutesWithIslands()
    {
        return $this->select('routes.*, 
                            departure.island_name as departure_island, 
                            arrival.island_name as arrival_island')
                   ->join('islands as departure', 'departure.island_id = routes.departure_island_id')
                   ->join('islands as arrival', 'arrival.island_id = routes.arrival_island_id')
                   ->orderBy('departure.island_name', 'ASC')
                   ->orderBy('arrival.island_name', 'ASC')
                   ->findAll();
    }
    
    public function getRouteDetails($id)
    {
        return $this->select('routes.*, 
                            departure.island_name as departure_island, 
                            arrival.island_name as arrival_island')
                   ->join('islands as departure', 'departure.island_id = routes.departure_island_id')
                   ->join('islands as arrival', 'arrival.island_id = routes.arrival_island_id')
                   ->where('routes.route_id', $id)
                   ->first();
    }
}