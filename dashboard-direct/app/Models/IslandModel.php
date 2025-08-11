<?php namespace App\Models;

use CodeIgniter\Model;

class IslandModel extends Model
{
    protected $table = 'islands';
    protected $primaryKey = 'island_id';
    protected $allowedFields = [
        'island_name', 'description', 'image_url'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    public function getIslandsWithRoutes()
    {
        $islands = $this->findAll();
        
        $routeModel = new RouteModel();
        foreach ($islands as &$island) {
            $island['departure_routes'] = $routeModel->getRoutesByDepartureIsland($island['island_id']);
            $island['arrival_routes'] = $routeModel->getRoutesByArrivalIsland($island['island_id']);
        }

        return $islands;
    }
}