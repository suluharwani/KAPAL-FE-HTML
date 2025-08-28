<?php namespace App\Models;

use CodeIgniter\Model;

class ScheduleModel extends Model
{
    protected $table = 'schedules';
    protected $primaryKey = 'schedule_id';
    
    protected $allowedFields = [
        'route_id',
        'boat_id',
        'departure_date',
        'departure_time',
        'available_seats',
        'status',
        'is_open_trip'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'route_id' => 'required|numeric',
        'boat_id' => 'required|numeric',
        'departure_date' => 'required|valid_date',
        'departure_time' => 'required',
        'available_seats' => 'required|numeric',
        'status' => 'required|in_list[available,full,canceled]',
        'is_open_trip' => 'required|numeric'
    ];
    
    protected $validationMessages = [
        'route_id' => [
            'required' => 'Route ID is required',
            'numeric' => 'Route ID must be a number'
        ],
        'boat_id' => [
            'required' => 'Boat ID is required',
            'numeric' => 'Boat ID must be a number'
        ],
        'departure_date' => [
            'required' => 'Departure date is required',
            'valid_date' => 'Please provide a valid departure date'
        ],
        'departure_time' => [
            'required' => 'Departure time is required'
        ],
        'available_seats' => [
            'required' => 'Available seats count is required',
            'numeric' => 'Available seats must be a number'
        ]
    ];

    /**
     * Get schedules with complete details including boat, route, and island information
     */
    public function getSchedulesWithDetails($routeId = null, $date = null)
    {
        $builder = $this->db->table('schedules s');
        $builder->select('
            s.schedule_id,
            s.departure_time,
            s.departure_date,
            s.available_seats,
            s.status,
            s.is_open_trip,
            b.boat_id,
            b.boat_name,
            b.boat_type,
            b.capacity,
            b.price_per_trip,
            b.image_url,
            b.facilities,
            b.is_featured,
            r.route_id,
            r.estimated_duration,
            r.distance,
            r.notes as route_notes,
            dep.island_id as departure_island_id,
            dep.island_name as departure_island,
            dep.slug as departure_slug,
            arr.island_id as arrival_island_id,
            arr.island_name as arrival_island,
            arr.slug as arrival_slug
        ');
        
        $builder->join('boats b', 's.boat_id = b.boat_id');
        $builder->join('routes r', 's.route_id = r.route_id');
        $builder->join('islands dep', 'r.departure_island_id = dep.island_id');
        $builder->join('islands arr', 'r.arrival_island_id = arr.island_id');
        
        // Filter berdasarkan status available dan tanggal tidak lewat
        $builder->where('s.status', 'available');
        $builder->where('s.departure_date >=', date('Y-m-d'));
        $builder->where('s.available_seats >', 0);
        
        // Filter berdasarkan rute
        if (!empty($routeId)) {
            $builder->where('s.route_id', $routeId);
        }
        
        // Filter berdasarkan tanggal
        if (!empty($date)) {
            $builder->where('s.departure_date', $date);
        }
        
        // Urutkan berdasarkan tanggal dan waktu
        $builder->orderBy('s.departure_date', 'ASC');
        $builder->orderBy('s.departure_time', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get available routes that have active schedules
     */
    public function getAvailableRoutes()
    {
        $builder = $this->db->table('routes r');
        $builder->select('
            r.route_id,
            r.estimated_duration,
            r.distance,
            dep.island_id as departure_island_id,
            dep.island_name as departure_island,
            dep.slug as departure_slug,
            arr.island_id as arrival_island_id,
            arr.island_name as arrival_island,
            arr.slug as arrival_slug
        ');
        
        $builder->join('islands dep', 'r.departure_island_id = dep.island_id');
        $builder->join('islands arr', 'r.arrival_island_id = arr.island_id');
        $builder->join('schedules s', 'r.route_id = s.route_id');
        
        $builder->where('s.status', 'available');
        $builder->where('s.departure_date >=', date('Y-m-d'));
        $builder->where('s.available_seats >', 0);
        
        $builder->groupBy('r.route_id');
        $builder->orderBy('dep.island_name', 'ASC');
        $builder->orderBy('arr.island_name', 'ASC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get available schedules for a specific route
     */
    public function getAvailableSchedules($routeId, $date = null)
    {
        $builder = $this->where('route_id', $routeId)
                       ->where('status', 'available')
                       ->where('available_seats >', 0)
                       ->where('departure_date >=', date('Y-m-d'));
        
        if ($date) {
            $builder->where('departure_date', $date);
        }
        
        return $builder->orderBy('departure_date')
                      ->orderBy('departure_time')
                      ->findAll();
    }
    
    /**
     * Update schedule status based on available seats
     */
    public function updateScheduleStatus($scheduleId)
    {
        $schedule = $this->find($scheduleId);
        if (!$schedule) {
            return false;
        }
        
        $newStatus = ($schedule['available_seats'] <= 0) ? 'full' : 'available';
        
        if ($schedule['status'] !== $newStatus) {
            return $this->update($scheduleId, ['status' => $newStatus]);
        }
        
        return true;
    }
    
    /**
     * Get schedules for open trips
     */
    public function getOpenTripSchedules($date = null)
    {
        $builder = $this->where('is_open_trip', 1)
                       ->where('status', 'available')
                       ->where('available_seats >', 0)
                       ->where('departure_date >=', date('Y-m-d'));
        
        if ($date) {
            $builder->where('departure_date >=', $date);
        }
        
        return $builder->orderBy('departure_date')
                      ->orderBy('departure_time')
                      ->findAll();
    }
    
    /**
     * Decrement available seats
     */
    public function decrementSeats($scheduleId, $count = 1)
    {
        $result = $this->set('available_seats', "available_seats - $count", false)
             ->where('schedule_id', $scheduleId)
             ->where('available_seats >=', $count)
             ->update();
        
        // Update status if needed
        if ($result) {
            $this->updateScheduleStatus($scheduleId);
        }
        
        return $result;
    }
    
    /**
     * Increment available seats
     */
    public function incrementSeats($scheduleId, $count = 1)
    {
        $result = $this->set('available_seats', "available_seats + $count", false)
             ->where('schedule_id', $scheduleId)
             ->update();
        
        // Update status if needed
        if ($result) {
            $this->updateScheduleStatus($scheduleId);
        }
        
        return $result;
    }

    /**
     * Get schedule by ID with complete details
     */
    public function getScheduleWithDetails($scheduleId)
    {
        $builder = $this->db->table('schedules s');
        $builder->select('
            s.*,
            b.boat_name,
            b.boat_type,
            b.capacity,
            b.price_per_trip,
            b.image_url,
            b.facilities,
            b.is_featured,
            r.route_id,
            r.estimated_duration,
            r.distance,
            dep.island_name as departure_island,
            arr.island_name as arrival_island
        ');
        
        $builder->join('boats b', 's.boat_id = b.boat_id');
        $builder->join('routes r', 's.route_id = r.route_id');
        $builder->join('islands dep', 'r.departure_island_id = dep.island_id');
        $builder->join('islands arr', 'r.arrival_island_id = arr.island_id');
        
        $builder->where('s.schedule_id', $scheduleId);
        
        return $builder->get()->getRowArray();
    }
}