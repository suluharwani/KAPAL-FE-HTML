<?php namespace App\Controllers\Api;

use App\Models\BlogModel;
use App\Models\BlogCategoryModel;

class Blogs extends BaseApiController
{
    protected $modelName = BlogModel::class;

    public function __construct()
    {
        $this->model = new BlogModel();
        $this->categoryModel = new BlogCategoryModel();
    }

    public function index()
    {
        $params = $this->getPaginationParams();
        $blogs = $this->model->getPaginated($params);

        return $this->respond([
            'status' => 200,
            'data' => $blogs['data'],
            'pagination' => $blogs['pagination']
        ]);
    }

    public function show($id = null)
    {
        $blog = $this->model->find($id);

        if (!$blog) {
            return $this->respondNotFound('Blog post not found');
        }

        return $this->respond([
            'status' => 200,
            'data' => $blog
        ]);
    }

    public function create()
    {
        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'content' => 'required',
            'author_id' => 'required|integer',
            'status' => 'permit_empty|in_list[draft,published,archived]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'title' => $this->request->getVar('title'),
            'slug' => url_title($this->request->getVar('title'), '-', true),
            'content' => $this->request->getVar('content'),
            'excerpt' => $this->request->getVar('excerpt'),
            'author_id' => $this->request->getVar('author_id'),
            'category_id' => $this->request->getVar('category_id'),
            'status' => $this->request->getVar('status') ?? 'draft'
        ];

        // Handle featured image upload
        if ($image = $this->request->getFile('featured_image')) {
            if ($image->isValid() && !$image->hasMoved()) {
                $newName = $image->getRandomName();
                $image->move(ROOTPATH . 'public/uploads/blogs', $newName);
                $data['featured_image'] = 'uploads/blogs/' . $newName;
            }
        }

        // Set published_at if status is published
        if ($data['status'] === 'published') {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        $blogId = $this->model->insert($data);

        if ($blogId) {
            return $this->respondCreated(['blog_id' => $blogId]);
        } else {
            return $this->failServerError('Failed to create blog post');
        }
    }

    // Implement update, delete, and other methods similarly
}