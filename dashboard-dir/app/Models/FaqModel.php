<?php namespace App\Models;

use CodeIgniter\Model;

class FaqModel extends Model
{
    protected $table = 'faqs';
    protected $primaryKey = 'faq_id';
    protected $allowedFields = ['question', 'answer', 'category', 'is_featured', 'display_order'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    public function getFaqs($category = null, $featured = false)
    {
        $builder = $this;
        if ($category) {
            $builder->where('category', $category);
        }
        if ($featured) {
            $builder->where('is_featured', 1);
        }
        return $builder->orderBy('display_order', 'ASC')->findAll();
    }
}