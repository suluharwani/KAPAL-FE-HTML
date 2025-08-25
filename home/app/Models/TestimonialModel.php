<?php
namespace App\Models;

use CodeIgniter\Model;

class TestimonialModel extends Model
{
    protected $table = 'testimonials';
    protected $primaryKey = 'testimonial_id';
    protected $allowedFields = [
        'user_id', 'guest_name', 'guest_email', 
        'content', 'rating', 'image', 'status'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    
    // Get approved testimonials
    public function getApprovedTestimonials($limit = null)
    {
        $builder = $this->db->table('testimonials t');
        $builder->select('t.*, u.full_name as user_name, u.phone as user_phone');
        $builder->join('users u', 't.user_id = u.user_id', 'left');
        $builder->where('t.status', 'approved');
        $builder->orderBy('t.created_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->get()->getResultArray();
    }
    
    // Get testimonials by rating
    public function getTestimonialsByRating($minRating = 4)
    {
        return $this->where('status', 'approved')
                    ->where('rating >=', $minRating)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
    
    // Get featured testimonials
    public function getFeaturedTestimonials($limit = 3)
    {
        return $this->where('status', 'approved')
                    ->where('rating >=', 4)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}