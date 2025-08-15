<?php namespace App\Models;

use CodeIgniter\Model;

class PassengerModel extends Model
{
    protected $table = 'passengers';
    protected $primaryKey = 'passenger_id';
    protected $allowedFields = [
        'booking_id',
        'full_name',
        'identity_number',
        'phone',
        'age'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    /**
     * Get passengers for a booking
     */
    public function getPassengersByBooking($bookingId)
    {
        return $this->where('booking_id', $bookingId)
                   ->findAll();
    }

    /**
     * Add multiple passengers
     */
    public function addPassengers($bookingId, array $passengers)
    {
        $data = [];
        foreach ($passengers as $passenger) {
            $data[] = [
                'booking_id' => $bookingId,
                'full_name' => $passenger['full_name'],
                'identity_number' => $passenger['identity_number'] ?? null,
                'phone' => $passenger['phone'] ?? null,
                'age' => $passenger['age'] ?? null
            ];
        }
        
        return $this->insertBatch($data);
    }
}