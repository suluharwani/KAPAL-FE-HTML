<?php namespace App\Models;

use CodeIgniter\Model;

class BookingModel extends Model
{
    protected $table = 'bookings';
    protected $primaryKey = 'booking_id';
    protected $allowedFields = [
        'booking_code', 'user_id', 'schedule_id', 'passenger_count',
        'total_price', 'booking_status', 'payment_method', 'payment_status',
        'is_open_trip', 'open_trip_type', 'open_trip_id', 'notes'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function generateBookingCode()
    {
        $prefix = 'BOOK';
        $date = date('Ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 4));
        return $prefix . $date . $random;
    }

    public function getBookingsWithDetails($status = null)
    {
        $builder = $this->select('bookings.*, 
                                users.full_name as customer_name, users.email as customer_email,
                                schedules.departure_date, schedules.departure_time,
                                boats.boat_name, 
                                di.island_name as departure_island, 
                                ai.island_name as arrival_island')
            ->join('users', 'users.user_id = bookings.user_id')
            ->join('schedules', 'schedules.schedule_id = bookings.schedule_id')
            ->join('boats', 'boats.boat_id = schedules.boat_id')
            ->join('routes r', 'r.route_id = schedules.route_id')
            ->join('islands di', 'di.island_id = r.departure_island_id')
            ->join('islands ai', 'ai.island_id = r.arrival_island')
            ->orderBy('bookings.created_at', 'DESC');

        if ($status) {
            $builder->where('bookings.booking_status', $status);
        }

        return $builder->findAll();
    }

    public function updateBookingStatus($bookingId, $status)
    {
        $data = [
            'booking_id' => $bookingId,
            'booking_status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        return $this->save($data);
    }

    public function getBookingWithPassengers($bookingId)
    {
        $booking = $this->find($bookingId);
        if (!$booking) {
            return null;
        }

        $passengerModel = new PassengerModel();
        $booking['passengers'] = $passengerModel->where('booking_id', $bookingId)->findAll();

        return $booking;
    }
}