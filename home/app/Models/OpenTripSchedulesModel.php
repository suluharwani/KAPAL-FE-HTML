<?php namespace App\Models;

use CodeIgniter\Model;

class OpenTripSchedulesModel extends Model
{
    protected $table = 'open_trip_schedules';
    protected $primaryKey = 'open_trip_id';
    protected $allowedFields = [
    'request_id',
    'schedule_id',
    'boat_id',
    'reserved_seats',
    'available_seats',
    'agreed_price',
    'commission_rate',
    'price_per_person',
    'show_contact_admin',
    'status'
];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get upcoming open trips with details
     */
  public function getUpcomingOpenTrips()
    {
        return $this->select('open_trip_schedules.*, 
                            schedules.departure_date,
                            schedules.departure_time,
                            boats.boat_name,
                            boats.capacity,
                            boats.price_per_trip,
                            departure.island_name as departure_island,
                            arrival.island_name as arrival_island,
                            request_open_trips.user_id as trip_owner_id,
                            users.full_name as trip_owner_name')
                   ->join('schedules', 'schedules.schedule_id = open_trip_schedules.schedule_id')
                   ->join('boats', 'boats.boat_id = schedules.boat_id')
                   ->join('routes', 'routes.route_id = schedules.route_id')
                   ->join('islands departure', 'departure.island_id = routes.departure_island_id')
                   ->join('islands arrival', 'arrival.island_id = routes.arrival_island_id')
                   ->join('request_open_trips', 'request_open_trips.request_id = open_trip_schedules.request_id', 'left')
                   ->join('users', 'users.user_id = request_open_trips.user_id', 'left')
                   ->where('schedules.departure_date >=', date('Y-m-d'))
                   ->where('open_trip_schedules.status', 'upcoming')
                   ->orderBy('schedules.departure_date', 'ASC')
                   ->orderBy('schedules.departure_time', 'ASC')
                   ->findAll();
    }


 public function getOpenTripDetails($openTripId)
    {
        return $this->select('open_trip_schedules.*, 
                            schedules.*,
                            boats.boat_name,
                            boats.capacity,
                            boats.price_per_trip,
                            departure.island_name as departure_island,
                            arrival.island_name as arrival_island,
                            request_open_trips.user_id as trip_owner_id,
                            users.full_name as trip_owner_name,
                            users.phone as trip_owner_phone')
                   ->join('schedules', 'schedules.schedule_id = open_trip_schedules.schedule_id')
                   ->join('boats', 'boats.boat_id = schedules.boat_id')
                   ->join('routes', 'routes.route_id = schedules.route_id')
                   ->join('islands departure', 'departure.island_id = routes.departure_island_id')
                   ->join('islands arrival', 'arrival.island_id = routes.arrival_island_id')
                   ->join('request_open_trips', 'request_open_trips.request_id = open_trip_schedules.request_id', 'left')
                   ->join('users', 'users.user_id = request_open_trips.user_id', 'left')
                   ->where('open_trip_schedules.open_trip_id', $openTripId)
                   ->first();
    }

    /**
     * Update available seats
     */
    public function updateAvailableSeats($openTripId, $change)
    {
        $this->set('available_seats', "available_seats + $change", false)
             ->where('open_trip_id', $openTripId)
             ->update();
    }
    // OpenTripSchedulesModel.php
public function getBoatCapacity($openTripId)
{
    return $this->select('b.capacity')
               ->join('schedules s', 's.schedule_id = open_trip_schedules.schedule_id')
               ->join('boats b', 'b.boat_id = s.boat_id')
               ->where('open_trip_schedules.open_trip_id', $openTripId)
               ->first();
}
}