<?php namespace App\Models;

use CodeIgniter\Model;

class GalleryModel extends Model
{
    protected $table = 'gallery';
    protected $primaryKey = 'gallery_id';
    protected $allowedFields = ['title', 'image_url', 'thumbnail_url', 'category', 'description', 'is_featured'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    
    public function getGalleryItems($category = null, $featured = false)
    {
        $builder = $this;
        if ($category) {
            $builder->where('category', $category);
        }
        if ($featured) {
            $builder->where('is_featured', 1);
        }
        return $builder->orderBy('created_at', 'DESC')->findAll();
    }
}