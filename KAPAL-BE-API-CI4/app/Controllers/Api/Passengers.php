<?php namespace App\Controllers\Api;

use App\Models\PassengerModel;
use App\Models\BookingModel;

class Passengers extends BaseApiController
{
    protected $modelName = PassengerModel::class;

    public function __construct()
    {
        $this->model = new PassengerModel();
        $this->bookingModel = new BookingModel();
    }

    public function index($bookingId = null)
    {
        // Verify booking exists and belongs to user (if not admin)
        $booking = $this->bookingModel->find($bookingId);
        if (!$booking) {
            return $this->respondNotFound('Booking not found');
        }

        if ($this->request->user->role !== 'admin' && 
            $booking['user_id'] !== $this->request->user->user_id) {
            return $this->failForbidden('You are not authorized to view these passengers');
        }

        $passengers = $this->model->getPassengersByBooking($bookingId);

        return $this->respond([
            'status' => 200,
            'data' => $passengers
        ]);
    }

    public function update($id = null)
    {
        $passenger = $this->model->find($id);
        if (!$passenger) {
            return $this->respondNotFound('Passenger not found');
        }

        // Verify booking belongs to user (if not admin)
        $booking = $this->bookingModel->find($passenger['booking_id']);
        if ($this->request->user->role !== 'admin' && 
            $booking['user_id'] !== $this->request->user->user_id) {
            return $this->failForbidden('You are not authorized to update this passenger');
        }

        // Cannot update passengers if booking is already completed/canceled
        if (in_array($booking['booking_status'], ['completed', 'canceled'])) {
            return $this->fail('Cannot update passengers for completed/canceled bookings', 400);
        }

        $rules = [
            'full_name' => 'permit_empty|min_length[3]|max_length[100]',
            'identity_number' => 'permit_empty|max_length[50]',
            'phone' => 'permit_empty|max_length[20]',
            'age' => 'permit_empty|integer'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'full_name' => $this->request->getVar('full_name') ?? $passenger['full_name'],
            'identity_number' => $this->request->getVar('identity_number') ?? $passenger['identity_number'],
            'phone' => $this->request->getVar('phone') ?? $passenger['phone'],
            'age' => $this->request->getVar('age') ?? $passenger['age']
        ];

        if ($this->model->update($id, $data)) {
            return $this->respondUpdated(['passenger_id' => $id]);
        } else {
            return $this->failServerError('Failed to update passenger');
        }
    }

    public function delete($id = null)
    {
        $passenger = $this->model->find($id);
        if (!$passenger) {
            return $this->respondNotFound('Passenger not found');
        }

        // Verify booking belongs to user (if not admin)
        $booking = $this->bookingModel->find($passenger['booking_id']);
        if ($this->request->user->role !== 'admin' && 
            $booking['user_id'] !== $this->request->user->user_id) {
            return $this->failForbidden('You are not authorized to delete this passenger');
        }

        // Cannot delete passengers if booking is already completed/canceled
        if (in_array($booking['booking_status'], ['completed', 'canceled'])) {
            return $this->fail('Cannot delete passengers from completed/canceled bookings', 400);
        }

        // Cannot delete if it's the last passenger
        $passengerCount = $this->model->where('booking_id', $booking['booking_id'])->countAllResults();
        if ($passengerCount <= 1) {
            return $this->fail('Cannot delete the last passenger from a booking', 400);
        }

        if ($this->model->delete($id)) {
            // Update passenger count in booking
            $this->bookingModel->update($booking['booking_id'], [
                'passenger_count' => $passengerCount - 1,
                'total_price' => $booking['total_price'] - ($booking['total_price'] / $passengerCount)
            ]);

            return $this->respondDeleted();
        } else {
            return $this->failServerError('Failed to delete passenger');
        }
    }
}