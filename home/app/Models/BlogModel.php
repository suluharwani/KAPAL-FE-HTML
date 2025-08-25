<?php
namespace App\Models;

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
    
    // Get all published blogs with category and author info
    public function getPublishedBlogs($limit = null, $offset = 0)
    {
        $builder = $this->db->table('blogs b');
        $builder->select('b.*, c.category_name, u.full_name as author_name');
        $builder->join('blog_categories c', 'b.category_id = c.category_id', 'left');
        $builder->join('users u', 'b.author_id = u.user_id', 'left');
        $builder->where('b.status', 'published');
        $builder->where('b.published_at <=', date('Y-m-d H:i:s'));
        $builder->orderBy('b.published_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit, $offset);
        }
        
        return $builder->get()->getResultArray();
    }
    
    // Get blog by slug
// Get blog by slug
public function getBlogBySlug($slug)
{
    $builder = $this->db->table('blogs b');
    $builder->select('b.*, c.category_name, c.slug as category_slug, u.full_name as author_name');
    $builder->join('blog_categories c', 'b.category_id = c.category_id', 'left');
    $builder->join('users u', 'b.author_id = u.user_id', 'left');
    $builder->where('b.slug', $slug);
    $builder->where('b.status', 'published');
    $builder->where('b.published_at <=', date('Y-m-d H:i:s'));
    
    return $builder->get()->getRowArray();
}
    
    // Get blogs by category
    public function getBlogsByCategory($category_id, $limit = null, $offset = 0)
    {
        $builder = $this->db->table('blogs b');
        $builder->select('b.*, c.category_name, u.full_name as author_name');
        $builder->join('blog_categories c', 'b.category_id = c.category_id', 'left');
        $builder->join('users u', 'b.author_id = u.user_id', 'left');
        $builder->where('b.category_id', $category_id);
        $builder->where('b.status', 'published');
        $builder->where('b.published_at <=', date('Y-m-d H:i:s'));
        $builder->orderBy('b.published_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit, $offset);
        }
        
        return $builder->get()->getResultArray();
    }
    
    // Get recent posts
    public function getRecentPosts($limit = 5)
    {
        return $this->where('status', 'published')
                    ->where('published_at <=', date('Y-m-d H:i:s'))
                    ->orderBy('published_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}