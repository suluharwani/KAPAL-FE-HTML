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
     * Get schedules with related data (boat and route information)
     */
    public function getSchedulesWithDetails($conditions = [])
    {
        $builder = $this->db->table('schedules s')
            ->select('s.*, b.boat_name, r.departure_island_id, r.arrival_island_id, 
                     di.island_name as departure_island, ai.island_name as arrival_island')
            ->join('boats b', 'b.boat_id = s.boat_id')
            ->join('routes r', 'r.route_id = s.route_id')
            ->join('islands di', 'di.island_id = r.departure_island_id')
            ->join('islands ai', 'ai.island_id = r.arrival_island_id');
        
        if (!empty($conditions)) {
            $builder->where($conditions);
        }
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get available schedules for a specific route
     */
    public function getAvailableSchedules($routeId, $date = null)
    {
        $builder = $this->where('route_id', $routeId)
                       ->where('status', 'available')
                       ->where('available_seats >', 0);
        
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
        $builder = $this->where('is_open_trip', 1);
        
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
        $this->set('available_seats', "available_seats - $count", false)
             ->where('schedule_id', $scheduleId)
             ->update();
        
        // Update status if needed
        $this->updateScheduleStatus($scheduleId);
        
        return $this->db->affectedRows() > 0;
    }
    
    /**
     * Increment available seats
     */
    public function incrementSeats($scheduleId, $count = 1)
    {
        $this->set('available_seats', "available_seats + $count", false)
             ->where('schedule_id', $scheduleId)
             ->update();
        
        // Update status if needed
        $this->updateScheduleStatus($scheduleId);
        
        return $this->db->affectedRows() > 0;
    }
}