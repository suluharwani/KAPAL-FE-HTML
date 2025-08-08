<?php namespace App\Models;

use CodeIgniter\Model;

class BookingModel extends Model
{
    protected $table = 'bookings';
    protected $primaryKey = 'booking_id';
    protected $allowedFields = ['user_id', 'schedule_id', 'passenger_count', 'total_price', 'booking_status'];
    
    public function getBookingsWithDetails()
    {
        return $this->select('bookings.*, users.full_name, boats.boat_name, schedules.departure_date')
                   ->join('users', 'users.user_id = bookings.user_id')
                   ->join('schedules', 'schedules.schedule_id = bookings.schedule_id')
                   ->join('boats', 'boats.boat_id = schedules.boat_id')
                   ->orderBy('bookings.created_at', 'DESC')
                   ->findAll();
    }
}