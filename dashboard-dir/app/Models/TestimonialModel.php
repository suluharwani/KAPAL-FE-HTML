<?php namespace App\Models;

use CodeIgniter\Model;

class TestimonialModel extends Model
{
    protected $table = 'testimonials';
    protected $primaryKey = 'testimonial_id';
    protected $allowedFields = ['user_id', 'guest_name', 'guest_email', 'content', 'rating', 'status'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    
    public function getTestimonials($status = null)
    {
        $builder = $this->select('testimonials.*, users.full_name as user_name')
                       ->join('users', 'users.user_id = testimonials.user_id', 'left');
        
        if ($status) {
            $builder->where('testimonials.status', $status);
        }
        
        return $builder->orderBy('testimonials.created_at', 'DESC')
                      ->findAll();
    }
}