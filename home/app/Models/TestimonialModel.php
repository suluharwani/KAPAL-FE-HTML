<?php namespace App\Models;

use CodeIgniter\Model;

class TestimonialModel extends Model
{
    protected $table = 'testimonials';
    protected $primaryKey = 'testimonial_id';
    protected $allowedFields = ['user_id', 'guest_name', 'guest_email', 'content', 'rating', 'image', 'status'];
    protected $returnType = 'array';
}