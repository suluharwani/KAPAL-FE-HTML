<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
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

    public function index()
    {
        $status = $this->request->getGet('status') ?? null;
        
        $data = [
            'blogs' => $this->blogModel->getBlogsWithCategory($status),
            'statusFilter' => $status
        ];

        return view('admin/blog/index', $data);
    }

    public function add()
    {
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'title' => 'required|min_length[5]|max_length[255]',
                'content' => 'required|min_length[100]',
                'category_id' => 'permit_empty|numeric',
                'status' => 'required|in_list[draft,published,archived]',
                'featured_image' => 'max_size[featured_image,5120]|is_image[featured_image]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $slug = $this->blogModel->generateSlug($this->request->getPost('title'));

            $data = [
                'title' => $this->request->getPost('title'),
                'slug' => $slug,
                'content' => $this->request->getPost('content'),
                'excerpt' => substr(strip_tags($this->request->getPost('content')), 0, 200),
                'author_id' => session()->get('user_id'),
                'category_id' => $this->request->getPost('category_id'),
                'status' => $this->request->getPost('status'),
                'published_at' => $this->request->getPost('status') === 'published' ? date('Y-m-d H:i:s') : null
            ];

            // Handle featured image upload
            $image = $this->request->getFile('featured_image');
            if ($image && $image->isValid() && !$image->hasMoved()) {
                $imageName = $image->getRandomName();
                $image->move(ROOTPATH . 'public/assets/img/blog', $imageName);
                $data['featured_image'] = 'assets/img/blog/' . $imageName;
            }

            if ($this->blogModel->save($data)) {
                return redirect()->to('/admin/blog')->with('success', 'Blog post saved');
            } else {
                // Clean up uploaded image if failed to save
                if (isset($data['featured_image'])) {
                    unlink(ROOTPATH . 'public/' . $data['featured_image']);
                }
                return redirect()->back()->with('error', 'Failed to save blog post');
            }
        }

        $data = [
            'categories' => $this->blogCategoryModel->findAll()
        ];

        return view('admin/blog/add', $data);
    }

    public function edit($id)
    {
        $blog = $this->blogModel->find($id);
        if (!$blog) {
            return redirect()->to('/admin/blog')->with('error', 'Blog post not found');
        }

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'title' => 'required|min_length[5]|max_length[255]',
                'content' => 'required|min_length[100]',
                'category_id' => 'permit_empty|numeric',
                'status' => 'required|in_list[draft,published,archived]',
                'featured_image' => 'max_size[featured_image,5120]|is_image[featured_image]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $data = [
                'blog_id' => $id,
                'title' => $this->request->getPost('title'),
                'content' => $this->request->getPost('content'),
                'excerpt' => substr(strip_tags($this->request->getPost('content')), 0, 200),
                'category_id' => $this->request->getPost('category_id'),
                'status' => $this->request->getPost('status'),
                'published_at' => $this->request->getPost('status') === 'published' ? date('Y-m-d H:i:s') : null
            ];

            // Handle featured image upload
            $image = $this->request->getFile('featured_image');
            if ($image && $image->isValid() && !$image->hasMoved()) {
                // Delete old image if exists
                if ($blog['featured_image'] && file_exists(ROOTPATH . 'public/' . $blog['featured_image'])) {
                    unlink(ROOTPATH . 'public/' . $blog['featured_image']);
                }

                $imageName = $image->getRandomName();
                $image->move(ROOTPATH . 'public/assets/img/blog', $imageName);
                $data['featured_image'] = 'assets/img/blog/' . $imageName;
            }

            if ($this->blogModel->save($data)) {
                return redirect()->to('/admin/blog')->with('success', 'Blog post updated');
            } else {
                return redirect()->back()->with('error', 'Failed to update blog post');
            }
        }

        $data = [
            'blog' => $blog,
            'categories' => $this->blogCategoryModel->findAll()
        ];

        return view('admin/blog/edit', $data);
    }

    public function delete($id)
    {
        $blog = $this->blogModel->find($id);
        if (!$blog) {
            return redirect()->to('/admin/blog')->with('error', 'Blog post not found');
        }

        // Delete featured image if exists
        if ($blog['featured_image'] && file_exists(ROOTPATH . 'public/' . $blog['featured_image'])) {
            unlink(ROOTPATH . 'public/' . $blog['featured_image']);
        }

        if ($this->blogModel->delete($id)) {
            return redirect()->to('/admin/blog')->with('success', 'Blog post deleted');
        } else {
            return redirect()->to('/admin/blog')->with('error', 'Failed to delete blog post');
        }
    }
}