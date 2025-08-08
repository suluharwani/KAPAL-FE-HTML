<?php namespace App\Controllers;

use App\Models\FaqModel;

class FaqController extends BaseController
{
    protected $faqModel;

    public function __construct()
    {
        $this->faqModel = new FaqModel();
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        $data = [
            'title' => 'Manage FAQs',
            'faqs' => $this->faqModel->findAll(),
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/faqs/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Add New FAQ',
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/faqs/create', $data);
    }

    public function store()
    {
        $rules = [
            'question' => 'required',
            'answer' => 'required',
            'category' => 'required|in_list[booking,payment,trip,other]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'question' => $this->request->getPost('question'),
            'answer' => $this->request->getPost('answer'),
            'category' => $this->request->getPost('category'),
            'is_featured' => $this->request->getPost('is_featured') ? 1 : 0,
            'display_order' => $this->request->getPost('display_order') ?? 0
        ];

        if ($this->faqModel->insert($data)) {
            return redirect()->to('/admin/faqs')->with('success', 'FAQ added successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to add FAQ');
        }
    }

    public function edit($id)
    {
        $faq = $this->faqModel->find($id);
        if (!$faq) {
            return redirect()->to('/admin/faqs')->with('error', 'FAQ not found');
        }

        $data = [
            'title' => 'Edit FAQ',
            'faq' => $faq,
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/faqs/edit', $data);
    }

    public function update($id)
    {
        $rules = [
            'question' => 'required',
            'answer' => 'required',
            'category' => 'required|in_list[booking,payment,trip,other]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'question' => $this->request->getPost('question'),
            'answer' => $this->request->getPost('answer'),
            'category' => $this->request->getPost('category'),
            'is_featured' => $this->request->getPost('is_featured') ? 1 : 0,
            'display_order' => $this->request->getPost('display_order') ?? 0
        ];

        if ($this->faqModel->update($id, $data)) {
            return redirect()->to('/admin/faqs')->with('success', 'FAQ updated successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update FAQ');
        }
    }

    public function delete($id)
    {
        if ($this->faqModel->delete($id)) {
            return redirect()->to('/admin/faqs')->with('success', 'FAQ deleted successfully');
        } else {
            return redirect()->to('/admin/faqs')->with('error', 'Failed to delete FAQ');
        }
    }
}