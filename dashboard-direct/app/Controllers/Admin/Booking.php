<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BookingModel;
use App\Models\PassengerModel;

class Booking extends BaseController
{
    protected $bookingModel;
    protected $passengerModel;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
        $this->passengerModel = new PassengerModel();
    }

    public function index()
    {
        $status = $this->request->getGet('status') ?? null;
        
        $data = [
            'bookings' => $this->bookingModel->getBookingsWithDetails($status),
            'statusFilter' => $status
        ];

        return view('admin/booking/index', $data);
    }

    public function view($bookingId)
    {
        $booking = $this->bookingModel->getBookingWithPassengers($bookingId);
        if (!$booking) {
            return redirect()->to('/admin/booking')->with('error', 'Booking not found');
        }

        return view('admin/booking/view', ['booking' => $booking]);
    }

    public function updateStatus($bookingId)
    {
        $booking = $this->bookingModel->find($bookingId);
        if (!$booking) {
            return redirect()->back()->with('error', 'Booking not found');
        }

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'status' => 'required|in_list[pending,confirmed,paid,completed,canceled]',
                'notes' => 'permit_empty|max_length[500]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $data = [
                'booking_id' => $bookingId,
                'booking_status' => $this->request->getPost('status'),
                'notes' => $this->request->getPost('notes')
            ];

            if ($this->bookingModel->save($data)) {
                return redirect()->to('/admin/booking')->with('success', 'Booking status updated');
            } else {
                return redirect()->back()->with('error', 'Failed to update booking status');
            }
        }

        return view('admin/booking/update_status', ['booking' => $booking]);
    }

    public function cancel($bookingId)
    {
        $booking = $this->bookingModel->find($bookingId);
        if (!$booking) {
            return redirect()->back()->with('error', 'Booking not found');
        }

        if ($this->bookingModel->updateBookingStatus($bookingId, 'canceled')) {
            // If open trip, update available seats
            if ($booking['is_open_trip'] && $booking['open_trip_id']) {
                $openTripModel = new OpenTripScheduleModel();
                $openTripModel->increaseAvailableSeats($booking['open_trip_id'], $booking['passenger_count']);
            }

            return redirect()->to('/admin/booking')->with('success', 'Booking canceled');
        } else {
            return redirect()->back()->with('error', 'Failed to cancel booking');
        }
    }
}