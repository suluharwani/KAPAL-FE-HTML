<?php namespace App\Models;

use CodeIgniter\Model;

class BookingModel extends Model
{
    protected $table = 'bookings';
    protected $primaryKey = 'booking_id';
   protected $allowedFields = [
    'booking_code',
    'user_id',
    'schedule_id',
    'passenger_count',
    'custom_price', // Tambahkan ini
    'total_price',
    'booking_status',
    'payment_method',
    'payment_status',
    'is_open_trip',
    'open_trip_id',
    'open_trip_type',
    'notes'
];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get open trip members with user details
     */
    // public function getOpenTripMembers($openTripId)
    // {
    //     return $this->select('bookings.*, 
    //                         users.full_name, 
    //                         users.email, 
    //                         users.phone,
    //                         COUNT(passengers.passenger_id) as passenger_count')
    //                ->join('users', 'users.user_id = bookings.user_id', 'left')
    //                ->join('passengers', 'passengers.booking_id = bookings.booking_id', 'left')
    //                ->where('bookings.open_trip_id', $openTripId)
    //                ->where('bookings.is_open_trip', 1)
    //                ->groupBy('bookings.booking_id')
    //                ->orderBy('bookings.created_at', 'DESC')
    //                ->findAll();
    // }

    /**
     * Get booking details with user information
     */
    // public function getBookingWithUser($bookingId)
    // {
    //     return $this->select('bookings.*, users.full_name, users.email, users.phone')
    //                ->join('users', 'users.user_id = bookings.user_id', 'left')
    //                ->where('bookings.booking_id', $bookingId)
    //                ->first();
    // }

    /**
     * Get bookings for a specific user
     */
    public function getUserBookings($userId)
    {
        return $this->where('user_id', $userId)
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }

    /**
     * Get open trip bookings for a user
     */
    public function getUserOpenTripBookings($userId)
    {
        return $this->where('user_id', $userId)
                   ->where('is_open_trip', 1)
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }

    /**
     * Get booking by code
     */
    public function getByBookingCode($bookingCode)
    {
        return $this->where('booking_code', $bookingCode)
                   ->first();
    }

    /**
     * Update booking status
     */
    public function updateStatus($bookingId, $status)
    {
        return $this->update($bookingId, ['booking_status' => $status]);
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus($bookingId, $status)
    {
        return $this->update($bookingId, ['payment_status' => $status]);
    }

    /**
     * Get bookings with payment details
     */
    public function getWithPayments($bookingId)
    {
        return $this->select('bookings.*, 
                            payments.amount, 
                            payments.payment_date,
                            payments.payment_method,
                            payments.status as payment_status')
                   ->join('payments', 'payments.booking_id = bookings.booking_id', 'left')
                   ->where('bookings.booking_id', $bookingId)
                   ->findAll();
    }

    /**
     * Get all open trips with details
     */
    public function getAllOpenTripsWithDetails()
    {
        return $this->select('bookings.*,
                            open_trip_schedules.status as open_trip_status,
                            schedules.departure_date,
                            schedules.departure_time,
                            boats.boat_name,
                            departure.island_name as departure_island,
                            arrival.island_name as arrival_island')
                   ->join('open_trip_schedules', 'open_trip_schedules.open_trip_id = bookings.open_trip_id')
                   ->join('schedules', 'schedules.schedule_id = open_trip_schedules.schedule_id')
                   ->join('boats', 'boats.boat_id = schedules.boat_id')
                   ->join('routes', 'routes.route_id = schedules.route_id')
                   ->join('islands departure', 'departure.island_id = routes.departure_island_id')
                   ->join('islands arrival', 'arrival.island_id = routes.arrival_island_id')
                   ->where('bookings.is_open_trip', 1)
                   ->orderBy('schedules.departure_date', 'ASC')
                   ->findAll();
    }

    /**
     * Create new booking for open trip
     */
public function createOpenTripBooking($data)
{
    // Generate booking code
    $data['booking_code'] = 'BOOK-' . strtoupper(uniqid());
    $data['is_open_trip'] = 1;
    
    // Hitung total price jika custom_price diset
    if (isset($data['custom_price']) && $data['custom_price'] > 0) {
        $data['total_price'] = $data['custom_price'] * $data['passenger_count'];
    }
    
    return $this->insert($data);
}

    /**
     * Get bookings for a specific open trip
     */
    public function getBookingsForOpenTrip($openTripId)
    {
        return $this->where('open_trip_id', $openTripId)
                   ->where('is_open_trip', 1)
                   ->findAll();
    }

    /**
     * Count available seats for open trip
     */
    public function countAvailableSeats($openTripId)
    {
        $totalCapacity = $this->db->table('open_trip_schedules')
                                ->select('boats.capacity')
                                ->join('schedules', 'schedules.schedule_id = open_trip_schedules.schedule_id')
                                ->join('boats', 'boats.boat_id = schedules.boat_id')
                                ->where('open_trip_schedules.open_trip_id', $openTripId)
                                ->get()
                                ->getRowArray();
        
        if (!$totalCapacity) {
            return 0;
        }
        
        $bookedSeats = $this->where('open_trip_id', $openTripId)
                           ->where('is_open_trip', 1)
                           ->selectSum('passenger_count')
                           ->get()
                           ->getRow();
        
        return $totalCapacity['capacity'] - ($bookedSeats->passenger_count ?? 0);
    }
public function getOpenTripMembers($openTripId)
{
    return $this->select('bookings.*, 
                        users.full_name, 
                        users.email, 
                        users.phone,
                        COUNT(passengers.passenger_id) as passenger_count')
               ->join('users', 'users.user_id = bookings.user_id', 'left')
               ->join('passengers', 'passengers.booking_id = bookings.booking_id', 'left')
               ->where('bookings.open_trip_id', $openTripId)
               ->groupBy('bookings.booking_id')
               ->orderBy('bookings.created_at', 'DESC')
               ->findAll();
}

public function getBookingWithUser($bookingId)
{
    return $this->select('bookings.*, users.full_name, users.email, users.phone')
               ->join('users', 'users.user_id = bookings.user_id', 'left')
               ->where('bookings.booking_id', $bookingId)
               ->first();
}
 public function getBookingWithDetails($bookingCode)
    {
        $builder = $this->db->table('bookings b');
        $builder->select('
            b.*,
            u.full_name as user_name,
            u.email as user_email,
            u.phone as user_phone,
            s.departure_time,
            s.departure_date,
            boat.boat_name,
            boat.boat_type,
            boat.price_per_trip,
            boat.image_url as boat_image,
            r.route_id,
            r.estimated_duration,
            dep.island_name as departure_island,
            arr.island_name as arrival_island,
            (SELECT COUNT(*) FROM passengers p WHERE p.booking_id = b.booking_id) as actual_passengers
        ');
        
        $builder->join('users u', 'b.user_id = u.user_id');
        $builder->join('schedules s', 'b.schedule_id = s.schedule_id');
        $builder->join('boats boat', 's.boat_id = boat.boat_id');
        $builder->join('routes r', 's.route_id = r.route_id');
        $builder->join('islands dep', 'r.departure_island_id = dep.island_id');
        $builder->join('islands arr', 'r.arrival_island_id = arr.island_id');
        
        $builder->where('b.booking_code', $bookingCode);
        
        return $builder->get()->getRowArray();
    }

    /**
     * Get bookings with details for user
     */
    public function getUserBookingsWithDetails($userId)
    {
        $builder = $this->db->table('bookings b');
        $builder->select('
            b.*,
            s.departure_time,
            s.departure_date,
            boat.boat_name,
            dep.island_name as departure_island,
            arr.island_name as arrival_island
        ');
        
        $builder->join('schedules s', 'b.schedule_id = s.schedule_id');
        $builder->join('boats boat', 's.boat_id = boat.boat_id');
        $builder->join('routes r', 's.route_id = r.route_id');
        $builder->join('islands dep', 'r.departure_island_id = dep.island_id');
        $builder->join('islands arr', 'r.arrival_island_id = arr.island_id');
        
        $builder->where('b.user_id', $userId);
        $builder->orderBy('b.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    public function canCancel($bookingId)
    {
        $booking = $this->find($bookingId);
        if (!$booking) return false;

        return in_array($booking['booking_status'], ['pending', 'confirmed']) &&
               in_array($booking['payment_status'], ['pending', 'partial']);
    }
}