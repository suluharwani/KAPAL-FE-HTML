<?php namespace App\Models;

use CodeIgniter\Model;

class OpenTripModel extends Model
{
    protected $table = 'open_trip_schedules';
    protected $primaryKey = 'open_trip_id';
    protected $allowedFields = ['request_id', 'schedule_id','boat_id','reserved_seats', 'available_seats', 'status'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    public function getOpenTripsWithDetails($status = null)
    {
        $builder = $this->select('open_trip_schedules.*, 
                                request_open_trips.user_id as requester_id, 
                                users.full_name as requester_name,
                                schedules.departure_date, schedules.departure_time,
                                boats.boat_name, boats.capacity,
                                departure.island_name as departure_island,
                                arrival.island_name as arrival_island')
                       ->join('request_open_trips', 'request_open_trips.request_id = open_trip_schedules.request_id')
                       ->join('users', 'users.user_id = request_open_trips.user_id')
                       ->join('schedules', 'schedules.schedule_id = open_trip_schedules.schedule_id')
                       ->join('boats', 'boats.boat_id = schedules.boat_id')
                       ->join('routes', 'routes.route_id = schedules.route_id')
                       ->join('islands as departure', 'departure.island_id = routes.departure_island_id')
                       ->join('islands as arrival', 'arrival.island_id = routes.arrival_island_id');
        
        if ($status) {
            $builder->where('open_trip_schedules.status', $status);
        }
        
        return $builder->orderBy('schedules.departure_date', 'ASC')
                      ->orderBy('schedules.departure_time', 'ASC')
                      ->findAll();
    }
    
    public function getOpenTripDetails($id)
    {
        return $this->select('open_trip_schedules.*, 
                            request_open_trips.*,
                            users.full_name as requester_name, users.email as requester_email, users.phone as requester_phone,
                            schedules.departure_date, schedules.departure_time,
                            boats.boat_name, boats.capacity, boats.price_per_trip,
                            routes.estimated_duration,
                            departure.island_name as departure_island,
                            arrival.island_name as arrival_island')
                   ->join('request_open_trips', 'request_open_trips.request_id = open_trip_schedules.request_id')
                   ->join('users', 'users.user_id = request_open_trips.user_id')
                   ->join('schedules', 'schedules.schedule_id = open_trip_schedules.schedule_id')
                   ->join('boats', 'boats.boat_id = schedules.boat_id')
                   ->join('routes', 'routes.route_id = schedules.route_id')
                   ->join('islands as departure', 'departure.island_id = routes.departure_island_id')
                   ->join('islands as arrival', 'arrival.island_id = routes.arrival_island_id')
                   ->where('open_trip_schedules.open_trip_id', $id)
                   ->first();
    }
    
    public function getOpenTripBookings($id)
    {
        return $this->db->table('bookings')
                       ->select('bookings.*, users.full_name, users.email')
                       ->join('users', 'users.user_id = bookings.user_id')
                       ->where('open_trip_id', $id)
                       ->orderBy('bookings.created_at', 'DESC')
                       ->get()
                       ->getResultArray();
    }
    // Add this method to your OpenTripModel
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