<?php
namespace App\Controllers;

use App\Models\TeamModel;
use App\Models\TestimonialModel;

class About extends BaseController
{
    protected $teamModel;
    protected $testimonialModel;
    
    public function __construct()
    {
        $this->teamModel = new TeamModel();
        $this->testimonialModel = new TestimonialModel();
    }
    
    // About Us page
    public function index()
    {
        $data = [
            'title' => 'Tentang Kami - Raja Ampat Boat Services',
            'page' => 'about',
            'testimonials' => $this->testimonialModel->getApprovedTestimonials(6)
        ];
        
        $this->render('about/index', $data);
    }
    
    // Team page
    public function team()
    {
        $data = [
            'title' => 'Tim Kami - Raja Ampat Boat Services',
            'page' => 'team',
            'teamMembers' => $this->teamModel->getActiveTeamMembers()
        ];
        
        $this->render('about/team', $data);
    }
    
    // Testimonials page
    public function testimonials()
    {
        $data = [
            'title' => 'Testimonial - Raja Ampat Boat Services',
            'page' => 'testimonials',
            'testimonials' => $this->testimonialModel->getApprovedTestimonials()
        ];
        
        $this->render('about/testimonials', $data);
    }
}