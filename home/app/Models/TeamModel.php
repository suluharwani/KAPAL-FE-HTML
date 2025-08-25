<?php
namespace App\Models;

use CodeIgniter\Model;

class TeamModel extends Model
{
    protected $table = 'teams';
    protected $primaryKey = 'team_id';
    protected $allowedFields = [
        'name', 'position', 'image', 'bio', 
        'social_facebook', 'social_twitter', 
        'social_instagram', 'social_linkedin',
        'is_active', 'display_order'
    ];
    protected $useTimestamps = true;
    
    // Get active team members ordered by display order
    public function getActiveTeamMembers()
    {
        return $this->where('is_active', 1)
                    ->orderBy('display_order', 'ASC')
                    ->findAll();
    }
    
    // Get team member by ID
    public function getTeamMember($id)
    {
        return $this->find($id);
    }
}