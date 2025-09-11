<?php namespace App\Controllers;

use App\Models\BlogModel;
use CodeIgniter\Files\File;

class BlogController extends BaseController
{
    protected $blogModel;

    public function __construct()
    {
        $this->blogModel = new BlogModel();
        $this->session = \Config\Services::session();
        helper(['form', 'url', 'text']);
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
            'status' => 'required|in_list[draft,published,archived]',
            'featured_image' => 'uploaded[featured_image]|max_size[featured_image,2048]|is_image[featured_image]|mime_in[featured_image,image/jpg,image/jpeg,image/png]'
        ];

        if (!$this->validate($rules)) {
            $response['errors'] = $this->validator->getErrors();
            return $this->response->setJSON($response);
        }

        // Upload and process image
        $imagePaths = $this->processImage($this->request->getFile('featured_image'));
        if (!$imagePaths) {
            $response['message'] = 'Failed to process image';
            return $this->response->setJSON($response);
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'slug' => url_title($this->request->getPost('title'), '-', true),
            'content' => $this->request->getPost('content'),
            'excerpt' => $this->request->getPost('excerpt'),
            'featured_image' => $imagePaths['original'],
            'thumbnail_image' => $imagePaths['resized'],
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

        // Add image validation only if a new image is uploaded
        if ($this->request->getFile('featured_image')->isValid()) {
            $rules['featured_image'] = 'uploaded[featured_image]|max_size[featured_image,2048]|is_image[featured_image]|mime_in[featured_image,image/jpg,image/jpeg,image/png]';
        }

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

        // Process new image if uploaded
        if ($this->request->getFile('featured_image')->isValid()) {
            $imagePaths = $this->processImage($this->request->getFile('featured_image'));
            if ($imagePaths) {
                // Delete old images
                $this->deleteImages($blog);
                
                $data['featured_image'] = $imagePaths['original'];
                $data['thumbnail_image'] = $imagePaths['resized'];
            }
        }

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
        
        $blog = $this->blogModel->find($id);
        if (!$blog) {
            $response['message'] = 'Blog not found';
            return $this->response->setJSON($response)->setStatusCode(404);
        }

        // Delete associated images
        $this->deleteImages($blog);

        if ($this->blogModel->delete($id)) {
            $response = [
                'success' => true,
                'message' => 'Blog deleted successfully'
            ];
        }

        return $this->response->setJSON($response);
    }

    /**
     * Process uploaded image - save original and create resized version
     */
    private function processImage($imageFile)
    {
        if (!$imageFile->isValid()) {
            return false;
        }

        $uploadPath = ROOTPATH . 'public/uploads/blogs/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Generate unique filename
        $newName = $imageFile->getRandomName();
        $originalPath = $uploadPath . 'original_' . $newName;
        $resizedPath = $uploadPath . 'resized_' . $newName;

        // Move original file
        $imageFile->move($uploadPath, 'original_' . $newName);

        // Resize image to 800px width
        $this->resizeImage($uploadPath . 'original_' . $newName, $resizedPath, 800);

        return [
            'original' => 'uploads/blogs/original_' . $newName,
            'resized' => 'uploads/blogs/resized_' . $newName
        ];
    }

    /**
     * Resize image to specified width while maintaining aspect ratio
     */
    private function resizeImage($sourcePath, $targetPath, $maxWidth)
    {
        $image = \Config\Services::image();
        
        try {
            $image->withFile($sourcePath)
                  ->resize($maxWidth, $maxWidth, true, 'width')
                  ->save($targetPath);
            
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Image resize failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete associated images
     */
    private function deleteImages($blog)
    {
        if (!empty($blog['featured_image']) && file_exists(ROOTPATH . 'public/' . $blog['featured_image'])) {
            unlink(ROOTPATH . 'public/' . $blog['featured_image']);
        }
        
        if (!empty($blog['thumbnail_image']) && file_exists(ROOTPATH . 'public/' . $blog['thumbnail_image'])) {
            unlink(ROOTPATH . 'public/' . $blog['thumbnail_image']);
        }
    }
}