<?php namespace App\Controllers\Api;

use App\Models\GalleryModel;

class Gallery extends BaseApiController
{
    protected $modelName = GalleryModel::class;

    public function __construct()
    {
        $this->model = new GalleryModel();
    }

    public function index()
    {
        $params = $this->getPaginationParams();
        $params['category'] = $this->request->getGet('category');
        $params['featured'] = $this->request->getGet('featured');

        $gallery = $this->model->getPaginated($params);

        return $this->respond([
            'status' => 200,
            'data' => $gallery['data'],
            'pagination' => $gallery['pagination']
        ]);
    }

    public function featured()
    {
        $gallery = $this->model->getFeatured();

        return $this->respond([
            'status' => 200,
            'data' => $gallery
        ]);
    }

    public function categories()
    {
        $categories = $this->model->getCategories();

        return $this->respond([
            'status' => 200,
            'data' => $categories
        ]);
    }

    public function create()
    {
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only admin can add gallery items');
        }

        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'image' => 'uploaded[image]|max_size[image,5120]|is_image[image]',
            'category' => 'required|in_list[kapal,wisata,penumpang,pulau]',
            'description' => 'permit_empty',
            'is_featured' => 'permit_empty|in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $image = $this->request->getFile('image');
        if (!$image->isValid() || $image->hasMoved()) {
            return $this->fail('Invalid image file', 400);
        }

        // Generate thumbnail
        $imageName = $image->getRandomName();
        $thumbnailName = 'thumb_' . $imageName;

        $image->move(ROOTPATH . 'public/uploads/gallery', $imageName);

        // Create thumbnail (you'll need to install and configure Intervention Image or similar)
        $this->createThumbnail(
            ROOTPATH . 'public/uploads/gallery/' . $imageName,
            ROOTPATH . 'public/uploads/gallery/' . $thumbnailName,
            300,
            300
        );

        $data = [
            'title' => $this->request->getVar('title'),
            'image_url' => 'uploads/gallery/' . $imageName,
            'thumbnail_url' => 'uploads/gallery/' . $thumbnailName,
            'category' => $this->request->getVar('category'),
            'description' => $this->request->getVar('description'),
            'is_featured' => $this->request->getVar('is_featured') ?? 0
        ];

        $galleryId = $this->model->insert($data);

        if ($galleryId) {
            return $this->respondCreated(['gallery_id' => $galleryId]);
        } else {
            // Clean up uploaded files if failed
            unlink(ROOTPATH . 'public/uploads/gallery/' . $imageName);
            if (file_exists(ROOTPATH . 'public/uploads/gallery/' . $thumbnailName)) {
                unlink(ROOTPATH . 'public/uploads/gallery/' . $thumbnailName);
            }
            return $this->failServerError('Failed to add gallery item');
        }
    }

    public function delete($id = null)
    {
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only admin can delete gallery items');
        }

        $gallery = $this->model->find($id);
        if (!$gallery) {
            return $this->respondNotFound('Gallery item not found');
        }

        // Delete image files
        if (file_exists(ROOTPATH . 'public/' . $gallery['image_url'])) {
            unlink(ROOTPATH . 'public/' . $gallery['image_url']);
        }
        if (file_exists(ROOTPATH . 'public/' . $gallery['thumbnail_url'])) {
            unlink(ROOTPATH . 'public/' . $gallery['thumbnail_url']);
        }

        if ($this->model->delete($id)) {
            return $this->respondDeleted();
        } else {
            return $this->failServerError('Failed to delete gallery item');
        }
    }

    protected function createThumbnail($sourcePath, $targetPath, $width, $height)
    {
        // Using Intervention Image (install via composer: composer require intervention/image)
        try {
            \Config\Services::image()
                ->withFile($sourcePath)
                ->fit($width, $height)
                ->save($targetPath);
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Failed to create thumbnail: ' . $e->getMessage());
            return false;
        }
    }
}