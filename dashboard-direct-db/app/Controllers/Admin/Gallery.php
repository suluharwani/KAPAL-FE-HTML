<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GalleryModel;

class Gallery extends BaseController
{
    protected $galleryModel;

    public function __construct()
    {
        $this->galleryModel = new GalleryModel();
    }

    public function index()
    {
        $category = $this->request->getGet('category') ?? null;
        
        $data = [
            'gallery' => $this->galleryModel->getGalleryByCategory($category),
            'categoryFilter' => $category
        ];

        return view('admin/gallery/index', $data);
    }

    public function add()
    {
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'title' => 'required|min_length[3]|max_length[255]',
                'category' => 'required|in_list[kapal,wisata,penumpang,pulau]',
                'description' => 'permit_empty',
                'image' => 'uploaded[image]|max_size[image,5120]|is_image[image]',
                'is_featured' => 'permit_empty'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $image = $this->request->getFile('image');
            $imageName = $image->getRandomName();
            $image->move(ROOTPATH . 'public/assets/img/gallery', $imageName);

            // Create thumbnail
            $thumbnailName = 'thumb_' . $imageName;
            $this->galleryModel->createThumbnail(
                ROOTPATH . 'public/assets/img/gallery/' . $imageName,
                ROOTPATH . 'public/assets/img/gallery/thumbs/' . $thumbnailName
            );

            $data = [
                'title' => $this->request->getPost('title'),
                'category' => $this->request->getPost('category'),
                'description' => $this->request->getPost('description'),
                'image_url' => 'assets/img/gallery/' . $imageName,
                'thumbnail_url' => 'assets/img/gallery/thumbs/' . $thumbnailName,
                'is_featured' => $this->request->getPost('is_featured') ? 1 : 0
            ];

            if ($this->galleryModel->save($data)) {
                return redirect()->to('/admin/gallery')->with('success', 'Image added to gallery');
            } else {
                // Clean up uploaded files if failed to save
                unlink(ROOTPATH . 'public/assets/img/gallery/' . $imageName);
                unlink(ROOTPATH . 'public/assets/img/gallery/thumbs/' . $thumbnailName);
                return redirect()->back()->with('error', 'Failed to add image to gallery');
            }
        }

        return view('admin/gallery/add');
    }

    public function toggleFeatured($id)
    {
        $image = $this->galleryModel->find($id);
        if (!$image) {
            return redirect()->back()->with('error', 'Image not found');
        }

        $newStatus = $image['is_featured'] ? 0 : 1;
        $this->galleryModel->update($id, ['is_featured' => $newStatus]);

        return redirect()->back()->with('success', 'Featured status updated');
    }

    public function delete($id)
    {
        $image = $this->galleryModel->find($id);
        if (!$image) {
            return redirect()->back()->with('error', 'Image not found');
        }

        // Delete files
        if (file_exists(ROOTPATH . 'public/' . $image['image_url'])) {
            unlink(ROOTPATH . 'public/' . $image['image_url']);
        }
        if (file_exists(ROOTPATH . 'public/' . $image['thumbnail_url'])) {
            unlink(ROOTPATH . 'public/' . $image['thumbnail_url']);
        }

        if ($this->galleryModel->delete($id)) {
            return redirect()->to('/admin/gallery')->with('success', 'Image deleted from gallery');
        } else {
            return redirect()->to('/admin/gallery')->with('error', 'Failed to delete image');
        }
    }
}