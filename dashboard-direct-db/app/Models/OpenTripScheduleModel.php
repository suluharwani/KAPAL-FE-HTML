<?php namespace App\Models;

use CodeIgniter\Model;

class OpenTripScheduleModel extends Model
{
    protected $table = 'open_trip_schedules';
    protected $primaryKey = 'open_trip_id';
    protected $allowedFields = ['request_id', 'schedule_id', 'reserved_seats', 'available_seats', 'status'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getUpcomingOpenTrips($limit = null)
{
    $builder = $this->select('open_trip_schedules.*, 
                            boats.boat_name, boats.image_url as boat_image,
                            schedules.departure_date, schedules.departure_time,
                            di.island_name as departure_island, 
                            ai.island_name as arrival_island,
                            boats.price_per_trip')
        ->join('schedules', 'schedules.schedule_id = open_trip_schedules.schedule_id')
        ->join('boats', 'boats.boat_id = schedules.boat_id')
        ->join('routes r', 'r.route_id = schedules.route_id')
        ->join('islands di', 'di.island_id = r.departure_island_id')
        ->join('islands ai', 'ai.island_id = r.arrival_island_id')
        ->where('open_trip_schedules.status', 'upcoming')
        ->where('schedules.departure_date >=', date('Y-m-d'))
        ->orderBy('schedules.departure_date', 'ASC');

    if ($limit) {
        $builder->limit($limit);
    }

    return $builder->findAll();
}
    // Method getUpcomingOpenTrips() ada di sini
}