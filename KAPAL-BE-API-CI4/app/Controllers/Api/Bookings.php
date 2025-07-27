<?php namespace App\Controllers\Api;

use App\Models\BookingModel;
use App\Models\ScheduleModel;
use App\Models\PassengerModel;
use App\Models\PaymentModel;

class Bookings extends BaseApiController
{
    protected $modelName = BookingModel::class;

    public function __construct()
    {
        $this->model = new BookingModel();
        $this->scheduleModel = new ScheduleModel();
        $this->passengerModel = new PassengerModel();
        $this->paymentModel = new PaymentModel();
    }

    public function index()
    {
        $params = $this->getPaginationParams();
        $userId = $this->request->user->role === 'admin' ? null : $this->request->user->user_id;
        $bookings = $this->model->getPaginated($params, $userId);

        return $this->respond([
            'status' => 200,
            'data' => $bookings['data'],
            'pagination' => $bookings['pagination']
        ]);
    }

    public function show($id = null)
    {
        $userId = $this->request->user->role === 'admin' ? null : $this->request->user->user_id;
        $booking = $this->model->getBookingDetails($id, $userId);

        if (!$booking) {
            return $this->respondNotFound('Booking not found');
        }

        return $this->respond([
            'status' => 200,
            'data' => $booking
        ]);
    }

    public function create()
    {
        $rules = [
            'schedule_id' => 'required|integer',
            'passenger_count' => 'required|integer|greater_than[0]',
            'passengers' => 'required|array|min_length[1]',
            'passengers.*.full_name' => 'required',
            'passengers.*.identity_number' => 'permit_empty',
            'passengers.*.phone' => 'permit_empty',
            'passengers.*.age' => 'permit_empty|integer',
            'payment_method' => 'required|in_list[transfer,cash]',
            'notes' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        // Check schedule availability
        $schedule = $this->scheduleModel->find($this->request->getVar('schedule_id'));
        if (!$schedule || $schedule['status'] !== 'available') {
            return $this->fail('Schedule not available', 400);
        }

        $passengerCount = (int) $this->request->getVar('passenger_count');
        if ($schedule['available_seats'] < $passengerCount) {
            return $this->fail('Not enough available seats', 400);
        }

        // Calculate total price
        $boat = $this->scheduleModel->getBoatForSchedule($this->request->getVar('schedule_id'));
        $totalPrice = $boat['price_per_trip'] * $passengerCount;

        // Create booking
        $bookingData = [
            'booking_code' => $this->model->generateBookingCode(),
            'user_id' => $this->request->user->user_id,
            'schedule_id' => $this->request->getVar('schedule_id'),
            'passenger_count' => $passengerCount,
            'total_price' => $totalPrice,
            'booking_status' => 'pending',
            'payment_method' => $this->request->getVar('payment_method'),
            'payment_status' => 'pending',
            'notes' => $this->request->getVar('notes')
        ];

        $this->db->transStart();

        $bookingId = $this->model->insert($bookingData);
        
        // Add passengers
        foreach ($this->request->getVar('passengers') as $passenger) {
            $this->passengerModel->insert([
                'booking_id' => $bookingId,
                'full_name' => $passenger['full_name'],
                'identity_number' => $passenger['identity_number'] ?? null,
                'phone' => $passenger['phone'] ?? null,
                'age' => $passenger['age'] ?? null
            ]);
        }

        // Update available seats
        $this->scheduleModel->update($this->request->getVar('schedule_id'), [
            'available_seats' => $schedule['available_seats'] - $passengerCount
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->failServerError('Failed to create booking');
        }

        return $this->respondCreated([
            'booking_id' => $bookingId,
            'booking_code' => $bookingData['booking_code']
        ], 'Booking created successfully');
    }

    public function updateStatus($id = null)
    {
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only admin can update booking status');
        }

        $rules = [
            'status' => 'required|in_list[pending,confirmed,paid,completed,canceled]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $booking = $this->model->find($id);
        if (!$booking) {
            return $this->respondNotFound('Booking not found');
        }

        $status = $this->request->getVar('status');
        
        // If canceling, return available seats
        if ($status === 'canceled' && $booking['booking_status'] !== 'canceled') {
            $schedule = $this->scheduleModel->find($booking['schedule_id']);
            $this->scheduleModel->update($booking['schedule_id'], [
                'available_seats' => $schedule['available_seats'] + $booking['passenger_count']
            ]);
        }

        $this->model->update($id, ['booking_status' => $status]);

        return $this->respondUpdated(['booking_id' => $id], 'Booking status updated');
    }

    public function cancel($id = null)
    {
        $userId = $this->request->user->user_id;
        $booking = $this->model->where('booking_id', $id)
                              ->where('user_id', $userId)
                              ->first();

        if (!$booking) {
            return $this->respondNotFound('Booking not found or you are not authorized');
        }

        if (!in_array($booking['booking_status'], ['pending', 'confirmed'])) {
            return $this->fail('Booking cannot be canceled at this stage', 400);
        }

        $this->db->transStart();

        // Update booking status
        $this->model->update($id, ['booking_status' => 'canceled']);

        // Return available seats
        $schedule = $this->scheduleModel->find($booking['schedule_id']);
        $this->scheduleModel->update($booking['schedule_id'], [
            'available_seats' => $schedule['available_seats'] + $booking['passenger_count']
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->failServerError('Failed to cancel booking');
        }

        return $this->respondUpdated(['booking_id' => $id], 'Booking canceled successfully');
    }
}