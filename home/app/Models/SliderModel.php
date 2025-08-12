<?php namespace App\Models;

use CodeIgniter\Model;

class SliderModel extends Model
{
    protected $table = 'sliders';
    protected $primaryKey = 'slider_id';
    protected $allowedFields = ['title', 'description', 'image', 'is_active'];
    protected $returnType = 'array';
}