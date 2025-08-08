<?php namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\BoatModel;
use App\Models\ScheduleModel;
use App\Models\BlogModel;
use App\Models\TestimonialModel;
use App\Models\GalleryModel;
use App\Models\FaqModel;

class Api extends BaseController
{
    use ResponseTrait;

    public function boats()
    {
        $boatModel = new BoatModel();
        $boats = $boatModel->findAll();
        
        return $this->respond([
            'status' => 'success',
            'data' => $boats
        ]);
    }

    public function schedules($departureIslandId = null, $arrivalIslandId = null)
    {
        $scheduleModel = new ScheduleModel();
        $builder = $scheduleModel->select('schedules.*, 
                                        boats.boat_name, boats.boat_type, boats.capacity, boats.price_per_trip,
                                        di.island_name as departure_island, 
                                        ai.island_name as arrival_island')
            ->join('boats', 'boats.boat_id = schedules.boat_id')
            ->join('routes r', 'r.route_id = schedules.route_id')
            ->join('islands di', 'di.island_id = r.departure_island_id')
            ->join('islands ai', 'ai.island_id = r.arrival_island')
            ->where('schedules.departure_date >=', date('Y-m-d'))
            ->orderBy('schedules.departure_date', 'ASC')
            ->orderBy('schedules.departure_time', 'ASC');

        if ($departureIslandId) {
            $builder->where('r.departure_island_id', $departureIslandId);
        }

        if ($arrivalIslandId) {
            $builder->where('r.arrival_island_id', $arrivalIslandId);
        }

        $schedules = $builder->findAll();

        return $this->respond([
            'status' => 'success',
            'data' => $schedules
        ]);
    }

    public function blogs($limit = null)
    {
        $blogModel = new BlogModel();
        $blogs = $blogModel->where('status', 'published')
            ->where('published_at <=', date('Y-m-d H:i:s'))
            ->orderBy('published_at', 'DESC');

        if ($limit) {
            $blogs->limit($limit);
        }

        return $this->respond([
            'status' => 'success',
            'data' => $blogs->findAll()
        ]);
    }

    public function blogDetail($slug)
    {
        $blogModel = new BlogModel();
        $blog = $blogModel->where('slug', $slug)
            ->where('status', 'published')
            ->first();

        if (!$blog) {
            return $this->failNotFound('Blog post not found');
        }

        return $this->respond([
            'status' => 'success',
            'data' => $blog
        ]);
    }

    public function testimonials()
    {
        $testimonialModel = new TestimonialModel();
        $testimonials = $testimonialModel->where('status', 'approved')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return $this->respond([
            'status' => 'success',
            'data' => $testimonials
        ]);
    }

    public function gallery($category = null)
    {
        $galleryModel = new GalleryModel();
        $builder = $galleryModel->orderBy('created_at', 'DESC');

        if ($category) {
            $builder->where('category', $category);
        }

        return $this->respond([
            'status' => 'success',
            'data' => $builder->findAll()
        ]);
    }

    public function faqs($category = null)
    {
        $faqModel = new FaqModel();
        $builder = $faqModel->orderBy('display_order', 'ASC');

        if ($category) {
            $builder->where('category', $category);
        }

        return $this->respond([
            'status' => 'success',
            'data' => $builder->findAll()
        ]);
    }

    public function islands()
    {
        $islandModel = new IslandModel();
        $islands = $islandModel->findAll();

        return $this->respond([
            'status' => 'success',
            'data' => $islands
        ]);
    }
}