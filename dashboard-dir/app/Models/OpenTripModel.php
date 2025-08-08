<?php namespace App\Models;

use CodeIgniter\Model;

class OpenTripModel extends Model
{
    protected $table = 'open_trip_schedules';
    protected $primaryKey = 'open_trip_id';
    protected $allowedFields = ['request_id', 'schedule_id', 'reserved_seats', 'available_seats', 'status'];
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
}