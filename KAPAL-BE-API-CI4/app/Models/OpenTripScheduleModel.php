<?php namespace App\Models;

use CodeIgniter\Model;

class OpenTripScheduleModel extends Model
{
    protected $table = 'open_trip_schedules';
    protected $primaryKey = 'open_trip_id';
    protected $allowedFields = [
        'request_id', 'schedule_id', 'reserved_seats',
        'available_seats', 'status' // upcoming, ongoing, completed
    ];
    protected $useTimestamps = true;
}