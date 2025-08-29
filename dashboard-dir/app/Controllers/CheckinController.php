<?php namespace App\Controllers;

use App\Models\BookingModel;
use App\Models\ScheduleModel;
use App\Models\OpenTripModel;

class CheckinController extends BaseController
{
    protected $bookingModel;
    protected $scheduleModel;
    protected $openTripModel;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
        $this->scheduleModel = new ScheduleModel();
        $this->openTripModel = new OpenTripModel();
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        $data = [
            'title' => 'Check-in Kapal',
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/checkin/index', $data);
    }

    public function process()
    {
        $bookingCode = $this->request->getPost('booking_code');
        
        // Validasi format booking code
        if (!preg_match('/^BOOK-[A-Z0-9]+(-\d+)?$/', $bookingCode)) {
            return redirect()->back()->withInput()->with('error', 'Format kode booking tidak valid');
        }

        // Parse kode booking dan jumlah penumpang
        $parts = explode('-', $bookingCode);
        $baseBookingCode = $parts[0] . '-' . $parts[1];
        $passengerCount = 1; // default 1 penumpang

        // Jika ada bagian ketiga (jumlah penumpang)
        if (isset($parts[2]) && is_numeric($parts[2])) {
            $passengerCount = (int) $parts[2];
        }

        // Cari booking berdasarkan kode
        $booking = $this->bookingModel->where('booking_code', $baseBookingCode)->first();
        
        if (!$booking) {
            return redirect()->back()->withInput()->with('error', 'Booking tidak ditemukan');
        }

        // Cek status booking
        if ($booking['booking_status'] !== 'confirmed' && $booking['booking_status'] !== 'paid') {
            return redirect()->back()->withInput()->with('error', 'Booking belum dikonfirmasi atau dibayar');
        }

        // Cek apakah sudah check-in sebelumnya
        $alreadyCheckedIn = $this->bookingModel->where('booking_code', $baseBookingCode)
            ->where('checkin_count >=', $passengerCount)
            ->first();

        if ($alreadyCheckedIn) {
            return redirect()->back()->withInput()->with('error', 'Penumpang sudah check-in sebelumnya');
        }

        // Update jumlah check-in
        $newCheckinCount = ($booking['checkin_count'] ?? 0) + $passengerCount;
        
        if ($newCheckinCount > $booking['passenger_count']) {
            return redirect()->back()->withInput()->with('error', 'Jumlah check-in melebihi jumlah penumpang booking');
        }

        // Kurangi kapasitas kursi yang tersedia
        $schedule = $this->scheduleModel->find($booking['schedule_id']);
        
        if (!$schedule) {
            return redirect()->back()->withInput()->with('error', 'Jadwal tidak ditemukan');
        }

        if ($schedule['available_seats'] < $passengerCount) {
            return redirect()->back()->withInput()->with('error', 'Kursi tidak cukup tersedia');
        }

        // Mulai transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update available seats di schedule
            $this->scheduleModel->update($schedule['schedule_id'], [
                'available_seats' => $schedule['available_seats'] - $passengerCount
            ]);

            // Update checkin count di booking
            $this->bookingModel->update($booking['booking_id'], [
                'checkin_count' => $newCheckinCount,
                'checkin_time' => date('Y-m-d H:i:s')
            ]);

            // Jika open trip, update juga di open_trip_schedules
            if ($booking['is_open_trip'] && $booking['open_trip_id']) {
                $openTrip = $this->openTripModel->find($booking['open_trip_id']);
                if ($openTrip) {
                    $this->openTripModel->update($openTrip['open_trip_id'], [
                        'reserved_seats' => $openTrip['reserved_seats'] + $passengerCount
                    ]);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                throw new \Exception('Transaction failed');
            }

            return redirect()->back()->with('success', "Check-in berhasil untuk $passengerCount penumpang");

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal melakukan check-in: ' . $e->getMessage());
        }
    }
}