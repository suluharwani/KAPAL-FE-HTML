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
        $data = [
            'title' => 'Manage Blogs',
            'blogs' => $this->blogModel->getBlogsWithCategory(),
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/blogs/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Add New Blog',
            'categories' => $this->blogModel->getBlogCategories(),
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/blogs/create', $data);
    }

    public function store()
    {
        $rules = [
            'title' => 'required|min_length[5]|max_length[255]',
            'content' => 'required',
            'category_id' => 'required|numeric',
            'status' => 'required|in_list[draft,published,archived]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
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
            return redirect()->to('/admin/blogs')->with('success', 'Blog added successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to add blog');
        }
    }

    public function edit($id)
    {
        $blog = $this->blogModel->find($id);
        if (!$blog) {
            return redirect()->to('/admin/blogs')->with('error', 'Blog not found');
        }

        $data = [
            'title' => 'Edit Blog',
            'blog' => $blog,
            'categories' => $this->blogModel->getBlogCategories(),
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/blogs/edit', $data);
    }

    public function update($id)
    {
        $rules = [
            'title' => 'required|min_length[5]|max_length[255]',
            'content' => 'required',
            'category_id' => 'required|numeric',
            'status' => 'required|in_list[draft,published,archived]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
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
            return redirect()->to('/admin/blogs')->with('success', 'Blog updated successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update blog');
        }
    }

    public function delete($id)
    {
        if ($this->blogModel->delete($id)) {
            return redirect()->to('/admin/blogs')->with('success', 'Blog deleted successfully');
        } else {
            return redirect()->to('/admin/blogs')->with('error', 'Failed to delete blog');
        }
    }
}