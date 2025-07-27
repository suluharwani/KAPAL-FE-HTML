<?php namespace App\Models;

use CodeIgniter\Model;

class TestimonialModel extends Model
{
    protected $table = 'testimonials';
    protected $primaryKey = 'testimonial_id';
    
    protected $allowedFields = [
        'user_id',
        'guest_name',
        'guest_email',
        'content',
        'rating',
        'status',
        'created_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = null; // Tidak ada updated_at di tabel
    
    protected $validationRules = [
        'content' => 'required|min_length[10]',
        'rating' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[5]',
        'status' => 'permit_empty|in_list[pending,approved,rejected]'
    ];
    
    protected $beforeInsert = ['setGuestInfo'];

    /**
     * Get paginated testimonials with optional filters
     * 
     * @param array $params Pagination and filter parameters
     * @return array
     */
    public function getPaginated(array $params): array
    {
        $builder = $this->builder();
        
        // Apply status filter if provided
        if (!empty($params['status'])) {
            $builder->where('status', $params['status']);
        }
        
        // Apply rating filter if provided
        if (!empty($params['min_rating'])) {
            $builder->where('rating >=', $params['min_rating']);
        }
        
        // Apply search if provided
        if (!empty($params['search'])) {
            $builder->groupStart()
                ->like('content', $params['search'])
                ->orLike('guest_name', $params['search'])
                ->groupEnd();
        }
        
        // Total rows count
        $total = $builder->countAllResults(false);
        
        // Apply pagination
        $page = $params['page'] ?? 1;
        $perPage = $params['per_page'] ?? 10;
        $offset = ($page - 1) * $perPage;
        
        $builder->limit($perPage, $offset);
        
        // Apply sorting
        $sortField = $params['sort'] ?? 'created_at';
        $sortOrder = $params['order'] ?? 'desc';
        $builder->orderBy($sortField, $sortOrder);
        
        // Get results
        $data = $builder->get()->getResultArray();
        
        return [
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($total / $perPage)
            ]
        ];
    }
    
    /**
     * Get featured testimonials (approved with high rating)
     * 
     * @param int $limit Number of testimonials to return
     * @return array
     */
    public function getFeatured(int $limit = 5): array
    {
        return $this->where('status', 'approved')
                   ->where('rating >=', 4)
                   ->orderBy('created_at', 'desc')
                   ->limit($limit)
                   ->findAll();
    }
    
    /**
     * Set guest info if user is logged in
     */
    protected function setGuestInfo(array $data): array
    {
        if (isset($data['data']['user_id']) && empty($data['data']['guest_name'])) {
            $userModel = new \App\Models\UserModel();
            $user = $userModel->find($data['data']['user_id']);
            
            if ($user) {
                $data['data']['guest_name'] = $user['full_name'];
                $data['data']['guest_email'] = $user['email'];
            }
        }
        
        return $data;
    }
    
    /**
     * Get testimonials by user ID
     */
    public function getByUser(int $userId, int $limit = 10): array
    {
        return $this->where('user_id', $userId)
                   ->orderBy('created_at', 'desc')
                   ->limit($limit)
                   ->findAll();
    }
}