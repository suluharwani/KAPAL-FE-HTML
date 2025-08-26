<?php
namespace App\Models;

use CodeIgniter\Model;

class GalleryModel extends Model
{
    protected $table = 'gallery';
    protected $primaryKey = 'gallery_id';
    protected $allowedFields = [
        'title', 'image_url', 'thumbnail_url', 'category',
        'description', 'is_featured'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    // Get all gallery items
    public function getAllGallery($limit = null)
    {
        $builder = $this->db->table('gallery');
        $builder->orderBy('created_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->get()->getResultArray();
    }
    
    // Get gallery by category
    public function getGalleryByCategory($category, $limit = null)
    {
        $builder = $this->db->table('gallery');
        $builder->where('category', $category);
        $builder->orderBy('created_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->get()->getResultArray();
    }
    
    // Get featured gallery items
    public function getFeaturedGallery($limit = 6)
    {
        return $this->where('is_featured', 1)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
    
    // Get gallery by multiple categories
    public function getGalleryByCategories($categories, $limit = null)
    {
        $builder = $this->db->table('gallery');
        $builder->whereIn('category', $categories);
        $builder->orderBy('created_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->get()->getResultArray();
    }
    
    // Search gallery items
    public function searchGallery($keyword, $limit = null)
    {
        $builder = $this->db->table('gallery');
        $builder->like('title', $keyword)
                ->orLike('description', $keyword)
                ->orLike('category', $keyword);
        $builder->orderBy('created_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->get()->getResultArray();
    }
}