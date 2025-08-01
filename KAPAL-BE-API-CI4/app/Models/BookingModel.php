<?php namespace App\Models;

use CodeIgniter\Model;

class BookingModel extends Model
{
    protected $table = 'bookings';
    protected $primaryKey = 'booking_id';
    protected $allowedFields = [
        'booking_code', 'user_id', 'schedule_id', 'passenger_count',
        'total_price', 'booking_status', 'payment_method', 'payment_status',
        'notes','is_open_trip','open_trip_type' 
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getPaginated(array $params, $userId = null)
    {
        $builder = $this->builder();
        $builder->select('bookings.*, 
            routes.route_id, departure.island_name as departure_island, 
            arrival.island_name as arrival_island, 
            boats.boat_name, schedules.departure_date, schedules.departure_time')
            ->join('schedules', 'schedules.schedule_id = bookings.schedule_id')
            ->join('routes', 'routes.route_id = schedules.route_id')
            ->join('boats', 'boats.boat_id = schedules.boat_id')
            ->join('islands as departure', 'departure.island_id = routes.departure_island_id')
            ->join('islands as arrival', 'arrival.island_id = routes.arrival_island_id');

        // Filter by user if not admin
        if ($userId && $this->request->user->role !== 'admin') {
            $builder->where('bookings.user_id', $userId);
        }

        // Search
        if (!empty($params['search'])) {
            $builder->groupStart()
                ->like('booking_code', $params['search'])
                ->orLike('boats.boat_name', $params['search'])
                ->orLike('departure.island_name', $params['search'])
                ->orLike('arrival.island_name', $params['search'])
                ->groupEnd();
        }

        // Filter by status
        if (!empty($params['status'])) {
            $builder->where('booking_status', $params['status']);
        }

        // Sort
        if (!empty($params['sort'])) {
            $builder->orderBy($params['sort'], $params['order']);
        } else {
            $builder->orderBy('bookings.created_at', 'desc');
        }

        // Pagination
        $total = $builder->countAllResults(false);
        $page = $params['page'];
        $perPage = $params['per_page'];
        $offset = ($page - 1) * $perPage;

        $data = $builder->get($perPage, $offset)->getResultArray();

        return [
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($total / $perPage)
            ]
        ];
    }

    public function getBookingDetails($bookingId, $userId = null)
    {
        $builder = $this->builder();
        $builder->select('bookings.*, 
            routes.route_id, departure.island_name as departure_island, 
            arrival.island_name as arrival_island, 
            boats.boat_name, boats.boat_type, boats.capacity, boats.price_per_trip,
            schedules.departure_date, schedules.departure_time, schedules.available_seats')
            ->join('schedules', 'schedules.schedule_id = bookings.schedule_id')
            ->join('routes', 'routes.route_id = schedules.route_id')
            ->join('boats', 'boats.boat_id = schedules.boat_id')
            ->join('islands as departure', 'departure.island_id = routes.departure_island_id')
            ->join('islands as arrival', 'arrival.island_id = routes.arrival_island_id')
            ->where('bookings.booking_id', $bookingId);

        // Filter by user if not admin
        if ($userId && $this->request->user->role !== 'admin') {
            $builder->where('bookings.user_id', $userId);
        }

        $booking = $builder->get()->getRowArray();

        if ($booking) {
            $passengerModel = new PassengerModel();
            $paymentModel = new PaymentModel();
            
            $booking['passengers'] = $passengerModel->where('booking_id', $bookingId)->findAll();
            $booking['payments'] = $paymentModel->where('booking_id', $bookingId)->findAll();
        }

        return $booking;
    }

    public function generateBookingCode()
    {
        $prefix = 'BOOK';
        $date = date('Ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 4));
        
        return $prefix . $date . $random;
    }
}