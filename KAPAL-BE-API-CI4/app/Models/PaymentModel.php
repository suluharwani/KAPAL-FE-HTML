<?php namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'payment_id';
    protected $allowedFields = [
        'booking_id', 'amount', 'payment_date', 'payment_method',
        'bank_name', 'account_number', 'receipt_image', 'status', 'notes'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getPaginated(array $params, $userId = null)
    {
        $builder = $this->builder();
        $builder->select('payments.*, bookings.booking_code, bookings.total_price, 
            users.full_name as customer_name')
            ->join('bookings', 'bookings.booking_id = payments.booking_id')
            ->join('users', 'users.user_id = bookings.user_id');

        // Filter by user if not admin
        if ($userId && $this->request->user->role !== 'admin') {
            $builder->where('bookings.user_id', $userId);
        }

        // Search
        if (!empty($params['search'])) {
            $builder->groupStart()
                ->like('bookings.booking_code', $params['search'])
                ->orLike('users.full_name', $params['search'])
                ->orLike('payments.bank_name', $params['search'])
                ->groupEnd();
        }

        // Filter by status
        if (!empty($params['status'])) {
            $builder->where('payments.status', $params['status']);
        }

        // Filter by payment method
        if (!empty($params['method'])) {
            $builder->where('payments.payment_method', $params['method']);
        }

        // Sort
        if (!empty($params['sort'])) {
            $builder->orderBy($params['sort'], $params['order']);
        } else {
            $builder->orderBy('payments.created_at', 'desc');
        }

        // Pagination
        $total = $builder->countAllResults(false);
        $page = $params['page'];
        $perPage = $params['per_page'];
        $offset = ($page - 1) * $perPage;

        $data = $builder->get($perPage, $offset)->getResultArray();

        return [
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($total / $perPage)
            ]
        ];
    }
}