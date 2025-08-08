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

    public function generateSlug($title)
    {
        $slug = url_title($title, '-', true);
        $count = $this->like('slug', $slug)->countAllResults();
        
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }

        return $slug;
    }

    public function getBlogsWithCategory($status = null)
    {
        $builder = $this->select('blogs.*, 
                                users.full_name as author_name,
                                blog_categories.category_name')
            ->join('users', 'users.user_id = blogs.author_id')
            ->join('blog_categories', 'blog_categories.category_id = blogs.category_id', 'left')
            ->orderBy('blogs.created_at', 'DESC');

        if ($status) {
            $builder->where('blogs.status', $status);
        }

        return $builder->findAll();
    }

    public function getPublishedBlogs($limit = null)
    {
        $builder = $this->where('status', 'published')
            ->where('published_at <=', date('Y-m-d H:i:s'))
            ->orderBy('published_at', 'DESC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }
}