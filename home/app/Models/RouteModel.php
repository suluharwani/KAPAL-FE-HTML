<?php namespace App\Models;

use CodeIgniter\Model;

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