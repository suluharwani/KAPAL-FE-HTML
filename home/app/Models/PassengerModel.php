<?php namespace App\Models;

use CodeIgniter\Model;

class PassengerModel extends Model
{
    protected $table = 'passengers';
    protected $primaryKey = 'passenger_id';
    protected $allowedFields = [
        'booking_id',
        'user_id',
        'full_name',
        'identity_number',
        'phone',
        'age',
        'status',
        'confirmed_at'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get passengers for a booking
     */
    public function getPassengersByBooking($bookingId)
    {
        return $this->where('booking_id', $bookingId)
                   ->findAll();
    }
 public function getConfirmedPassengersCount($scheduleId)
    {
        return $this->select('COUNT(*) as count')
            ->join('bookings', 'bookings.booking_id = passengers.booking_id')
            ->where('bookings.schedule_id', $scheduleId)
            ->where('passengers.status', 'confirmed')
            ->countAllResults();
    }

    // Method untuk mendapatkan jumlah penumpang pending per schedule
    public function getPendingPassengersCount($scheduleId)
    {
        return $this->select('COUNT(*) as count')
            ->join('bookings', 'bookings.booking_id = passengers.booking_id')
            ->where('bookings.schedule_id', $scheduleId)
            ->where('passengers.status', 'pending')
            ->countAllResults();
    }
    /**
     * Get passengers by status
     */
    public function getPassengersByStatus($bookingId, $status)
    {
        return $this->where('booking_id', $bookingId)
                   ->where('status', $status)
                   ->findAll();
    }

    /**
     * Confirm a passenger and reduce available seats
     */
    public function confirmPassenger($passengerId, $scheduleId)
    {
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Update passenger status
            $this->update($passengerId, [
                'status' => 'confirmed',
                'confirmed_at' => date('Y-m-d H:i:s')
            ]);
            
            // Reduce available seats in schedule
            $scheduleModel = new ScheduleModel();
            $schedule = $scheduleModel->find($scheduleId);
            
            if ($schedule && $schedule['available_seats'] > 0) {
                $scheduleModel->update($scheduleId, [
                    'available_seats' => $schedule['available_seats'] - 1
                ]);
                
                // Jika kursi habis, update status jadwal
                if (($schedule['available_seats'] - 1) <= 0) {
                    $scheduleModel->update($scheduleId, [
                        'status' => 'full'
                    ]);
                }
            }
            
            $db->transComplete();
            
            return $db->transStatus();
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error confirming passenger: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cancel passenger confirmation and restore available seats
     */
    public function cancelPassengerConfirmation($passengerId, $scheduleId)
    {
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Update passenger status
            $this->update($passengerId, [
                'status' => 'canceled',
                'confirmed_at' => null
            ]);
            
            // Increase available seats in schedule
            $scheduleModel = new ScheduleModel();
            $schedule = $scheduleModel->find($scheduleId);
            
            if ($schedule) {
                $scheduleModel->update($scheduleId, [
                    'available_seats' => $schedule['available_seats'] + 1,
                    'status' => 'available' // Kembalikan status ke available
                ]);
            }
            
            $db->transComplete();
            
            return $db->transStatus();
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error canceling passenger confirmation: ' . $e->getMessage());
            return false;
        }
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
                        'status' => 'pending',
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
    
    /**
     * Count confirmed passengers for a schedule
     */
    public function countConfirmedPassengers($scheduleId)
    {
        return $this->select('COUNT(*) as confirmed_count')
                   ->join('bookings', 'bookings.booking_id = passengers.booking_id')
                   ->where('bookings.schedule_id', $scheduleId)
                   ->where('passengers.status', 'confirmed')
                   ->countAllResults();
    }
}