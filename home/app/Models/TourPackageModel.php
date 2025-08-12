<?php namespace App\Models;

use CodeIgniter\Model;

class TourPackageModel extends Model
{
    protected $table = 'tour_packages';
    protected $primaryKey = 'package_id';
    protected $allowedFields = ['package_name', 'island_slug', 'description', 'duration', 'price', 'inclusions', 'image'];
    protected $returnType = 'array';
}