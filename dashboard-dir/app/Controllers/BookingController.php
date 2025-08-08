<?php namespace App\Controllers;

use App\Models\BookingModel;
use App\Models\ScheduleModel;
use App\Models\BoatModel;
use App\Models\UserModel;

class BookingController extends BaseController
{
    protected $bookingModel;
    protected $scheduleModel;
    protected $boatModel;
    protected $userModel;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
        $this->scheduleModel = new ScheduleModel();
        $this->boatModel = new BoatModel();
        $this->userModel = new UserModel();
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        $data = [
            'title' => 'Manage Bookings',
            'bookings' => $this->bookingModel->getBookingsWithDetails(),
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/bookings/index', $data);
    }

    public function show($id)
    {
        $booking = $this->bookingModel->getBookingDetails($id);
        if (!$booking) {
            return redirect()->to('/admin/bookings')->with('error', 'Booking not found');
        }

        $data = [
            'title' => 'Booking Details',
            'booking' => $booking,
            'passengers' => $this->bookingModel->getPassengers($id),
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/bookings/show', $data);
    }

    public function updateStatus($id)
    {
        $status = $this->request->getPost('status');
        $validStatuses = ['pending', 'confirmed', 'paid', 'completed', 'canceled'];

        if (!in_array($status, $validStatuses)) {
            return redirect()->back()->with('error', 'Invalid status');
        }

        if ($this->bookingModel->update($id, ['booking_status' => $status])) {
            return redirect()->back()->with('success', 'Booking status updated');
        } else {
            return redirect()->back()->with('error', 'Failed to update status');
        }
    }

    public function updatePaymentStatus($id)
    {
        $status = $this->request->getPost('status');
        $validStatuses = ['pending', 'partial', 'paid', 'failed'];

        if (!in_array($status, $validStatuses)) {
            return redirect()->back()->with('error', 'Invalid payment status');
        }

        if ($this->bookingModel->update($id, ['payment_status' => $status])) {
            return redirect()->back()->with('success', 'Payment status updated');
        } else {
            return redirect()->back()->with('error', 'Failed to update payment status');
        }
    }
}