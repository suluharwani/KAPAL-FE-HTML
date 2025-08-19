<?php namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'payment_id';
    protected $allowedFields = [
        'booking_id', 
        'amount', 
        'payment_date', 
        'payment_method',
        'bank_name',
        'account_number',
        'receipt_image',
        'status',
        'notes'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}