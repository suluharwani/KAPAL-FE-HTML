<?php namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table = 'activity_logs';
    protected $primaryKey = 'log_id';
    protected $allowedFields = [
        'user_id', 'action', 'entity_type', 
        'entity_id', 'details', 'ip_address'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    public function logActivity($userId, $action, $entityType, $entityId = null, $details = null)
    {
        $data = [
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'details' => json_encode($details),
            'ip_address' => $this->request->getIPAddress()
        ];

        return $this->insert($data);
    }
}