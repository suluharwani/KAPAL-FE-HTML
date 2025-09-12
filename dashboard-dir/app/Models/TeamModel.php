<?php
namespace App\Models;

use CodeIgniter\Model;

class TeamModel extends Model
{
    protected $table = 'teams';
    protected $primaryKey = 'team_id';
    protected $allowedFields = [
        'name', 
        'position', 
        'image', 
        'bio', 
        'social_facebook', 
        'social_twitter', 
        'social_instagram', 
        'social_linkedin', 
        'display_order', 
        'is_active', 
        'created_at', 
        'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'name' => 'required|max_length[255]',
        'position' => 'required|max_length[255]',
        'bio' => 'required'
    ];
}