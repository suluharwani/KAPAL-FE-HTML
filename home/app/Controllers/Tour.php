<?php
namespace App\Controllers;

use App\Models\IslandModel;
use App\Models\TourPackageModel;
use App\Models\GalleryModel;

class Tour extends BaseController
{
    protected $islandModel;
    protected $tourPackageModel;
    protected $galleryModel;
    
    public function __construct()
    {
        $this->islandModel = new IslandModel();
        $this->tourPackageModel = new TourPackageModel();
        $this->galleryModel = new GalleryModel();
    }
    
    // Waigeo Island page
    public function waigeo()
    {
        $island = $this->islandModel->where('island_name', 'Waigeo')->first();
        $packages = $this->tourPackageModel->where('island_slug', 'waigeo')->where('is_active', 1)->findAll();
        $gallery = $this->galleryModel->where('category', 'wisata')->findAll(6);
        
        $data = [
            'title' => 'Wisata Pulau Waigeo - Raja Ampat Boat Services',
            'island' => $island,
            'packages' => $packages,
            'gallery' => $gallery,
            'page' => 'waigeo'
        ];
        
        $this->render('tour/island', $data);
    }
    
    // Misool Island page
    public function misool()
    {
        $island = $this->islandModel->where('island_name', 'Misool')->first();
        $packages = $this->tourPackageModel->where('island_slug', 'misool')->where('is_active', 1)->findAll();
        $gallery = $this->galleryModel->where('category', 'wisata')->findAll(6);
        
        $data = [
            'title' => 'Wisata Pulau Misool - Raja Ampat Boat Services',
            'island' => $island,
            'packages' => $packages,
            'gallery' => $gallery,
            'page' => 'misool'
        ];
        
        $this->render('tour/island', $data);
    }
    
    // Salawati Island page
    public function salawati()
    {
        $island = $this->islandModel->where('island_name', 'Salawati')->first();
        $packages = $this->tourPackageModel->where('island_slug', 'salawati')->where('is_active', 1)->findAll();
        $gallery = $this->galleryModel->where('category', 'wisata')->findAll(6);
        
        $data = [
            'title' => 'Wisata Pulau Salawati - Raja Ampat Boat Services',
            'island' => $island,
            'packages' => $packages,
            'gallery' => $gallery,
            'page' => 'salawati'
        ];
        
        $this->render('tour/island', $data);
    }
    
    // Batanta Island page
    public function batanta()
    {
        $island = $this->islandModel->where('island_name', 'Batanta')->first();
        $packages = $this->tourPackageModel->where('island_slug', 'batanta')->where('is_active', 1)->findAll();
        $gallery = $this->galleryModel->where('category', 'wisata')->findAll(6);
        
        $data = [
            'title' => 'Wisata Pulau Batanta - Raja Ampat Boat Services',
            'island' => $island,
            'packages' => $packages,
            'gallery' => $gallery,
            'page' => 'batanta'
        ];
        
        $this->render('tour/island', $data);
    }
    
    // All tour packages page
    public function packages()
    {
        $packages = $this->tourPackageModel->where('is_active', 1)->findAll();
        $islands = $this->islandModel->findAll();
        
        $data = [
            'title' => 'Paket Wisata Lengkap - Raja Ampat Boat Services',
            'packages' => $packages,
            'islands' => $islands,
            'page' => 'packages'
        ];
        
        $this->render('tour/packages', $data);
    }
    
    // Package detail page
    public function detail($slug)
    {
        $package = $this->tourPackageModel->where('slug', $slug)->where('is_active', 1)->first();
        
        if (!$package) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Paket wisata tidak ditemukan');
        }
        
        $relatedPackages = $this->tourPackageModel->where('island_slug', $package['island_slug'])
                                                 ->where('package_id !=', $package['package_id'])
                                                 ->where('is_active', 1)
                                                 ->findAll(3);
        
        $data = [
            'title' => $package['package_name'] . ' - Raja Ampat Boat Services',
            'package' => $package,
            'relatedPackages' => $relatedPackages
        ];
        
        $this->render('tour/detail', $data);
    }
}