<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FaqModel;

class Faq extends BaseController
{
    protected $faqModel;

    public function __construct()
    {
        $this->faqModel = new FaqModel();
    }

    public function index()
    {
        $category = $this->request->getGet('category') ?? null;
        
        $data = [
            'faqs' => $this->faqModel->getFaqsByCategory($category),
            'categories' => $this->faqModel->getCategories(),
            'categoryFilter' => $category
        ];

        return view('admin/faq/index', $data);
    }

    public function add()
    {
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'question' => 'required|min_length[10]|max_length[255]',
                'answer' => 'required|min_length[10]',
                'category' => 'required|in_list[booking,payment,trip,other]',
                'display_order' => 'permit_empty|numeric',
                'is_featured' => 'permit_empty'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $data = [
                'question' => $this->request->getPost('question'),
                'answer' => $this->request->getPost('answer'),
                'category' => $this->request->getPost('category'),
                'display_order' => $this->request->getPost('display_order') ?? 0,
                'is_featured' => $this->request->getPost('is_featured') ? 1 : 0
            ];

            if ($this->faqModel->save($data)) {
                return redirect()->to('/admin/faq')->with('success', 'FAQ added');
            } else {
                return redirect()->back()->with('error', 'Failed to add FAQ');
            }
        }

        return view('admin/faq/add');
    }

    public function edit($id)
    {
        $faq = $this->faqModel->find($id);
        if (!$faq) {
            return redirect()->to('/admin/faq')->with('error', 'FAQ not found');
        }

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'question' => 'required|min_length[10]|max_length[255]',
                'answer' => 'required|min_length[10]',
                'category' => 'required|in_list[booking,payment,trip,other]',
                'display_order' => 'permit_empty|numeric',
                'is_featured' => 'permit_empty'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $data = [
                'faq_id' => $id,
                'question' => $this->request->getPost('question'),
                'answer' => $this->request->getPost('answer'),
                'category' => $this->request->getPost('category'),
                'display_order' => $this->request->getPost('display_order') ?? 0,
                'is_featured' => $this->request->getPost('is_featured') ? 1 : 0
            ];

            if ($this->faqModel->save($data)) {
                return redirect()->to('/admin/faq')->with('success', 'FAQ updated');
            } else {
                return redirect()->back()->with('error', 'Failed to update FAQ');
            }
        }

        return view('admin/faq/edit', ['faq' => $faq]);
    }

    public function delete($id)
    {
        if ($this->faqModel->delete($id)) {
            return redirect()->to('/admin/faq')->with('success', 'FAQ deleted');
        } else {
            return redirect()->to('/admin/faq')->with('error', 'Failed to delete FAQ');
        }
    }

    public function toggleFeatured($id)
    {
        $faq = $this->faqModel->find($id);
        if (!$faq) {
            return redirect()->back()->with('error', 'FAQ not found');
        }

        $newStatus = $faq['is_featured'] ? 0 : 1;
        $this->faqModel->update($id, ['is_featured' => $newStatus]);

        return redirect()->back()->with('success', 'Featured status updated');
    }

    public function updateOrder()
    {
        $order = $this->request->getPost('order');
        
        if (is_array($order)) {
            foreach ($order as $position => $faqId) {
                $this->faqModel->update($faqId, ['display_order' => $position]);
            }
            
            return $this->response->setJSON(['success' => true]);
        }
        
        return $this->response->setJSON(['success' => false]);
    }
}