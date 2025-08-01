<?php namespace App\Models;

use CodeIgniter\Model;

class RequestOpenTripModel extends Model
{
    protected $table = 'request_open_trips';
    protected $primaryKey = 'request_id';
    protected $allowedFields = [
        'user_id', 'boat_id', 'route_id', 
        'proposed_date', 'proposed_time', 
        'min_passengers', 'max_passengers',
        'notes', 'status' // pending, approved, rejected
    ];
    protected $useTimestamps = true;
}