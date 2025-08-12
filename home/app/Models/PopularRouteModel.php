<?php namespace App\Models;

use CodeIgniter\Model;

class PopularRouteModel extends Model
{
    protected $table = 'popular_routes';
    protected $primaryKey = 'route_id';
    protected $allowedFields = ['departure_island_id', 'arrival_island_id', 'schedule', 'duration', 'price', 'is_active'];
    protected $returnType = 'array';
    
    public function getPopularRoutes()
    {
        return $this->select('popular_routes.*, 
                            departure.island_name as departure_island, 
                            arrival.island_name as arrival_island')
                   ->join('islands departure', 'departure.island_id = popular_routes.departure_island_id')
                   ->join('islands arrival', 'arrival.island_id = popular_routes.arrival_island_id')
                   ->where('popular_routes.is_active', 1)
                   ->findAll();
    }
}