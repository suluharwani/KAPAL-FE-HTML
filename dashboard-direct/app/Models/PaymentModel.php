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

    public function getPaymentsWithDetails($perPage = 10)
    {
        return $this->select('payments.*, bookings.booking_code, users.full_name as customer_name')
            ->join('bookings', 'bookings.booking_id = payments.booking_id')
            ->join('users', 'users.user_id = bookings.user_id')
            ->orderBy('payments.created_at', 'DESC')
            ->paginate($perPage);
    }
}