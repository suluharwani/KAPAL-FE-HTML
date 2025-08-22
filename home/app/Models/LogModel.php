<?php namespace App\Models;

use CodeIgniter\Model;

class LogModel extends Model
{
    protected $table = 'activity_logs';
    protected $primaryKey = 'log_id';
    protected $allowedFields = [
        'user_id',
        'action',
        'details',
        'ip_address',
        'user_agent'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}