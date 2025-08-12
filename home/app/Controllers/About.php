<?php namespace App\Controllers;

use App\Models\TeamModel;
use App\Models\TestimonialModel;

class About extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Tentang Kami - Raja Ampat Boat Services',
            'active' => 'about'
        ];
        
        return $this->render('about/index', $data);
    }
    
    public function team()
    {
        $model = new TeamModel();
        
        $data = [
            'title' => 'Tim Kami - Raja Ampat Boat Services',
            'team' => $model->findAll(),
            'active' => 'about'
        ];
        
        return $this->render('about/team', $data);
    }
    
    public function testimonials()
    {
        $model = new TestimonialModel();
        
        $data = [
            'title' => 'Testimonial - Raja Ampat Boat Services',
            'testimonials' => $model->where('status', 'approved')->findAll(),
            'active' => 'about'
        ];
        
        return $this->render('about/testimonials', $data);
    }
}