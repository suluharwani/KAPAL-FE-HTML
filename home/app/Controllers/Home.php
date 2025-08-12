<?php namespace App\Controllers;
use App\Models\FeatureModel;
use App\Models\IslandModel;
use App\Models\PopularRouteModel;
use App\Models\SliderModel;
use App\Models\TestimonialModel;
class Home extends BaseController
{
     public function index()
    {
        // Load models
        $sliderModel = new SliderModel();
        $islandModel = new IslandModel();
        $featureModel = new FeatureModel();
        $popularRouteModel = new PopularRouteModel();
        $testimonialModel = new TestimonialModel();

        // Get data from database
        $data = [
            'title' => 'Pemesanan Kapal Raja Ampat',
            'active' => 'home',
            'sliders' => $sliderModel->where('is_active', 1)->findAll(),
            'islands' => $islandModel->findAll(),
            'features' => $featureModel->where('is_active', 1)->findAll(),
            'popularRoutes' => $popularRouteModel->getPopularRoutes(),
            'testimonials' => $testimonialModel->where('status', 'approved')->orderBy('created_at', 'DESC')->findAll(3)
        ];
        
        $this->render('home', $data);
    }

    public function about()
    {
        $data = [
            'title' => 'Tentang Kami - Raja Ampat Boat Services',
            'active' => 'about'
        ];
        
        $this->render('about', $data);
    }

    public function blog()
    {
        $data = [
            'title' => 'Blog - Raja Ampat Boat Services',
            'active' => 'blog'
        ];
        
        $this->render('blog', $data);
    }

    public function blogSingle($slug)
    {
        $data = [
            'title' => 'Blog Post - Raja Ampat Boat Services',
            'active' => 'blog',
            'post' => [
                'title' => '5 Spot Snorkeling Terbaik di Raja Ampat',
                'content' => '...' // Your blog content here
            ]
        ];
        
        $this->render('blog_single', $data);
    }

    public function gallery()
    {
        $data = [
            'title' => 'Galeri - Raja Ampat Boat Services',
            'active' => 'gallery'
        ];
        
        $this->render('gallery', $data);
    }

    public function contact()
    {
        $data = [
            'title' => 'Kontak - Raja Ampat Boat Services',
            'active' => 'contact'
        ];
        
        $this->render('contact', $data);
    }

    public function faq()
    {
        $data = [
            'title' => 'FAQ - Raja Ampat Boat Services',
            'active' => 'faq'
        ];
        
        $this->render('faq', $data);
    }
}