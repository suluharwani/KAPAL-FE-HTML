<?php namespace App\Models;

use CodeIgniter\Model;

class RequestOpenTripModel extends Model
{
    protected $table = 'request_open_trips';
    protected $primaryKey = 'request_id';
    protected $allowedFields = [
        'user_id', 'boat_id', 'route_id', 'proposed_date', 'proposed_time',
        'min_passengers', 'max_passengers', 'notes', 'admin_notes', 'status'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    public function getRequestsWithDetails($status = null)
    {
        $builder = $this->select('request_open_trips.*, 
                                users.full_name as requester_name,
                                boats.boat_name,
                                departure.island_name as departure_island,
                                arrival.island_name as arrival_island')
                       ->join('users', 'users.user_id = request_open_trips.user_id')
                       ->join('boats', 'boats.boat_id = request_open_trips.boat_id')
                       ->join('routes', 'routes.route_id = request_open_trips.route_id')
                       ->join('islands as departure', 'departure.island_id = routes.departure_island_id')
                       ->join('islands as arrival', 'arrival.island_id = routes.arrival_island_id');
        
        if ($status) {
            $builder->where('request_open_trips.status', $status);
        }
        
        return $builder->orderBy('request_open_trips.created_at', 'DESC')
                      ->findAll();
    }
    
    public function getRequestDetails($id)
    {
        return $this->select('request_open_trips.*, 
                            users.full_name as requester_name, users.email as requester_email, users.phone as requester_phone,
                            boats.boat_name, boats.capacity,
                            routes.estimated_duration,
                            departure.island_name as departure_island,
                            arrival.island_name as arrival_island')
                   ->join('users', 'users.user_id = request_open_trips.user_id')
                   ->join('boats', 'boats.boat_id = request_open_trips.boat_id')
                   ->join('routes', 'routes.route_id = request_open_trips.route_id')
                   ->join('islands as departure', 'departure.island_id = routes.departure_island_id')
                   ->join('islands as arrival', 'arrival.island_id = routes.arrival_island_id')
                   ->where('request_open_trips.request_id', $id)
                   ->first();
    }
public function getPendingRequests()
{
    return $this->db->table('request_open_trips r')
        ->select('r.*, b.boat_name, di.island_name as departure_island, ai.island_name as arrival_island, u.full_name as requester_name')
        ->join('boats b', 'b.boat_id = r.boat_id')
        ->join('routes rt', 'rt.route_id = r.route_id')
        ->join('islands di', 'di.island_id = rt.departure_island_id')
        ->join('islands ai', 'ai.island_id = rt.arrival_island_id')
        ->join('users u', 'u.user_id = r.user_id')
        ->where('r.status', 'pending')
        ->get()
        ->getResultArray();
}
}