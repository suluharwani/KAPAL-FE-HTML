<?php namespace App\Controllers;

use App\Models\BookingModel;
use App\Models\ScheduleModel;
use App\Models\BoatModel;
use App\Models\RouteModel;
use App\Models\PassengerModel;

class Booking extends BaseController
{
    protected $bookingModel;
    protected $scheduleModel;
    protected $boatModel;
    protected $routeModel;
    protected $passengerModel;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
        $this->scheduleModel = new ScheduleModel();
        $this->boatModel = new BoatModel();
        $this->routeModel = new RouteModel();
        $this->passengerModel = new PassengerModel();
        
        helper(['form', 'text']);
    }

    /**
     * Form pemesanan
     */
    public function create($scheduleId)
    {
        // Cek schedule
        $schedule = $this->scheduleModel->getScheduleWithDetails($scheduleId);
        if (!$schedule) {
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan');
        }

        // Cek ketersediaan kursi
        if ($schedule['available_seats'] <= 0) {
            return redirect()->back()->with('error', 'Kursi tidak tersedia');
        }

        $data = [
            'title' => 'Pemesanan Tiket - ' . $schedule['boat_name'],
            'active' => 'booking',
            'schedule' => $schedule,
            'user' => session()->get('user'),
            'validation' => \Config\Services::validation()
        ];

        $this->render('booking/create', $data);
    }

    /**
     * Proses pemesanan
     */
/**
 * Proses pemesanan
 */
public function process()
{
    if (!$this->request->isAJAX()) {
        return redirect()->back();
    }

    $validation = \Config\Services::validation();
    $validation->setRules([
        'schedule_id' => 'required|numeric',
        'passenger_count' => 'required|numeric|greater_than[0]',
        'passengers' => 'required',
        'payment_method' => 'required|in_list[transfer,cash]'
    ]);

    if (!$validation->withRequest($this->request)->run()) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Validasi gagal',
            'errors' => $validation->getErrors()
        ]);
    }

    $scheduleId = $this->request->getPost('schedule_id');
    $passengerCount = $this->request->getPost('passenger_count');
    $passengers = json_decode($this->request->getPost('passengers'), true);
    $paymentMethod = $this->request->getPost('payment_method');

    // Cek schedule
    $schedule = $this->scheduleModel->find($scheduleId);
    if (!$schedule || $schedule['available_seats'] < $passengerCount) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Kursi tidak tersedia'
        ]);
    }

    // Generate booking code
    $bookingCode = $this->bookingModel->generateBookingCode();

    // Hitung total harga
    $scheduleDetails = $this->scheduleModel->getScheduleWithDetails($scheduleId);
    $totalPrice = $scheduleDetails['price_per_trip'] * $passengerCount;

    // Data booking
    $bookingData = [
        'booking_code' => $bookingCode,
        'user_id' => session()->get('user')['user_id'],
        'schedule_id' => $scheduleId,
        'passenger_count' => $passengerCount,
        'total_price' => $totalPrice,
        'payment_method' => $paymentMethod,
        'booking_status' => 'pending',
        'payment_status' => 'pending',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];

    // Start transaction
    $this->db->transStart();

    try {
        // Simpan booking
        if (!$this->bookingModel->save($bookingData)) {
            throw new \Exception('Gagal menyimpan booking: ' . implode(', ', $this->bookingModel->errors()));
        }

        $bookingId = $this->bookingModel->getInsertID();

        // Simpan data penumpang menggunakan method baru
        if (!$this->passengerModel->addPassengers($bookingId, $passengers)) {
            throw new \Exception('Gagal menyimpan data penumpang');
        }

        // Kurangi kursi tersedia
        if (!$this->scheduleModel->decrementSeats($scheduleId, $passengerCount)) {
            throw new \Exception('Gagal update kursi tersedia');
        }

        $this->db->transComplete();

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Booking berhasil dibuat',
            'booking_code' => $bookingCode
        ]);

    } catch (\Exception $e) {
        $this->db->transRollback();
        return $this->response->setJSON([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

    /**
     * Halaman sukses booking
     */
    public function success($bookingCode)
    {
        $booking = $this->bookingModel->getBookingWithDetails($bookingCode);
        
        if (!$booking || $booking['user_id'] != session()->get('user')['user_id']) {
            return redirect()->to('/')->with('error', 'Booking tidak ditemukan');
        }

        $data = [
            'title' => 'Booking Sukses - ' . $bookingCode,
            'active' => 'booking',
            'booking' => $booking
        ];

        $this->render('booking/success', $data);
    }

    /**
     * Daftar booking user
     */
    public function myBookings()
    {
        $userId = session()->get('user')['user_id'];
        $bookings = $this->bookingModel->where('user_id', $userId)
                                     ->orderBy('created_at', 'DESC')
                                     ->findAll();

        $data = [
            'title' => 'Booking Saya',
            'active' => 'my_bookings',
            'bookings' => $bookings
        ];

        $this->render('booking/my_bookings', $data);
    }

    /**
     * Detail booking
     */
    public function detail($bookingCode)
    {
        $booking = $this->bookingModel->getBookingWithDetails($bookingCode);
        
        if (!$booking || $booking['user_id'] != session()->get('user')['user_id']) {
            return redirect()->to('/')->with('error', 'Booking tidak ditemukan');
        }

        $data = [
            'title' => 'Detail Booking - ' . $bookingCode,
            'active' => 'booking',
            'booking' => $booking
        ];

        $this->render('booking/detail', $data);
    }

    /**
     * Print tiket
     */
    public function printTicket($bookingCode)
    {
        $booking = $this->bookingModel->getBookingWithDetails($bookingCode);
        
        if (!$booking || $booking['user_id'] != session()->get('user')['user_id']) {
            return redirect()->to('/')->with('error', 'Booking tidak ditemukan');
        }

        // Hanya bisa print jika sudah bayar atau confirmed
        if (!in_array($booking['payment_status'], ['paid', 'partial']) && 
            !in_array($booking['booking_status'], ['confirmed', 'completed'])) {
            return redirect()->to('/booking/detail/' . $bookingCode)
                           ->with('error', 'Tiket hanya bisa dicetak setelah pembayaran dikonfirmasi');
        }

        $data = [
            'title' => 'Tiket - ' . $bookingCode,
            'booking' => $booking
        ];

        $this->render('booking/print_ticket', $data);
    }

    /**
     * Batalkan booking
     */
    public function cancel()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $bookingCode = $this->request->getPost('booking_code');
        $booking = $this->bookingModel->where('booking_code', $bookingCode)->first();

        if (!$booking || $booking['user_id'] != session()->get('user')['user_id']) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Booking tidak ditemukan'
            ]);
        }

        // Hanya bisa cancel jika masih pending
        if (!in_array($booking['booking_status'], ['pending', 'confirmed'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Booking tidak dapat dibatalkan'
            ]);
        }

        // Start transaction
        $this->db->transStart();

        try {
            // Kembalikan kursi
            $this->scheduleModel->incrementSeats($booking['schedule_id'], $booking['passenger_count']);

            // Update status booking
            $this->bookingModel->update($booking['booking_id'], [
                'booking_status' => 'canceled',
                'payment_status' => 'failed'
            ]);

            $this->db->transComplete();

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Booking berhasil dibatalkan'
            ]);

        } catch (\Exception $e) {
            $this->db->transRollback();
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}