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


public function getOpenTripDetails($openTripId)
{
    return $this->select('open_trip_schedules.*, 
                        schedules.departure_date, schedules.departure_time,
                        boats.boat_name, boats.boat_type, boats.capacity, boats.price_per_trip,
                        routes.departure_island_id, routes.arrival_island_id,
                        departure.island_name as departure_island,
                        arrival.island_name as arrival_island')
               ->join('schedules', 'schedules.schedule_id = open_trip_schedules.schedule_id')
               ->join('boats', 'boats.boat_id = schedules.boat_id') // Join through schedules
               ->join('routes', 'routes.route_id = schedules.route_id')
               ->join('islands departure', 'departure.island_id = routes.departure_island_id')
               ->join('islands arrival', 'arrival.island_id = routes.arrival_island_id')
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