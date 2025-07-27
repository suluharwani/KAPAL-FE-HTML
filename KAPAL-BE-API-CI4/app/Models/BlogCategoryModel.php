<?php namespace App\Models;

use CodeIgniter\Model;

class BlogCategoryModel extends Model
{
    protected $table = 'blog_categories';
    protected $primaryKey = 'category_id';
    protected $allowedFields = [
        'category_name', 'slug', 'description'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    public function getCategoriesWithCount()
    {
        return $this->db->table('blog_categories')
            ->select('blog_categories.*, COUNT(blogs.blog_id) as post_count')
            ->join('blogs', 'blogs.category_id = blog_categories.category_id', 'left')
            ->groupBy('blog_categories.category_id')
            ->get()
            ->getResultArray();
    }
}