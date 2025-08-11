<?php namespace App\Controllers;

use App\Models\BlogModel;

class BlogController extends BaseController
{
    protected $blogModel;

    public function __construct()
    {
        $this->blogModel = new BlogModel();
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        if ($this->request->isAJAX()) {
            $blogs = $this->blogModel->getBlogsWithCategory();
            return $this->response->setJSON(['data' => $blogs]);
        }

        $data = [
            'title' => 'Manage Blogs',
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/blogs/index', $data);
    }

    public function create()
    {
        $categories = $this->blogModel->getBlogCategories();
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'categories' => $categories
            ]);
        }

        $data = [
            'title' => 'Add New Blog',
            'categories' => $categories,
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/blogs/create', $data);
    }

    public function store()
    {
        $response = ['success' => false, 'message' => 'Failed to add blog'];
        
        $rules = [
            'title' => 'required|min_length[5]|max_length[255]',
            'content' => 'required',
            'category_id' => 'required|numeric',
            'status' => 'required|in_list[draft,published,archived]'
        ];

        if (!$this->validate($rules)) {
            $response['errors'] = $this->validator->getErrors();
            return $this->response->setJSON($response);
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'slug' => url_title($this->request->getPost('title'), '-', true),
            'content' => $this->request->getPost('content'),
            'excerpt' => $this->request->getPost('excerpt'),
            'author_id' => $this->session->get('user_id'),
            'category_id' => $this->request->getPost('category_id'),
            'status' => $this->request->getPost('status'),
            'published_at' => $this->request->getPost('status') == 'published' ? date('Y-m-d H:i:s') : null
        ];

        if ($this->blogModel->insert($data)) {
            $response = [
                'success' => true,
                'message' => 'Blog added successfully',
                'redirect' => base_url('admin/blogs')
            ];
        }

        return $this->response->setJSON($response);
    }

    public function edit($id)
    {
        $blog = $this->blogModel->find($id);
        if (!$blog) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['error' => 'Blog not found'])->setStatusCode(404);
            }
            return redirect()->to('/admin/blogs')->with('error', 'Blog not found');
        }

        $categories = $this->blogModel->getBlogCategories();
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'blog' => $blog,
                'categories' => $categories
            ]);
        }

        $data = [
            'title' => 'Edit Blog',
            'blog' => $blog,
            'categories' => $categories,
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/blogs/edit', $data);
    }

    public function update($id)
    {
        $response = ['success' => false, 'message' => 'Failed to update blog'];
        
        $blog = $this->blogModel->find($id);
        if (!$blog) {
            $response['message'] = 'Blog not found';
            return $this->response->setJSON($response)->setStatusCode(404);
        }

        $rules = [
            'title' => 'required|min_length[5]|max_length[255]',
            'content' => 'required',
            'category_id' => 'required|numeric',
            'status' => 'required|in_list[draft,published,archived]'
        ];

        if (!$this->validate($rules)) {
            $response['errors'] = $this->validator->getErrors();
            return $this->response->setJSON($response);
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'slug' => url_title($this->request->getPost('title'), '-', true),
            'content' => $this->request->getPost('content'),
            'excerpt' => $this->request->getPost('excerpt'),
            'category_id' => $this->request->getPost('category_id'),
            'status' => $this->request->getPost('status'),
            'published_at' => $this->request->getPost('status') == 'published' ? date('Y-m-d H:i:s') : null
        ];

        if ($this->blogModel->update($id, $data)) {
            $response = [
                'success' => true,
                'message' => 'Blog updated successfully',
                'redirect' => base_url('admin/blogs')
            ];
        }

        return $this->response->setJSON($response);
    }

    public function delete($id)
    {
        $response = ['success' => false, 'message' => 'Failed to delete blog'];
        
        if ($this->blogModel->delete($id)) {
            $response = [
                'success' => true,
                'message' => 'Blog deleted successfully'
            ];
        }

        return $this->response->setJSON($response);
    }
}