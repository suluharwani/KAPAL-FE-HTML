<?php namespace App\Models;

use CodeIgniter\Model;

class TeamModel extends Model
{
    protected $table = 'team_members';
    protected $primaryKey = 'member_id';
    protected $allowedFields = ['full_name', 'position', 'bio', 'photo', 'social_links'];
    protected $returnType = 'array';
}