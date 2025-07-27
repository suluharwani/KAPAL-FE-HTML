<?php namespace App\Controllers\Api;

use App\Models\FaqModel;

class Faqs extends BaseApiController
{
    protected $modelName = FaqModel::class;

    public function __construct()
    {
        $this->model = new FaqModel();
    }

    public function index()
    {
        $params = $this->getPaginationParams();
        $params['category'] = $this->request->getGet('category');
        $params['featured'] = $this->request->getGet('featured');

        $faqs = $this->model->getPaginated($params);

        return $this->respond([
            'status' => 200,
            'data' => $faqs['data'],
            'pagination' => $faqs['pagination']
        ]);
    }

    public function featured()
    {
        $faqs = $this->model->getFeatured();

        return $this->respond([
            'status' => 200,
            'data' => $faqs
        ]);
    }

    public function categories()
    {
        $categories = [
            'booking' => 'Booking',
            'payment' => 'Payment',
            'trip' => 'Trip',
            'other' => 'Other'
        ];

        return $this->respond([
            'status' => 200,
            'data' => $categories
        ]);
    }

    public function create()
    {
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only admin can create FAQs');
        }

        $rules = [
            'question' => 'required|min_length[5]',
            'answer' => 'required|min_length[10]',
            'category' => 'required|in_list[booking,payment,trip,other]',
            'is_featured' => 'permit_empty|in_list[0,1]',
            'display_order' => 'permit_empty|integer'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'question' => $this->request->getVar('question'),
            'answer' => $this->request->getVar('answer'),
            'category' => $this->request->getVar('category'),
            'is_featured' => $this->request->getVar('is_featured') ?? 0,
            'display_order' => $this->request->getVar('display_order') ?? 0
        ];

        $faqId = $this->model->insert($data);

        if ($faqId) {
            return $this->respondCreated(['faq_id' => $faqId]);
        } else {
            return $this->failServerError('Failed to create FAQ');
        }
    }

    public function update($id = null)
    {
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only admin can update FAQs');
        }

        $faq = $this->model->find($id);
        if (!$faq) {
            return $this->respondNotFound('FAQ not found');
        }

        $rules = [
            'question' => 'permit_empty|min_length[5]',
            'answer' => 'permit_empty|min_length[10]',
            'category' => 'permit_empty|in_list[booking,payment,trip,other]',
            'is_featured' => 'permit_empty|in_list[0,1]',
            'display_order' => 'permit_empty|integer'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'question' => $this->request->getVar('question') ?? $faq['question'],
            'answer' => $this->request->getVar('answer') ?? $faq['answer'],
            'category' => $this->request->getVar('category') ?? $faq['category'],
            'is_featured' => $this->request->getVar('is_featured') ?? $faq['is_featured'],
            'display_order' => $this->request->getVar('display_order') ?? $faq['display_order']
        ];

        if ($this->model->update($id, $data)) {
            return $this->respondUpdated(['faq_id' => $id]);
        } else {
            return $this->failServerError('Failed to update FAQ');
        }
    }

    public function delete($id = null)
    {
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only admin can delete FAQs');
        }

        $faq = $this->model->find($id);
        if (!$faq) {
            return $this->respondNotFound('FAQ not found');
        }

        if ($this->model->delete($id)) {
            return $this->respondDeleted();
        } else {
            return $this->failServerError('Failed to delete FAQ');
        }
    }
}