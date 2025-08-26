<?php
namespace App\Models;

use CodeIgniter\Model;

class TourPackageModel extends Model
{
    protected $table = 'tour_packages';
    protected $primaryKey = 'package_id';
    protected $allowedFields = [
        'package_name', 'slug', 'description', 'duration', 
        'price', 'image_url', 'island_slug', 'inclusions',
        'exclusions', 'itinerary', 'is_active'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    // Get all active packages
    public function getActivePackages()
    {
        return $this->where('is_active', 1)
                    ->orderBy('package_name', 'ASC')
                    ->findAll();
    }
    
    // Get packages by island
    public function getPackagesByIsland($island_slug)
    {
        return $this->where('island_slug', $island_slug)
                    ->where('is_active', 1)
                    ->orderBy('price', 'ASC')
                    ->findAll();
    }
    
    // Get featured packages
    public function getFeaturedPackages($limit = 6)
    {
        return $this->where('is_active', 1)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
    
    // Get package by slug
    public function getPackageBySlug($slug)
    {
        return $this->where('slug', $slug)
                    ->where('is_active', 1)
                    ->first();
    }
    
    // Search packages
    public function searchPackages($keyword)
    {
        return $this->like('package_name', $keyword)
                    ->orLike('description', $keyword)
                    ->orLike('island_slug', $keyword)
                    ->where('is_active', 1)
                    ->findAll();
    }
}