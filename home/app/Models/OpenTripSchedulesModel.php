<?php namespace App\Models;

use CodeIgniter\Model;

class RequestOpenTripsModel extends Model
{
    protected $table = 'request_open_trips';
    protected $primaryKey = 'request_id';
    protected $allowedFields = ['user_id', 'boat_id', 'route_id', 'proposed_date', 'proposed_time', 
                              'min_passengers', 'max_passengers', 'notes', 'status'];
    protected $returnType = 'array';
}

class OpenTripSchedulesModel extends Model
{
    protected $table = 'open_trip_schedules';
    protected $primaryKey = 'open_trip_id';
    protected $allowedFields = ['request_id', 'schedule_id', 'reserved_seats', 'available_seats', 'status'];
    protected $returnType = 'array';
    
    public function getUpcomingOpenTrips()
    {
        return $this->select('ots.*, 
                            r.user_id, u.full_name as requester_name,
                            s.departure_date, s.departure_time,
                            b.boat_name, b.capacity, b.price_per_trip,
                            rt.departure_island_id, rt.arrival_island_id,
                            di.island_name as departure_island,
                            ai.island_name as arrival_island')
                   ->from('open_trip_schedules ots')
                   ->join('request_open_trips r', 'r.request_id = ots.request_id')
                   ->join('users u', 'u.user_id = r.user_id')
                   ->join('schedules s', 's.schedule_id = ots.schedule_id')
                   ->join('boats b', 'b.boat_id = s.boat_id')
                   ->join('routes rt', 'rt.route_id = s.route_id')
                   ->join('islands di', 'di.island_id = rt.departure_island_id')
                   ->join('islands ai', 'ai.island_id = rt.arrival_island_id')
                   ->where('ots.status', 'upcoming')
                   ->where('s.departure_date >=', date('Y-m-d'))
                   ->orderBy('s.departure_date', 'ASC')
                   ->orderBy('s.departure_time', 'ASC')
                   ->findAll();
    }
}