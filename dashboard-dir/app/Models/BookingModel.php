<?php namespace App\Models;

use CodeIgniter\Model;

class BookingModel extends Model
{
    protected $table = 'bookings';
    protected $primaryKey = 'booking_id';
    protected $allowedFields = ['user_id', 'schedule_id', 'passenger_count', 'total_price', 'booking_status', 'checkin_count', 'checkin_time'];
    
    public function getBookingsWithDetails()
    {
        return $this->select('bookings.*, users.full_name, boats.boat_name, schedules.departure_date')
                   ->join('users', 'users.user_id = bookings.user_id')
                   ->join('schedules', 'schedules.schedule_id = bookings.schedule_id')
                   ->join('boats', 'boats.boat_id = schedules.boat_id')
                   ->orderBy('bookings.created_at', 'DESC')
                   ->findAll();
    }

    // Tambahkan method untuk mendapatkan detail booking
    public function getBookingDetails($id)
    {
        return $this->select('bookings.*, users.full_name, users.email, users.phone, 
                            boats.boat_name, schedules.departure_date, schedules.departure_time,
                            routes.estimated_duration, departure.island_name as departure_island,
                            arrival.island_name as arrival_island')
                   ->join('users', 'users.user_id = bookings.user_id')
                   ->join('schedules', 'schedules.schedule_id = bookings.schedule_id')
                   ->join('boats', 'boats.boat_id = schedules.boat_id')
                   ->join('routes', 'routes.route_id = schedules.route_id')
                   ->join('islands as departure', 'departure.island_id = routes.departure_island_id')
                   ->join('islands as arrival', 'arrival.island_id = routes.arrival_island_id')
                   ->where('bookings.booking_id', $id)
                   ->first();
    }

    // Method untuk mendapatkan data penumpang
    public function getPassengers($bookingId)
    {
        return $this->db->table('passengers')
                       ->where('booking_id', $bookingId)
                       ->get()
                       ->getResultArray();
    }
}