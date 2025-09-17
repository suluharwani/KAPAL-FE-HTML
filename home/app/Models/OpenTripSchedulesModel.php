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
public function getUpcomingOpenTripsWithPassengerCounts()
{
    $builder = $this->db->table('schedules s');
    
    // Query dasar untuk mendapatkan jadwal open trip
    $builder->select('s.*, b.boat_name, b.image_url, b.capacity,
        r.departure_island_id, r.arrival_island_id');
    
    $builder->join('boats b', 's.boat_id = b.boat_id', 'left');
    $builder->join('routes r', 's.route_id = r.route_id', 'left');
    $builder->where('s.is_open_trip', 1);
    $builder->where('s.departure_date >=', date('Y-m-d'));
    $builder->where('s.available_seats >', 0);
    $builder->orderBy('s.departure_date', 'ASC');
    $builder->orderBy('s.departure_time', 'ASC');
    
    $schedules = $builder->get()->getResultArray();
    
    // Jika tidak ada jadwal, return array kosong
    if (empty($schedules)) {
        return [];
    }
    
    // Ambil data pulau dan hitung passengers untuk setiap schedule
    foreach ($schedules as &$schedule) {
        // Get nama pulau
        $schedule['departure_island'] = $this->getIslandName($schedule['departure_island_id'] ?? null);
        $schedule['arrival_island'] = $this->getIslandName($schedule['arrival_island_id'] ?? null);
        
        // Hitung jumlah passengers dengan status yang berbeda
        $passengerCounts = $this->getPassengerCountsBySchedule($schedule['schedule_id']);
        
        $schedule['confirmed_seats'] = $passengerCounts['confirmed'];
        $schedule['pending_seats'] = $passengerCounts['pending'];
        $schedule['cancelled_seats'] = $passengerCounts['cancelled'];
        
        // Hitung available seats yang akurat
        $totalCapacity = $schedule['capacity'] ?? 0;
        $bookedSeats = $passengerCounts['confirmed'] + $passengerCounts['pending'];
        $schedule['available_seats'] = max(0, $totalCapacity - $bookedSeats);
        
        // Update available_seats di database jika berbeda
        if ($schedule['available_seats'] != $schedule['available_seats']) {
            $this->updateAvailableSeats($schedule['schedule_id'], $schedule['available_seats']);
        }
    }
    
    return $schedules;
}

protected function getIslandName($islandId)
{
    if (!$islandId) {
        return 'Unknown';
    }
    
    // Cari kolom name yang benar di tabel islands
    $islandsColumns = $this->db->getFieldNames('islands');
    $nameField = 'name';
    
    if (in_array('island_name', $islandsColumns)) {
        $nameField = 'island_name';
    } elseif (in_array('nama', $islandsColumns)) {
        $nameField = 'nama';
    } elseif (in_array('nama_pulau', $islandsColumns)) {
        $nameField = 'nama_pulau';
    } elseif (in_array('title', $islandsColumns)) {
        $nameField = 'title';
    }
    
    $island = $this->db->table('islands')
        ->select($nameField . ' as island_name')
        ->where('island_id', $islandId)
        ->get()
        ->getRowArray();
    
    return $island['island_name'] ?? 'Unknown';
}

protected function getPassengerCountsBySchedule($scheduleId)
{
    $result = [
        'confirmed' => 0,
        'pending' => 0,
        'cancelled' => 0,
        'total' => 0
    ];
    
    try {
        // Query untuk menghitung passengers berdasarkan status
        $query = $this->db->query("
            SELECT 
                p.status,
                COUNT(p.passenger_id) as count
            FROM passengers p
            JOIN bookings b ON p.booking_id = b.booking_id
            WHERE b.schedule_id = ?
            AND b.booking_status != 'cancelled'
            GROUP BY p.status
        ", [$scheduleId]);
        
        $counts = $query->getResultArray();
        
        foreach ($counts as $count) {
            $status = strtolower($count['status']);
            $result[$status] = (int) $count['count'];
            $result['total'] += (int) $count['count'];
        }
        
    } catch (\Exception $e) {
        // Fallback: hitung sederhana jika query complex error
        error_log('Error counting passengers: ' . $e->getMessage());
        
        $totalPassengers = $this->db->table('passengers p')
            ->join('bookings b', 'p.booking_id = b.booking_id')
            ->where('b.schedule_id', $scheduleId)
            ->where('b.booking_status !=', 'cancelled')
            ->countAllResults();
        
        // Asumsi sebagian pending, sebagian confirmed (untuk fallback)
        $result['pending'] = ceil($totalPassengers * 0.7);
        $result['confirmed'] = floor($totalPassengers * 0.3);
        $result['total'] = $totalPassengers;
    }
    
    return $result;
}

protected function updateAvailableSeats($scheduleId, $availableSeats)
{
    try {
        $this->db->table('schedules')
            ->where('schedule_id', $scheduleId)
            ->update(['available_seats' => $availableSeats]);
    } catch (\Exception $e) {
        error_log('Error updating available seats: ' . $e->getMessage());
    }
}

// Method untuk mendapatkan data yang lebih detail termasuk info user booking
public function getUpcomingOpenTripsWithUserStatus($userId = null)
{
    $schedules = $this->getUpcomingOpenTripsWithPassengerCounts();
    
    if ($userId) {
        // Tambahkan info status booking user untuk setiap schedule
        foreach ($schedules as &$schedule) {
            $userBookingStatus = $this->getUserBookingStatus($schedule['schedule_id'], $userId);
            $schedule['user_booking_status'] = $userBookingStatus['status'];
            $schedule['user_booking_id'] = $userBookingStatus['booking_id'];
            $schedule['user_passenger_id'] = $userBookingStatus['passenger_id'];
        }
    }
    
    return $schedules;
}

protected function getUserBookingStatus($scheduleId, $userId)
{
    $result = [
        'status' => null,
        'booking_id' => null,
        'passenger_id' => null
    ];
    
    try {
        $booking = $this->db->table('bookings b')
            ->select('b.booking_id, b.booking_status, p.passenger_id, p.status as passenger_status')
            ->join('passengers p', 'p.booking_id = b.booking_id', 'left')
            ->where('b.schedule_id', $scheduleId)
            ->where('b.user_id', $userId)
            ->where('b.booking_status !=', 'cancelled')
            ->get()
            ->getRowArray();
        
        if ($booking) {
            // Prioritaskan passenger status jika ada, otherwise use booking status
            $result['status'] = !empty($booking['passenger_status']) ? $booking['passenger_status'] : $booking['booking_status'];
            $result['booking_id'] = $booking['booking_id'];
            $result['passenger_id'] = $booking['passenger_id'];
        }
        
    } catch (\Exception $e) {
        error_log('Error getting user booking status: ' . $e->getMessage());
    }
    
    return $result;
}

protected function getRouteWithIslands($routeId)
{
    $route = $this->db->table('routes r')
        ->select('r.*')
        ->where('r.route_id', $routeId)
        ->get()
        ->getRowArray();
    
    if (!$route) {
        return ['departure_island' => 'Unknown', 'arrival_island' => 'Unknown'];
    }
    
    // Cari kolom name yang benar di tabel islands
    $islandsColumns = $this->db->getFieldNames('islands');
    $nameField = 'name';
    
    if (in_array('island_name', $islandsColumns)) {
        $nameField = 'island_name';
    } elseif (in_array('nama_pulau', $islandsColumns)) {
        $nameField = 'nama_pulau';
    } elseif (in_array('title', $islandsColumns)) {
        $nameField = 'title';
    }
    
    // Get departure island
    $depIsland = $this->db->table('islands')
        ->select($nameField . ' as island_name')
        ->where('island_id', $route['departure_island_id'])
        ->get()
        ->getRowArray();
    
    // Get arrival island
    $arrIsland = $this->db->table('islands')
        ->select($nameField . ' as island_name')
        ->where('island_id', $route['arrival_island_id'])
        ->get()
        ->getRowArray();
    
    return [
        'departure_island' => $depIsland['island_name'] ?? 'Unknown',
        'arrival_island' => $arrIsland['island_name'] ?? 'Unknown'
    ];
}
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