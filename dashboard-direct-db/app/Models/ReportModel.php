<?php namespace App\Models;

use CodeIgniter\Model;

class ReportModel extends Model
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function getBookingReport($startDate = null, $endDate = null)
    {
        $builder = $this->db->table('bookings b')
            ->select('b.*, u.full_name as customer_name, s.departure_date, s.departure_time, 
                     boat.boat_name, di.island_name as departure_island, ai.island_name as arrival_island')
            ->join('users u', 'u.user_id = b.user_id')
            ->join('schedules s', 's.schedule_id = b.schedule_id')
            ->join('boats boat', 'boat.boat_id = s.boat_id')
            ->join('routes r', 'r.route_id = s.route_id')
            ->join('islands di', 'di.island_id = r.departure_island_id')
            ->join('islands ai', 'ai.island_id = r.arrival_island_id')
            ->orderBy('b.created_at', 'DESC');

        if ($startDate && $endDate) {
            $builder->where('b.created_at >=', $startDate)
                   ->where('b.created_at <=', $endDate . ' 23:59:59');
        }

        return $builder->get()->getResultArray();
    }

    public function getPaymentReport($startDate = null, $endDate = null)
    {
        $builder = $this->db->table('payments p')
            ->select('p.*, b.booking_code, u.full_name as customer_name')
            ->join('bookings b', 'b.booking_id = p.booking_id')
            ->join('users u', 'u.user_id = b.user_id')
            ->orderBy('p.created_at', 'DESC');

        if ($startDate && $endDate) {
            $builder->where('p.created_at >=', $startDate)
                   ->where('p.created_at <=', $endDate . ' 23:59:59');
        }

        return $builder->get()->getResultArray();
    }

    public function getRevenueReport($startDate = null, $endDate = null)
    {
        $builder = $this->db->table('payments p')
            ->select('DATE(p.created_at) as payment_date, SUM(p.amount) as total_revenue, COUNT(p.payment_id) as payment_count')
            ->where('p.status', 'verified')
            ->groupBy('DATE(p.created_at)')
            ->orderBy('payment_date', 'ASC');

        if ($startDate && $endDate) {
            $builder->where('p.created_at >=', $startDate)
                   ->where('p.created_at <=', $endDate . ' 23:59:59');
        }

        return $builder->get()->getResultArray();
    }
}