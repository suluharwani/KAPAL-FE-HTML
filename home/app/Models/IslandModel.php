<?php
namespace App\Models;

use CodeIgniter\Model;

class IslandModel extends Model
{
    protected $table = 'islands';
    protected $primaryKey = 'island_id';
    protected $allowedFields = [
        'island_name', 'description', 'image_url', 'slug'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    // Get all active islands
    public function getActiveIslands()
    {
        return $this->orderBy('island_name', 'ASC')->findAll();
    }
    
    // Get island by slug
    public function getIslandBySlug($slug)
    {
        return $this->where('slug', $slug)->first();
    }
    
    // Get popular islands
    public function getPopularIslands($limit = 4)
    {
        return $this->orderBy('island_name', 'ASC')->limit($limit)->findAll();
    }
}