<?php namespace App\Models;

use CodeIgniter\Model;

class GalleryModel extends Model
{
    protected $table = 'gallery';
    protected $primaryKey = 'gallery_id';
    protected $allowedFields = [
        'title', 'image_url', 'thumbnail_url', 'category', 'description', 'is_featured'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    public function getGalleryByCategory($category = null, $isFeatured = false)
    {
        $builder = $this->orderBy('created_at', 'DESC');

        if ($category) {
            $builder->where('category', $category);
        }

        if ($isFeatured) {
            $builder->where('is_featured', 1);
        }

        return $builder->findAll();
    }

    public function createThumbnail($sourcePath, $destinationPath, $width = 300, $height = 200)
    {
        $image = \Config\Services::image()
            ->withFile($sourcePath)
            ->fit($width, $height, 'center')
            ->save($destinationPath);

        return $image;
    }
}