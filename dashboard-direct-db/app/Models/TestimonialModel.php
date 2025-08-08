<?php namespace App\Models;

use CodeIgniter\Model;

class TestimonialModel extends Model
{
    protected $table = 'testimonials';
    protected $primaryKey = 'testimonial_id';
    protected $allowedFields = [
        'user_id', 'guest_name', 'guest_email', 'content', 'rating', 'status'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    public function getTestimonials($status = null, $limit = null)
    {
        $builder = $this->select('testimonials.*, users.full_name as user_name, users.email as user_email')
            ->join('users', 'users.user_id = testimonials.user_id', 'left')
            ->orderBy('testimonials.created_at', 'DESC');

        if ($status) {
            $builder->where('testimonials.status', $status);
        }

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    public function getApprovedTestimonials($limit = null)
    {
        return $this->where('status', 'approved')
            ->orderBy('created_at', 'DESC')
            ->findAll($limit);
    }

    public function updateStatus($testimonialId, $status)
    {
        return $this->update($testimonialId, ['status' => $status]);
    }
}