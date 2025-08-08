<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PaymentModel;
use App\Models\BookingModel;

class Payments extends BaseController
{
    protected $paymentModel;
    protected $bookingModel;

    public function __construct()
    {
        $this->paymentModel = new PaymentModel();
        $this->bookingModel = new BookingModel();
    }

    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/admin/login');
        }

        $data = [
            'payments' => $this->paymentModel->getPaymentsWithDetails(10),
            'pager' => $this->paymentModel->pager
        ];

        return view('admin/payments/index', $data);
    }

    public function verify($id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/admin/login');
        }

        $payment = $this->paymentModel->find($id);
        if (!$payment) {
            return redirect()->to('/admin/payments')->with('error', 'Payment not found');
        }

        if ($this->paymentModel->update($id, ['status' => 'verified'])) {
            // Update booking status to paid
            $this->bookingModel->update($payment['booking_id'], ['payment_status' => 'paid', 'booking_status' => 'confirmed']);
            
            return redirect()->to('/admin/payments')->with('success', 'Payment verified successfully');
        } else {
            return redirect()->to('/admin/payments')->with('error', 'Failed to verify payment');
        }
    }

    public function reject($id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/admin/login');
        }

        $payment = $this->paymentModel->find($id);
        if (!$payment) {
            return redirect()->to('/admin/payments')->with('error', 'Payment not found');
        }

        if ($this->paymentModel->update($id, ['status' => 'rejected'])) {
            return redirect()->to('/admin/payments')->with('success', 'Payment rejected successfully');
        } else {
            return redirect()->to('/admin/payments')->with('error', 'Failed to reject payment');
        }
    }
}