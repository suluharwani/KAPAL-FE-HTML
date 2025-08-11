<?php namespace App\Models;

use CodeIgniter\Model;

class FaqModel extends Model
{
    protected $table = 'faqs';
    protected $primaryKey = 'faq_id';
    protected $allowedFields = [
        'question', 'answer', 'category', 'is_featured', 'display_order'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getFaqsByCategory($category = null, $isFeatured = false)
    {
        $builder = $this->orderBy('display_order', 'ASC');

        if ($category) {
            $builder->where('category', $category);
        }

        if ($isFeatured) {
            $builder->where('is_featured', 1);
        }

        return $builder->findAll();
    }

    public function getCategories()
    {
        return $this->distinct()->select('category')->findAll();
    }
}