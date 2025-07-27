<?php namespace App\Controllers\Api;

use App\Models\BookingModel;
use App\Models\PaymentModel;
use CodeIgniter\API\ResponseTrait;

class Reports extends BaseApiController
{
    use ResponseTrait;

    public function bookingReport()
    {
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only admin can access reports');
        }

        $bookingModel = new BookingModel();
        $paymentModel = new PaymentModel();

        $dateFrom = $this->request->getGet('date_from') ?? date('Y-m-01');
        $dateTo = $this->request->getGet('date_to') ?? date('Y-m-t');

        $bookings = $bookingModel->where('created_at >=', $dateFrom)
                               ->where('created_at <=', $dateTo)
                               ->findAll();

        $payments = $paymentModel->where('payment_date >=', $dateFrom)
                               ->where('payment_date <=', $dateTo)
                               ->where('status', 'verified')
                               ->findAll();

        $totalBookings = count($bookings);
        $totalRevenue = array_sum(array_column($payments, 'amount'));
        $completedBookings = array_filter($bookings, function($booking) {
            return $booking['booking_status'] === 'completed';
        });

        $report = [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'total_bookings' => $totalBookings,
            'completed_bookings' => count($completedBookings),
            'total_revenue' => $totalRevenue,
            'bookings_by_status' => $this->groupByStatus($bookings),
            'revenue_by_month' => $this->revenueByMonth($payments, $dateFrom, $dateTo)
        ];

        return $this->respond([
            'status' => 200,
            'data' => $report
        ]);
    }

    protected function groupByStatus($bookings)
    {
        $statuses = ['pending', 'confirmed', 'paid', 'completed', 'canceled'];
        $result = array_fill_keys($statuses, 0);

        foreach ($bookings as $booking) {
            $result[$booking['booking_status']]++;
        }

        return $result;
    }

    protected function revenueByMonth($payments, $dateFrom, $dateTo)
    {
        $result = [];
        $current = strtotime($dateFrom);
        $end = strtotime($dateTo);

        while ($current <= $end) {
            $month = date('Y-m', $current);
            $result[$month] = 0;
            $current = strtotime('+1 month', $current);
        }

        foreach ($payments as $payment) {
            $month = date('Y-m', strtotime($payment['payment_date']));
            if (isset($result[$month])) {
                $result[$month] += $payment['amount'];
            }
        }

        return $result;
    }
}