<?php namespace App\Models;

use CodeIgniter\Model;

class RequestOpenTripsModel extends Model
{
    protected $table = 'request_open_trips';
    protected $primaryKey = 'request_id';
    protected $allowedFields = [
        'user_id',
        'boat_id',
        'route_id',
        'proposed_date',
        'proposed_time',
        'min_passengers',
        'max_passengers',
        'notes',
        'status'
    ];
    protected $returnType = 'array';
    public function getUserRequests($userId)
{
    return $this->select('request_open_trips.*, 
                        boats.boat_name, boats.boat_type,
                        departure.island_name as departure_island_name, 
                        arrival.island_name as arrival_island_name')
               ->join('boats', 'boats.boat_id = request_open_trips.boat_id')
               ->join('routes', 'routes.route_id = request_open_trips.route_id')
               ->join('islands departure', 'departure.island_id = routes.departure_island_id')
               ->join('islands arrival', 'arrival.island_id = routes.arrival_island_id')
               ->where('request_open_trips.user_id', $userId)
               ->orderBy('request_open_trips.proposed_date', 'DESC')
               ->findAll();
}
}