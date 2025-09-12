<?php
namespace App\Controllers;

use App\Models\GalleryModel;

class Gallery extends BaseController
{
    protected $galleryModel;
    
    public function __construct()
    {
        $this->galleryModel = new GalleryModel();
    }
    
    // Gallery index page
    public function index()
    {
        $categories = ['kapal', 'wisata', 'penumpang', 'pulau'];
        $allGallery = $this->galleryModel->getAllGallery();
        
        // Group gallery by category
        $galleryByCategory = [];
        foreach ($categories as $category) {
            $galleryByCategory[$category] = $this->galleryModel->where('category', $category)->findAll();
        }
        
        $data = [
            'title' => 'Galeri - Raja Ampat Boat Services',
            'allGallery' => $allGallery,
            'galleryByCategory' => $galleryByCategory,
            'categories' => $categories,
            'featuredGallery' => $this->galleryModel->where('is_featured', 1)->findAll(12),
            'adminUrl' => $_ENV['adminUrl'] ?? base_url() // Add admin URL
        ];
        
       $this->render('gallery/index', $data);
    }
    
    // Gallery by category
    public function category($category)
    {
        $validCategories = ['kapal', 'wisata', 'penumpang', 'pulau'];
        
        if (!in_array($category, $validCategories)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Kategori galeri tidak ditemukan');
        }
        
        $gallery = $this->galleryModel->where('category', $category)->findAll();
        
        $data = [
            'title' => 'Galeri ' . ucfirst($category) . ' - Raja Ampat Boat Services',
            'gallery' => $gallery,
            'category' => $category,
            'categoryName' => $this->getCategoryName($category),
            'adminUrl' => $_ENV['adminUrl'] ?? base_url() // Add admin URL
        ];
        
       $this->render('gallery/category', $data);
    }
    

    // Get category name in Indonesian
    private function getCategoryName($category)
    {
        $categoryNames = [
            'kapal' => 'Kapal',
            'wisata' => 'Wisata',
            'penumpang' => 'Penumpang',
            'pulau' => 'Pulau'
        ];
        
        return $categoryNames[$category] ?? ucfirst($category);
    }
}