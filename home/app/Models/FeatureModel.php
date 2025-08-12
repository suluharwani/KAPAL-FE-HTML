<?php namespace App\Models;

use CodeIgniter\Model;

class FeatureModel extends Model
{
    protected $table = 'features';
    protected $primaryKey = 'feature_id';
    protected $allowedFields = ['title', 'description', 'icon', 'is_active'];
    protected $returnType = 'array';
}