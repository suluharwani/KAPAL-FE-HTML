<?php namespace App\Controllers;

use App\Models\ScheduleModel;
use App\Models\BoatModel;
use App\Models\BookingModel;
use App\Models\PassengerModel;
use App\Models\PaymentModel;

class Booking extends BaseController
{
    public function checkAvailability()
    {
        $scheduleModel = new ScheduleModel();
        $scheduleId = $this->request->getGet('schedule_id');

        $schedule = $scheduleModel->getScheduleWithDetails($scheduleId);
        if (!$schedule) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Schedule not found'
            ]);
        }

        $passengerCount = $this->request->getGet('passenger_count') ?? 1;

        if ($schedule['available_seats'] < $passengerCount) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Not enough available seats'
            ]);
        }

        $totalPrice = $schedule['price_per_trip'] * $passengerCount;

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'schedule' => $schedule,
                'passenger_count' => $passengerCount,
                'total_price' => $totalPrice
            ]
        ]);
    }

    public function create()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please login to make a booking');
        }

        $scheduleModel = new ScheduleModel();
        $scheduleId = $this->request->getGet('schedule_id');

        $schedule = $scheduleModel->getScheduleWithDetails($scheduleId);
        if (!$schedule) {
            return redirect()->back()->with('error', 'Schedule not found');
        }

        $passengerCount = $this->request->getGet('passengers') ?? 1;

        if ($schedule['available_seats'] < $passengerCount) {
            return redirect()->back()->with('error', 'Not enough available seats');
        }

        $totalPrice = $schedule['price_per_trip'] * $passengerCount;

        $data = [
            'schedule' => $schedule,
            'passenger_count' => $passengerCount,
            'total_price' => $totalPrice
        ];

        return view('booking_create', $data);
    }

    public function store()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please login to make a booking');
        }

        $rules = [
            'schedule_id' => 'required|numeric',
            'passenger_count' => 'required|numeric|greater_than[0]',
            'passengers' => 'required',
            'passengers.*.full_name' => 'required|min_length[3]|max_length[100]',
            'passengers.*.identity_number' => 'permit_empty|max_length[50]',
            'passengers.*.phone' => 'permit_empty|max_length[20]',
            'passengers.*.age' => 'permit_empty|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $scheduleModel = new ScheduleModel();
        $bookingModel = new BookingModel();
        $passengerModel = new PassengerModel();

        $scheduleId = $this->request->getPost('schedule_id');
        $passengerCount = $this->request->getPost('passenger_count');
        $passengers = $this->request->getPost('passengers');

        $schedule = $scheduleModel->find($scheduleId);
        if (!$schedule) {
            return redirect()->back()->with('error', 'Schedule not found');
        }

        // Check availability again
        if ($schedule['available_seats'] < $passengerCount) {
            return redirect()->back()->with('error', 'Not enough available seats');
        }

        // Calculate total price
        $boatModel = new BoatModel();
        $boat = $boatModel->find($schedule['boat_id']);
        $totalPrice = $boat['price_per_trip'] * $passengerCount;

        // Create booking
        $bookingData = [
            'booking_code' => $bookingModel->generateBookingCode(),
            'user_id' => session()->get('user_id'),
            'schedule_id' => $scheduleId,
            'passenger_count' => $passengerCount,
            'total_price' => $totalPrice,
            'booking_status' => 'pending',
            'payment_method' => 'transfer',
            'payment_status' => 'pending'
        ];

        if ($bookingId = $bookingModel->insert($bookingData)) {
            // Add passengers
            $passengerModel->addPassengers($bookingId, $passengers);

            // Update available seats
            $scheduleModel->decrementAvailableSeats($scheduleId, $passengerCount);

            return redirect()->to('/booking/' . $bookingId)->with('success', 'Booking created successfully');
        } else {
            return redirect()->back()->with('error', 'Failed to create booking');
        }
    }

    public function show($bookingId)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please login to view booking');
        }

        $bookingModel = new BookingModel();
        $paymentModel = new PaymentModel();

        $booking = $bookingModel->getBookingWithDetails($bookingId);
        if (!$booking) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Check if booking belongs to current user
        if ($booking['user_id'] != session()->get('user_id') && session()->get('role') !== 'admin') {
            return redirect()->to('/')->with('error', 'You are not authorized to view this booking');
        }

        $data = [
            'booking' => $booking,
            'payments' => $paymentModel->where('booking_id', $bookingId)->findAll()
        ];

        return view('booking_detail', $data);
    }

    public function payment($bookingId)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please login to make payment');
        }

        $bookingModel = new BookingModel();
        $booking = $bookingModel->find($bookingId);

        if (!$booking) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Check if booking belongs to current user
        if ($booking['user_id'] != session()->get('user_id') && session()->get('role') !== 'admin') {
            return redirect()->to('/')->with('error', 'You are not authorized to make payment for this booking');
        }

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'amount' => 'required|numeric|greater_than[0]',
                'payment_method' => 'required|in_list[transfer,cash]',
                'bank_name' => 'required_if[payment_method,transfer]',
                'account_number' => 'required_if[payment_method,transfer]',
                'receipt_image' => 'required_if[payment_method,transfer]|uploaded[receipt_image]|max_size[receipt_image,2048]|is_image[receipt_image]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $paymentModel = new PaymentModel();

            $data = [
                'booking_id' => $bookingId,
                'amount' => $this->request->getPost('amount'),
                'payment_method' => $this->request->getPost('payment_method'),
                'bank_name' => $this->request->getPost('bank_name'),
                'account_number' => $this->request->getPost('account_number'),
                'status' => 'pending'
            ];

            // Handle receipt image upload
            if ($this->request->getPost('payment_method') === 'transfer') {
                $image = $this->request->getFile('receipt_image');
                $imageName = $image->getRandomName();
                $image->move(ROOTPATH . 'public/assets/img/payments', $imageName);
                $data['receipt_image'] = 'assets/img/payments/' . $imageName;
            }

            if ($paymentModel->save($data)) {
                // Update booking payment status
                $bookingModel->update($bookingId, ['payment_status' => 'pending']);

                return redirect()->to('/booking/' . $bookingId)->with('success', 'Payment submitted successfully. Waiting for verification.');
            } else {
                // Clean up uploaded image if failed to save
                if (isset($data['receipt_image'])) {
                    unlink(ROOTPATH . 'public/' . $data['receipt_image']);
                }
                return redirect()->back()->with('error', 'Failed to submit payment');
            }
        }

        $data = [
            'booking' => $booking,
            'paymentMethods' => [
                'transfer' => 'Bank Transfer',
                'cash' => 'Cash Payment'
            ]
        ];

        return view('booking_payment', $data);
    }

    public function cancel($bookingId)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please login to cancel booking');
        }

        $bookingModel = new BookingModel();
        $booking = $bookingModel->find($bookingId);

        if (!$booking) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Check if booking belongs to current user
        if ($booking['user_id'] != session()->get('user_id') && session()->get('role') !== 'admin') {
            return redirect()->to('/')->with('error', 'You are not authorized to cancel this booking');
        }

        // Check if booking can be canceled
        if (!in_array($booking['booking_status'], ['pending', 'confirmed'])) {
            return redirect()->back()->with('error', 'Booking cannot be canceled at this stage');
        }

        if ($bookingModel->update($bookingId, ['booking_status' => 'canceled'])) {
            // Update available seats
            $scheduleModel = new ScheduleModel();
            $scheduleModel->incrementAvailableSeats($booking['schedule_id'], $booking['passenger_count']);

            return redirect()->to('/booking/' . $bookingId)->with('success', 'Booking canceled successfully');
        } else {
            return redirect()->back()->with('error', 'Failed to cancel booking');
        }
    }
}