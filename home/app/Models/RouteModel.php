<?php namespace App\Models;

use CodeIgniter\Model;

class RouteModel extends Model
{
    protected $table = 'routes';
    protected $primaryKey = 'route_id';
    protected $allowedFields = ['departure_island_id', 'arrival_island_id', 'estimated_duration', 'distance', 'notes'];
    protected $returnType = 'array';
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get all routes with island names
     */
    public function getRoutesWithIslands()
    {
        return $this->select('routes.*, 
                            departure.island_name as departure_island_name, 
                            departure.slug as departure_slug,
                            arrival.island_name as arrival_island_name,
                            arrival.slug as arrival_slug')
                   ->join('islands departure', 'departure.island_id = routes.departure_island_id')
                   ->join('islands arrival', 'arrival.island_id = routes.arrival_island_id')
                   ->orderBy('departure.island_name')
                   ->orderBy('arrival.island_name')
                   ->findAll();
    }

    /**
     * Get route by ID with complete island details
     */
    public function getRouteWithIslands($routeId)
    {
        $builder = $this->db->table('routes r');
        $builder->select('
            r.route_id,
            r.estimated_duration,
            r.distance,
            r.notes,
            dep.island_id as departure_island_id,
            dep.island_name as departure_island,
            dep.slug as departure_slug,
            dep.description as departure_description,
            dep.image_url as departure_image,
            arr.island_id as arrival_island_id,
            arr.island_name as arrival_island,
            arr.slug as arrival_slug,
            arr.description as arrival_description,
            arr.image_url as arrival_image
        ');
        
        $builder->join('islands dep', 'r.departure_island_id = dep.island_id');
        $builder->join('islands arr', 'r.arrival_island_id = arr.island_id');
        $builder->where('r.route_id', $routeId);
        
        return $builder->get()->getRowArray();
    }

    /**
     * Get routes between specific islands
     */
    public function getRoutesBetweenIslands($departureIslandId, $arrivalIslandId)
    {
        return $this->where('departure_island_id', $departureIslandId)
                   ->where('arrival_island_id', $arrivalIslandId)
                   ->findAll();
    }

    /**
     * Get routes from a specific departure island
     */
    public function getRoutesFromIsland($departureIslandId)
    {
        return $this->select('routes.*, islands.island_name as arrival_island_name')
                   ->join('islands', 'islands.island_id = routes.arrival_island_id')
                   ->where('departure_island_id', $departureIslandId)
                   ->orderBy('islands.island_name')
                   ->findAll();
    }

    /**
     * Get routes to a specific arrival island
     */
    public function getRoutesToIsland($arrivalIslandId)
    {
        return $this->select('routes.*, islands.island_name as departure_island_name')
                   ->join('islands', 'islands.island_id = routes.departure_island_id')
                   ->where('arrival_island_id', $arrivalIslandId)
                   ->orderBy('islands.island_name')
                   ->findAll();
    }

    /**
     * Check if route exists between two islands
     */
    public function routeExists($departureIslandId, $arrivalIslandId)
    {
        return $this->where('departure_island_id', $departureIslandId)
                   ->where('arrival_island_id', $arrivalIslandId)
                   ->countAllResults() > 0;
    }

    /**
     * Get popular routes (routes with most schedules)
     */
    public function getPopularRoutes($limit = 10)
    {
        $builder = $this->db->table('routes r');
        $builder->select('
            r.route_id,
            r.estimated_duration,
            r.distance,
            dep.island_name as departure_island,
            arr.island_name as arrival_island,
            COUNT(s.schedule_id) as schedule_count
        ');
        
        $builder->join('islands dep', 'r.departure_island_id = dep.island_id');
        $builder->join('islands arr', 'r.arrival_island_id = arr.island_id');
        $builder->join('schedules s', 'r.route_id = s.route_id', 'left');
        
        $builder->where('s.status', 'available');
        $builder->where('s.departure_date >=', date('Y-m-d'));
        
        $builder->groupBy('r.route_id');
        $builder->orderBy('schedule_count', 'DESC');
        $builder->orderBy('dep.island_name');
        $builder->orderBy('arr.island_name');
        $builder->limit($limit);
        
        return $builder->get()->getResultArray();
    }
     public function getRegularRoutesWithCorrectColumns()
    {
        $builder = $this->db->table('routes r');
        $builder->select('r.route_id, 
            dep_island.name as departure_island,
            arr_island.name as arrival_island');
        
        $builder->join('islands dep_island', 'r.departure_island_id = dep_island.island_id');
        $builder->join('islands arr_island', 'r.arrival_island_id = arr_island.island_id');
        
        $builder->where('r.is_active', 1);
        return $builder->get()->getResultArray();
    }
    
}