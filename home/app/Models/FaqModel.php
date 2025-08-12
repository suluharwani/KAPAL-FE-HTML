<?php namespace App\Models;

use CodeIgniter\Model;

class FaqModel extends Model
{
    protected $table = 'faqs';
    protected $primaryKey = 'faq_id';
    protected $allowedFields = ['question', 'answer', 'category', 'is_featured','display_order','created_at','updated_at'];
    protected $returnType = 'array';
}