<?php namespace App\Models;

use CodeIgniter\Model;

class IslandModel extends Model
{
    protected $table = 'islands';
    protected $primaryKey = 'island_id';
    protected $allowedFields = ['island_name', 'description', 'image_url'];
    protected $returnType = 'array';
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}