<?php namespace App\Controllers;

use App\Models\GalleryModel;

class GalleryController extends BaseController
{
    protected $galleryModel;

    public function __construct()
    {
        $this->galleryModel = new GalleryModel();
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        $category = $this->request->getGet('category');
        
        $data = [
            'title' => 'Gallery Management',
            'category' => $category,
            'gallery' => $this->galleryModel->getGalleryItems($category),
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/gallery/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Add Gallery Item',
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/gallery/create', $data);
    }

    public function store()
    {
        $rules = [
            'title' => 'required|max_length[255]',
            'category' => 'required|in_list[kapal,wisata,penumpang,pulau]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle file upload
        $imageFile = $this->request->getFile('image');
        $thumbnailFile = $this->request->getFile('thumbnail');

        if ($imageFile->isValid() && !$imageFile->hasMoved()) {
            $newName = $imageFile->getRandomName();
            $imageFile->move(ROOTPATH . 'public/uploads/gallery', $newName);
            $imageUrl = 'uploads/gallery/' . $newName;
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to upload image');
        }

        if ($thumbnailFile->isValid() && !$thumbnailFile->hasMoved()) {
            $newName = $thumbnailFile->getRandomName();
            $thumbnailFile->move(ROOTPATH . 'public/uploads/gallery/thumbnails', $newName);
            $thumbnailUrl = 'uploads/gallery/thumbnails/' . $newName;
        } else {
            $thumbnailUrl = $imageUrl; // Use same image if thumbnail not provided
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'image_url' => $imageUrl,
            'thumbnail_url' => $thumbnailUrl,
            'category' => $this->request->getPost('category'),
            'description' => $this->request->getPost('description'),
            'is_featured' => $this->request->getPost('is_featured') ? 1 : 0
        ];

        if ($this->galleryModel->insert($data)) {
            return redirect()->to('/admin/gallery')->with('success', 'Gallery item added successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to add gallery item');
        }
    }

    public function delete($id)
    {
        $item = $this->galleryModel->find($id);
        if (!$item) {
            return redirect()->to('/admin/gallery')->with('error', 'Item not found');
        }

        // Delete files
        if (file_exists(ROOTPATH . 'public/' . $item['image_url'])) {
            unlink(ROOTPATH . 'public/' . $item['image_url']);
        }
        if (file_exists(ROOTPATH . 'public/' . $item['thumbnail_url']) && $item['thumbnail_url'] != $item['image_url']) {
            unlink(ROOTPATH . 'public/' . $item['thumbnail_url']);
        }

        if ($this->galleryModel->delete($id)) {
            return redirect()->to('/admin/gallery')->with('success', 'Item deleted successfully');
        } else {
            return redirect()->to('/admin/gallery')->with('error', 'Failed to delete item');
        }
    }
}