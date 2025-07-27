<?php namespace App\Models;

use CodeIgniter\Model;

class BlogModel extends Model
{
    protected $table = 'blogs';
    protected $primaryKey = 'blog_id';
    
    protected $allowedFields = [
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'author_id',
        'category_id',
        'status',
        'published_at',
        'created_at',
        'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';
    
    protected $validationRules = [
        'title' => 'required|min_length[3]|max_length[255]',
        'content' => 'required',
        'author_id' => 'required|numeric',
        'status' => 'permit_empty|in_list[draft,published,archived]'
    ];
    
    protected $beforeInsert = ['generateSlug'];
    protected $beforeUpdate = ['generateSlug'];
    
    /**
     * Get paginated blog posts
     * 
     * @param array $params Pagination parameters
     * @return array
     */
    public function getPaginated(array $params): array
    {
        $builder = $this->builder();
        
        // Apply filters if any
        if (!empty($params['status'])) {
            $builder->where('status', $params['status']);
        }
        
        if (!empty($params['category_id'])) {
            $builder->where('category_id', $params['category_id']);
        }
        
        if (!empty($params['search'])) {
            $builder->groupStart()
                ->like('title', $params['search'])
                ->orLike('content', $params['search'])
                ->orLike('excerpt', $params['search'])
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
        $sortField = $params['sort'] ?? 'published_at';
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
     * Generate slug before insert/update
     */
    protected function generateSlug(array $data): array
    {
        if (isset($data['data']['title'])) {
            $slug = url_title($data['data']['title'], '-', true);
            
            // Make sure slug is unique
            $originalSlug = $slug;
            $count = 1;
            
            while ($this->where('slug', $slug)->countAllResults() > 0) {
                if (!empty($data['id'])) {
                    $existing = $this->find($data['id']);
                    if ($existing['slug'] === $slug) {
                        break;
                    }
                }
                $slug = $originalSlug . '-' . $count++;
            }
            
            $data['data']['slug'] = $slug;
        }
        
        return $data;
    }
    
    /**
     * Get blog posts by category
     */
    public function getByCategory(int $categoryId, int $limit = 10): array
    {
        return $this->where('category_id', $categoryId)
                   ->where('status', 'published')
                   ->orderBy('published_at', 'desc')
                   ->limit($limit)
                   ->findAll();
    }
    
    /**
     * Get recent blog posts
     */
    public function getRecent(int $limit = 5): array
    {
        return $this->where('status', 'published')
                   ->orderBy('published_at', 'desc')
                   ->limit($limit)
                   ->findAll();
    }
    
    /**
     * Get blog post by slug
     */
    public function getBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)
                   ->where('status', 'published')
                   ->first();
    }
}