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

    public function getRoutesWithIslands()
    {
        return $this->select('routes.*, 
                            di.island_name as departure_island, 
                            ai.island_name as arrival_island')
            ->join('islands di', 'di.island_id = routes.departure_island_id')
            ->join('islands ai', 'ai.island_id = routes.arrival_island_id')
            ->orderBy('departure_island', 'ASC')
            ->orderBy('arrival_island', 'ASC')
            ->findAll();
    }

    public function getRoutesByDepartureIsland($islandId)
    {
        return $this->where('departure_island_id', $islandId)
            ->join('islands ai', 'ai.island_id = routes.arrival_island_id')
            ->findAll();
    }

    public function getRoutesByArrivalIsland($islandId)
    {
        return $this->where('arrival_island_id', $islandId)
            ->join('islands di', 'di.island_id = routes.departure_island_id')
            ->findAll();
    }
}