<?php namespace App\Models;

use CodeIgniter\Model;

class PassengerModel extends Model
{
    protected $table = 'passengers';
    protected $primaryKey = 'passenger_id';
    protected $allowedFields = [
        'booking_id', 'full_name', 'identity_number', 
        'phone', 'age'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    public function getPassengersByBooking($bookingId)
    {
        return $this->where('booking_id', $bookingId)->findAll();
    }
}