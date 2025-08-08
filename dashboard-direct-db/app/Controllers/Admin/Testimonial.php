<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TestimonialModel;

class Testimonial extends BaseController
{
    protected $testimonialModel;

    public function __construct()
    {
        $this->testimonialModel = new TestimonialModel();
    }

    public function index()
    {
        $status = $this->request->getGet('status') ?? null;
        
        $data = [
            'testimonials' => $this->testimonialModel->getTestimonials($status),
            'statusFilter' => $status
        ];

        return view('admin/testimonial/index', $data);
    }

    public function approve($id)
    {
        if ($this->testimonialModel->updateStatus($id, 'approved')) {
            return redirect()->to('/admin/testimonial')->with('success', 'Testimonial approved');
        } else {
            return redirect()->back()->with('error', 'Failed to approve testimonial');
        }
    }

    public function reject($id)
    {
        if ($this->testimonialModel->updateStatus($id, 'rejected')) {
            return redirect()->to('/admin/testimonial')->with('success', 'Testimonial rejected');
        } else {
            return redirect()->back()->with('error', 'Failed to reject testimonial');
        }
    }

    public function delete($id)
    {
        if ($this->testimonialModel->delete($id)) {
            return redirect()->to('/admin/testimonial')->with('success', 'Testimonial deleted');
        } else {
            return redirect()->to('/admin/testimonial')->with('error', 'Failed to delete testimonial');
        }
    }
}