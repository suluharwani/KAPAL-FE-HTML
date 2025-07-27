<?php namespace App\Controllers\Api;

use App\Models\TestimonialModel;
use App\Models\UserModel;

class Testimonials extends BaseApiController
{
    protected $modelName = TestimonialModel::class;

    public function __construct()
    {
        $this->model = new TestimonialModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $params = $this->getPaginationParams();
        $testimonials = $this->model->getPaginated($params);

        return $this->respond([
            'status' => 200,
            'data' => $testimonials['data'],
            'pagination' => $testimonials['pagination']
        ]);
    }

    public function approved()
    {
        $testimonials = $this->model->where('status', 'approved')
                                   ->orderBy('created_at', 'desc')
                                   ->findAll();

        return $this->respond([
            'status' => 200,
            'data' => $testimonials
        ]);
    }

    public function create()
    {
        $userId = $this->request->user->user_id;
        $user = $this->userModel->find($userId);

        $rules = [
            'content' => 'required|min_length[10]',
            'rating' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[5]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'user_id' => $userId,
            'guest_name' => $user['full_name'],
            'guest_email' => $user['email'],
            'content' => $this->request->getVar('content'),
            'rating' => $this->request->getVar('rating'),
            'status' => 'pending'
        ];

        $testimonialId = $this->model->insert($data);

        if ($testimonialId) {
            return $this->respondCreated(['testimonial_id' => $testimonialId]);
        } else {
            return $this->failServerError('Failed to submit testimonial');
        }
    }

    public function updateStatus($id = null)
    {
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only admin can update testimonial status');
        }

        $rules = [
            'status' => 'required|in_list[pending,approved,rejected]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $testimonial = $this->model->find($id);
        if (!$testimonial) {
            return $this->respondNotFound('Testimonial not found');
        }

        $status = $this->request->getVar('status');

        if ($this->model->update($id, ['status' => $status])) {
            return $this->respondUpdated(['testimonial_id' => $id]);
        } else {
            return $this->failServerError('Failed to update testimonial status');
        }
    }
}