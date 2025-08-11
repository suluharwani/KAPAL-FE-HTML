<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BoatModel;
use App\Models\ScheduleModel;
use App\Models\PaymentModel;
use App\Models\BookingModel;

class Dashboard extends BaseController
{
    protected $boatModel;
    protected $scheduleModel;
    protected $paymentModel;
    protected $bookingModel;

    public function __construct()
    {
        $this->boatModel = new BoatModel();
        $this->scheduleModel = new ScheduleModel();
        $this->paymentModel = new PaymentModel();
        $this->bookingModel = new BookingModel();
    }

   public function index()
{
    if (!session()->get('logged_in')) {
        return redirect()->to('/admin/login');
    }

    // Get current month and year
    $currentMonth = date('m');
    $currentYear = date('Y');

    // Monthly revenue data
    $revenueData = [];
    for ($i = 1; $i <= 12; $i++) {
        $month = str_pad($i, 2, '0', STR_PAD_LEFT);
        $revenue = $this->paymentModel
            ->selectSum('amount')
            ->where('status', 'verified')
            ->where('YEAR(payment_date)', $currentYear)
            ->where('MONTH(payment_date)', $month)
            ->get()
            ->getRow()
            ->amount;

        $revenueData[] = $revenue ? (float)$revenue : 0;
    }

    // Booking status statistics
    $bookingStats = $this->bookingModel
        ->select('booking_status, COUNT(*) as count')
        ->groupBy('booking_status')
        ->findAll();

    // Recent activities
    $activities = [];
    
    // Recent bookings
    $recentBookings = $this->bookingModel
        ->select('bookings.*, users.full_name as customer_name')
        ->join('users', 'users.user_id = bookings.user_id')
        ->orderBy('bookings.created_at', 'DESC')
        ->limit(5)
        ->findAll();
    
    foreach ($recentBookings as $booking) {
        $activities[] = [
            'type' => 'booking',
            'title' => 'New Booking #' . $booking['booking_code'],
            'description' => $booking['customer_name'] . ' made a new booking',
            'time' => $booking['created_at'],
            'icon' => 'fas fa-calendar-check'
        ];
    }

    // Recent payments
    $recentPayments = $this->paymentModel
        ->select('payments.*, bookings.booking_code, users.full_name as customer_name')
        ->join('bookings', 'bookings.booking_id = payments.booking_id')
        ->join('users', 'users.user_id = bookings.user_id')
        ->orderBy('payments.created_at', 'DESC')
        ->limit(5)
        ->findAll();
    
    foreach ($recentPayments as $payment) {
        $activities[] = [
            'type' => 'payment',
            'title' => 'New Payment for #' . $payment['booking_code'],
            'description' => $payment['customer_name'] . ' made a payment of Rp ' . number_format($payment['amount'], 0, ',', '.'),
            'time' => $payment['created_at'],
            'icon' => 'fas fa-money-bill-wave'
        ];
    }

    // Sort activities by time
    usort($activities, function($a, $b) {
        return strtotime($b['time']) - strtotime($a['time']);
    });

    // Take only 5 most recent activities
    $activities = array_slice($activities, 0, 5);

    $data = [
        'total_boats' => $this->boatModel->countAll(),
        'total_schedules' => $this->scheduleModel->countAll(),
        'total_payments' => $this->paymentModel->where('status', 'verified')->countAllResults(),
        'total_bookings' => $this->bookingModel->countAll(),
        'revenue_data' => $revenueData,
        'booking_stats' => $bookingStats,
        'activities' => $activities,
        'recent_payments' => $this->paymentModel
            ->select('payments.*, bookings.booking_code, users.full_name as customer_name')
            ->join('bookings', 'bookings.booking_id = payments.booking_id')
            ->join('users', 'users.user_id = bookings.user_id')
            ->orderBy('payments.created_at', 'DESC')
            ->findAll(5),
        'upcoming_schedules' => $this->scheduleModel
            ->select('schedules.*, boats.boat_name, 
                     di.island_name as departure_island, 
                     ai.island_name as arrival_island')
            ->join('boats', 'boats.boat_id = schedules.boat_id')
            ->join('routes r', 'r.route_id = schedules.route_id')
            ->join('islands di', 'di.island_id = r.departure_island_id')
            ->join('islands ai', 'ai.island_id = r.arrival_island_id')
            ->where('schedules.departure_date >=', date('Y-m-d'))
            ->orderBy('schedules.departure_date', 'ASC')
            ->orderBy('schedules.departure_time', 'ASC')
            ->findAll(5)
    ];

    return view('admin/dashboard', $data);
}
}