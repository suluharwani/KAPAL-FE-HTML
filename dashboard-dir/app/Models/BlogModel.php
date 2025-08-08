<?php namespace App\Models;

use CodeIgniter\Model;

class BlogModel extends Model
{
    protected $table = 'blogs';
    protected $primaryKey = 'blog_id';
    protected $allowedFields = [
        'title', 'slug', 'content', 'excerpt', 'featured_image', 
        'author_id', 'category_id', 'status', 'published_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    public function getBlogsWithCategory()
    {
        return $this->select('blogs.*, blog_categories.category_name')
                   ->join('blog_categories', 'blog_categories.category_id = blogs.category_id', 'left')
                   ->findAll();
    }
        public function getBlogCategories()
    {
        return $this->db->table('blog_categories')->get()->getResultArray();
    }
}