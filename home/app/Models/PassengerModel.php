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
public function addPassengers($bookingId, $passengers)
{
    try {
        $data = [];
        foreach ($passengers as $index => $passenger) {
            if (!empty($passenger['name'])) {
                $data[] = [
                    'booking_id' => $bookingId,
                    'full_name' => $passenger['name'],
                    'identity_number' => $passenger['identity'] ?? null,
                    'phone' => $passenger['phone'] ?? null,
                    'age' => $passenger['age'] ?? null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            }
        }
        
        if (empty($data)) {
            throw new \Exception('Tidak ada data penumpang yang valid');
        }
        
        return $this->insertBatch($data);
    } catch (\Exception $e) {
        log_message('error', 'Error adding passengers: ' . $e->getMessage());
        return false;
    }
}
}