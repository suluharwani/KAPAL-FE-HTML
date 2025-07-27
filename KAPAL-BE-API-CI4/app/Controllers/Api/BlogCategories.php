<?php namespace App\Controllers\Api;

use App\Models\BlogCategoryModel;
use App\Models\BlogModel;

class BlogCategories extends BaseApiController
{
    protected $modelName = BlogCategoryModel::class;

    public function __construct()
    {
        $this->model = new BlogCategoryModel();
        $this->blogModel = new BlogModel();
    }

    public function index()
    {
        $categories = $this->model->getCategoriesWithCount();

        return $this->respond([
            'status' => 200,
            'data' => $categories
        ]);
    }

    public function show($id = null)
    {
        $category = $this->model->find($id);
        if (!$category) {
            return $this->respondNotFound('Category not found');
        }

        // Get posts in this category
        $posts = $this->blogModel->where('category_id', $id)
                               ->where('status', 'published')
                               ->orderBy('published_at', 'desc')
                               ->findAll();

        $category['posts'] = $posts;

        return $this->respond([
            'status' => 200,
            'data' => $category
        ]);
    }

    public function create()
    {
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only admin can create blog categories');
        }

        $rules = [
            'category_name' => 'required|min_length[3]|max_length[100]|is_unique[blog_categories.category_name]',
            'description' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'category_name' => $this->request->getVar('category_name'),
            'slug' => url_title($this->request->getVar('category_name'), '-', true),
            'description' => $this->request->getVar('description')
        ];

        $categoryId = $this->model->insert($data);

        if ($categoryId) {
            return $this->respondCreated(['category_id' => $categoryId]);
        } else {
            return $this->failServerError('Failed to create category');
        }
    }

    public function update($id = null)
    {
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only admin can update blog categories');
        }

        $category = $this->model->find($id);
        if (!$category) {
            return $this->respondNotFound('Category not found');
        }

        $rules = [
            'category_name' => 'permit_empty|min_length[3]|max_length[100]',
            'description' => 'permit_empty'
        ];

        if ($this->request->getVar('category_name') && 
            $this->request->getVar('category_name') !== $category['category_name']) {
            $rules['category_name'] .= '|is_unique[blog_categories.category_name]';
        }

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'category_name' => $this->request->getVar('category_name') ?? $category['category_name'],
            'description' => $this->request->getVar('description') ?? $category['description']
        ];

        // Update slug if name changed
        if (isset($data['category_name']) && $data['category_name'] !== $category['category_name']) {
            $data['slug'] = url_title($data['category_name'], '-', true);
        }

        if ($this->model->update($id, $data)) {
            return $this->respondUpdated(['category_id' => $id]);
        } else {
            return $this->failServerError('Failed to update category');
        }
    }

    public function delete($id = null)
    {
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only admin can delete blog categories');
        }

        $category = $this->model->find($id);
        if (!$category) {
            return $this->respondNotFound('Category not found');
        }

        // Check if category has posts
        $postCount = $this->blogModel->where('category_id', $id)->countAllResults();
        if ($postCount > 0) {
            return $this->fail('Cannot delete category with posts', 400);
        }

        if ($this->model->delete($id)) {
            return $this->respondDeleted();
        } else {
            return $this->failServerError('Failed to delete category');
        }
    }
}