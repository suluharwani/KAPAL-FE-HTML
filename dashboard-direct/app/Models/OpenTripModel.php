<?php namespace App\Models;

use CodeIgniter\Model;

class OpenTripModel extends Model
{
    protected $table = 'request_open_trips';
    protected $primaryKey = 'request_id';
    protected $allowedFields = [
        'user_id', 'boat_id', 'route_id', 'proposed_date', 'proposed_time',
        'min_passengers', 'max_passengers', 'notes', 'admin_notes', 'status'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getOpenTripsWithDetails($status = null)
    {
        $builder = $this->select('request_open_trips.*, 
                                boats.boat_name, boats.capacity,
                                u.full_name as requester_name, u.email as requester_email,
                                di.island_name as departure_island, 
                                ai.island_name as arrival_island')
            ->join('boats', 'boats.boat_id = request_open_trips.boat_id')
            ->join('routes r', 'r.route_id = request_open_trips.route_id')
            ->join('islands di', 'di.island_id = r.departure_island_id')
            ->join('islands ai', 'ai.island_id = r.arrival_island_id')
            ->join('users u', 'u.user_id = request_open_trips.user_id')
            ->orderBy('request_open_trips.created_at', 'DESC');

        if ($status) {
            $builder->where('request_open_trips.status', $status);
        }

        return $builder->findAll();
    }

    public function approveRequest($requestId, $adminNotes = null)
    {
        $data = [
            'request_id' => $requestId,
            'status' => 'approved',
            'admin_notes' => $adminNotes
        ];

        return $this->save($data);
    }

    public function rejectRequest($requestId, $adminNotes = null)
    {
        $data = [
            'request_id' => $requestId,
            'status' => 'rejected',
            'admin_notes' => $adminNotes
        ];

        return $this->save($data);
    }
}