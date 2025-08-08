<?php namespace App\Models;

use CodeIgniter\Model;

class IslandModel extends Model
{
    protected $table = 'islands';
    protected $primaryKey = 'island_id';
    protected $allowedFields = ['island_name', 'description', 'image_url'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    
    public function getIslandsWithStats()
    {
        return $this->select('islands.*, COUNT(DISTINCT routes.route_id) as route_count')
                   ->join('routes', 'routes.departure_island_id = islands.island_id OR routes.arrival_island_id = islands.island_id', 'left')
                   ->groupBy('islands.island_id')
                   ->orderBy('island_name', 'ASC')
                   ->findAll();
    }
}