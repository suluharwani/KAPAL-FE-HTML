<?php namespace App\Models;

use CodeIgniter\Model;

class BoatModel extends Model
{
    protected $table = 'boats';
    protected $primaryKey = 'boat_id';
    protected $allowedFields = [
        'boat_name', 'boat_type', 'capacity', 'description', 
        'price_per_trip', 'image_url', 'facilities'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}