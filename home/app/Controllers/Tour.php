<?php namespace App\Controllers;

use App\Models\IslandModel;
use App\Models\TourPackageModel;

class Tour extends BaseController
{
    public function index($island = null)
    {
        $model = new IslandModel();
        $packageModel = new TourPackageModel();
        
        if ($island) {
            $data = [
                'title' => 'Wisata ' . ucfirst($island),
                'island' => $model->where('slug', $island)->first(),
                'packages' => $packageModel->where('island_slug', $island)->findAll()
            ];
            
            return $this->render('tour/island', $data);
        }
        
        $data = [
            'title' => 'Paket Wisata Lengkap',
            'islands' => $model->findAll(),
            'packages' => $packageModel->findAll()
        ];
        
        return $this->render('tour/packages', $data);
    }
    
    public function waigeo() { return $this->index('waigeo'); }
    public function misool() { return $this->index('misool'); }
    public function salawati() { return $this->index('salawati'); }
    public function batanta() { return $this->index('batanta'); }
    public function packages() { return $this->index(); }
}