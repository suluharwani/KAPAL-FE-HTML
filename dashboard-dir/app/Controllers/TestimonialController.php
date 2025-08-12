<?php namespace App\Controllers;

use App\Models\TestimonialModel;
use App\Models\UserModel;

class TestimonialController extends BaseController
{
    protected $testimonialModel;
    protected $userModel;

    public function __construct()
    {
        $this->testimonialModel = new TestimonialModel();
        $this->userModel = new UserModel();
        $this->session = \Config\Services::session();
        
        // Check if user is logged in
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
    }

    public function index()
    {
        $status = $this->request->getGet('status');
        
        $data = [
            'title' => 'Manage Testimonials',
            'testimonials' => $this->testimonialModel->getTestimonials($status),
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        
        return view('admin/testimonials/index', $data);
    }

    public function updateStatus($id)
    {
        $status = $this->request->getPost('status');
        $validStatuses = ['pending', 'approved', 'rejected'];

        if (!in_array($status, $validStatuses)) {
            return redirect()->back()->with('error', 'Invalid status');
        }

        if ($this->testimonialModel->update($id, ['status' => $status])) {
            return redirect()->back()->with('success', 'Testimonial status updated');
        } else {
            return redirect()->back()->with('error', 'Failed to update status');
        }
    }

    public function delete($id)
    {
        if ($this->testimonialModel->delete($id)) {
            return redirect()->to('/admin/testimonials')->with('success', 'Testimonial deleted successfully');
        } else {
            return redirect()->to('/admin/testimonials')->with('error', 'Failed to delete testimonial');
        }
    }

    // Additional methods for AJAX implementation similar to the blogs example
    public function getTestimonials()
    {
        $status = $this->request->getGet('status');
        $testimonials = $this->testimonialModel->getTestimonials($status);
        
        return $this->response->setJSON([
            'data' => $testimonials
        ]);
    }

    public function edit($id)
    {
        $testimonial = $this->testimonialModel->find($id);
        
        if (!$testimonial) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Testimonial not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $testimonial
        ]);
    }
}