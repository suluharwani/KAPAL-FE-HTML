<?php
namespace App\Models;

use CodeIgniter\Model;

class BlogCategoryModel extends Model
{
    protected $table = 'blog_categories';
    protected $primaryKey = 'category_id';
    protected $allowedFields = ['category_name', 'slug', 'description'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    
    // Get all categories with post count
    public function getCategoriesWithCount()
    {
        $builder = $this->db->table('blog_categories c');
        $builder->select('c.*, COUNT(b.blog_id) as post_count');
        $builder->join('blogs b', 'c.category_id = b.category_id AND b.status = "published"', 'left');
        $builder->groupBy('c.category_id');
        $builder->orderBy('c.category_name', 'ASC');
        
        return $builder->get()->getResultArray();
    }
    
    // Get category by slug
    public function getCategoryBySlug($slug)
    {
        return $this->where('slug', $slug)->first();
    }
}