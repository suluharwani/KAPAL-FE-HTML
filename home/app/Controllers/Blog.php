<?php
namespace App\Controllers;

use App\Models\BlogModel;
use App\Models\BlogCategoryModel;

class Blog extends BaseController
{
    protected $blogModel;
    protected $blogCategoryModel;
    
    public function __construct()
    {
        $this->blogModel = new BlogModel();
        $this->blogCategoryModel = new BlogCategoryModel();
    }
    
    // Blog index page - list all posts
    public function index()
    {
        $data = [
            'title' => 'Blog - Raja Ampat Boat Services',
            'blogs' => $this->blogModel->getPublishedBlogs(9),
            'categories' => $this->blogCategoryModel->getCategoriesWithCount(),
            'recentPosts' => $this->blogModel->getRecentPosts(5),
            'pager' => $this->blogModel->pager
        ];
        return $this->render('blog/index', $data);
    }
    
    // Single blog post
    public function post($slug)
    {
        $blog = $this->blogModel->getBlogBySlug($slug);
        
        if (!$blog) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Blog post not found');
        }
        
        $data = [
            'title' => $blog['title'] . ' - Raja Ampat Boat Services',
            'blog' => $blog,
            'categories' => $this->blogCategoryModel->getCategoriesWithCount(),
            'recentPosts' => $this->blogModel->getRecentPosts(5)
        ];
        
        return $this->render('blog/single', $data);
    }
    
    // Blog posts by category
    public function category($slug)
    {
        $category = $this->blogCategoryModel->getCategoryBySlug($slug);
        
        if (!$category) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Category not found');
        }
        
        $blogs = $this->blogModel->getBlogsByCategory($category['category_id'], 9);
        
        $data = [
            'title' => $category['category_name'] . ' - Blog Category - Raja Ampat Boat Services',
            'blogs' => $blogs,
            'category' => $category,
            'categories' => $this->blogCategoryModel->getCategoriesWithCount(),
            'recentPosts' => $this->blogModel->getRecentPosts(5)
        ];
        
       return $this->render('blog/category', $data);
    }
    
}