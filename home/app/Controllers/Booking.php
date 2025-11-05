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
public function process()
{
    // Only allow AJAX requests
    if (!$this->request->isAJAX()) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Hanya request AJAX yang diperbolehkan'
        ]);
    }

    // Set content type to JSON
    $this->response->setContentType('application/json');

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
    $passengers = $this->request->getPost('passengers');
    $paymentMethod = $this->request->getPost('payment_method');

    // Decode passengers JSON
    $passengersData = json_decode($passengers, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Format data penumpang tidak valid'
        ]);
    }

    // Cek schedule
    $schedule = $this->scheduleModel->find($scheduleId);
    if (!$schedule) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Jadwal tidak ditemukan'
        ]);
    }

    if ($schedule['available_seats'] < $passengerCount) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Kursi tidak tersedia. Hanya tersedia ' . $schedule['available_seats'] . ' kursi'
        ]);
    }

    // Generate booking code
    $bookingCode = $this->bookingModel->generateBookingCode();

    // Hitung total harga
    $scheduleDetails = $this->scheduleModel->getScheduleWithDetails($scheduleId);
    if (!$scheduleDetails) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Detail jadwal tidak ditemukan'
        ]);
    }

    $totalPrice = $scheduleDetails['price_per_trip'] * $passengerCount;

    // Data booking
    $bookingData = [
        'booking_code' => $bookingCode,
        'user_id' => $_SESSION['userData']['user_id'],
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
            $errorMessage = 'Gagal menyimpan booking';
            if ($this->bookingModel->errors()) {
                $errorMessage .= ': ' . implode(', ', $this->bookingModel->errors());
            }
            throw new \Exception($errorMessage);
        }

        $bookingId = $this->bookingModel->getInsertID();

        // Simpan data penumpang
        $passengerSaveResult = $this->passengerModel->addPassengers($bookingId, $passengersData);
        if (!$passengerSaveResult) {
            throw new \Exception('Gagal menyimpan data penumpang');
        }

        // Kurangi kursi tersedia
        $decrementResult = $this->scheduleModel->decrementSeats($scheduleId, $passengerCount);
        if (!$decrementResult) {
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
        
        if (!$booking || $booking['user_id'] != $_SESSION['userData']['user_id']) {
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
        // Pastikan user sudah login
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        
        $bookingModel = new BookingModel();
        $user_id = $_SESSION['userData']['user_id'];
        
        // Ambil booking berdasarkan status
        $upcoming_bookings = $bookingModel->getUserBookingsByStatus($user_id, ['pending', 'confirmed', 'paid']);
        $completed_bookings = $bookingModel->getUserBookingsByStatus($user_id, ['completed']);
        $canceled_bookings = $bookingModel->getUserBookingsByStatus($user_id, ['canceled']);
        
        $data = [
            'title' => 'Pemesanan Saya - Raja Ampat Boat Services',
            'upcoming_bookings' => $upcoming_bookings,
            'completed_bookings' => $completed_bookings,
            'canceled_bookings' => $canceled_bookings,
            'active' => 'my_bookings'
        ];
        
        return $this->render('booking/my_bookings', $data);
    }
    
    public function cancel()
    {
        // Pastikan user sudah login
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        
        $booking_id = $this->request->getPost('booking_id');
        $reason = $this->request->getPost('cancel_reason');
        
        $bookingModel = new BookingModel();
        $user_id = $_SESSION['userData']['user_id'];
        
        // Pastikan booking milik user yang login
        $booking = $bookingModel->where('booking_id', $booking_id)
                               ->where('user_id', $user_id)
                               ->first();
        
        if (!$booking) {
            return redirect()->back()->with('error', 'Pemesanan tidak ditemukan');
        }
        
        // Update status booking menjadi canceled
        if ($bookingModel->update($booking_id, [
            'booking_status' => 'canceled',
            'notes' => $reason ? "Dibatalkan: " . $reason : 'Dibatalkan oleh pengguna',
            'updated_at' => date('Y-m-d H:i:s')
        ])) {
            return redirect()->back()->with('success', 'Pemesanan berhasil dibatalkan');
        } else {
            return redirect()->back()->with('error', 'Gagal membatalkan pemesanan');
        }
    }

    /**
     * Detail booking
     */
    public function detail($bookingCode)
    {
        $booking = $this->bookingModel->getBookingWithDetails($bookingCode);
        
        if (!$booking || $booking['user_id'] != $_SESSION['userData']['user_id']) {
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
        
        if (!$booking || $booking['user_id'] != $_SESSION['userData']['user_id']) {
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
    // public function cancel()
    // {
    //     if (!$this->request->isAJAX()) {
    //         return redirect()->back();
    //     }

    //     $bookingCode = $this->request->getPost('booking_code');
    //     $booking = $this->bookingModel->where('booking_code', $bookingCode)->first();

    //     if (!$booking || $booking['user_id'] != $_SESSION['userData']['user_id']) {
    //         return $this->response->setJSON([
    //             'status' => 'error',
    //             'message' => 'Booking tidak ditemukan'
    //         ]);
    //     }

    //     // Hanya bisa cancel jika masih pending
    //     if (!in_array($booking['booking_status'], ['pending', 'confirmed'])) {
    //         return $this->response->setJSON([
    //             'status' => 'error',
    //             'message' => 'Booking tidak dapat dibatalkan'
    //         ]);
    //     }

    //     // Start transaction
    //     $this->db->transStart();

    //     try {
    //         // Kembalikan kursi
    //         $this->scheduleModel->incrementSeats($booking['schedule_id'], $booking['passenger_count']);

    //         // Update status booking
    //         $this->bookingModel->update($booking['booking_id'], [
    //             'booking_status' => 'canceled',
    //             'payment_status' => 'failed'
    //         ]);

    //         $this->db->transComplete();

    //         return $this->response->setJSON([
    //             'status' => 'success',
    //             'message' => 'Booking berhasil dibatalkan'
    //         ]);

    //     } catch (\Exception $e) {
    //         $this->db->transRollback();
    //         return $this->response->setJSON([
    //             'status' => 'error',
    //             'message' => $e->getMessage()
    //         ]);
    //     }
    // }
}