<?php namespace App\Controllers\Api;

use App\Models\PaymentModel;
use App\Models\BookingModel;

class Payments extends BaseApiController
{
    protected $modelName = PaymentModel::class;

    public function __construct()
    {
        $this->model = new PaymentModel();
        $this->bookingModel = new BookingModel();
    }

    public function index()
    {
        $params = $this->getPaginationParams();
        $userId = $this->request->user->role === 'admin' ? null : $this->request->user->user_id;
        $payments = $this->model->getPaginated($params, $userId);

        return $this->respond([
            'status' => 200,
            'data' => $payments['data'],
            'pagination' => $payments['pagination']
        ]);
    }

    public function show($id = null)
    {
        $payment = $this->model->find($id);
        if (!$payment) {
            return $this->respondNotFound('Payment not found');
        }

        // Check authorization
        if ($this->request->user->role !== 'admin') {
            $booking = $this->bookingModel->find($payment['booking_id']);
            if ($booking['user_id'] !== $this->request->user->user_id) {
                return $this->failForbidden('You are not authorized to view this payment');
            }
        }

        return $this->respond([
            'status' => 200,
            'data' => $payment
        ]);
    }

    public function create()
    {
        $rules = [
            'booking_id' => 'required|integer',
            'amount' => 'required|decimal',
            'payment_method' => 'required|in_list[transfer,cash]',
            'payment_date' => 'permit_empty|valid_date',
            'bank_name' => 'required_if[payment_method,transfer]',
            'account_number' => 'required_if[payment_method,transfer]',
            'receipt_image' => 'required_if[payment_method,transfer]|uploaded[receipt_image]|max_size[receipt_image,2048]|is_image[receipt_image]',
            'notes' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        // Check booking
        $booking = $this->bookingModel->find($this->request->getVar('booking_id'));
        if (!$booking) {
            return $this->respondNotFound('Booking not found');
        }

        // Check authorization
        if ($this->request->user->role !== 'admin' && $booking['user_id'] !== $this->request->user->user_id) {
            return $this->failForbidden('You are not authorized to make payment for this booking');
        }

        $data = [
            'booking_id' => $this->request->getVar('booking_id'),
            'amount' => $this->request->getVar('amount'),
            'payment_method' => $this->request->getVar('payment_method'),
            'payment_date' => $this->request->getVar('payment_date') ?? date('Y-m-d H:i:s'),
            'bank_name' => $this->request->getVar('bank_name'),
            'account_number' => $this->request->getVar('account_number'),
            'notes' => $this->request->getVar('notes'),
            'status' => 'pending'
        ];

        // Handle receipt image upload
        if ($image = $this->request->getFile('receipt_image')) {
            if ($image->isValid() && !$image->hasMoved()) {
                $newName = $image->getRandomName();
                $image->move(ROOTPATH . 'public/uploads/payments', $newName);
                $data['receipt_image'] = 'uploads/payments/' . $newName;
            }
        }

        $this->db->transStart();

        $paymentId = $this->model->insert($data);

        // Update booking payment status if full payment
        $totalPaid = $this->model->where('booking_id', $booking['booking_id'])
                                ->selectSum('amount')
                                ->get()
                                ->getRow()->amount;

        if ($totalPaid >= $booking['total_price']) {
            $this->bookingModel->update($booking['booking_id'], [
                'payment_status' => 'paid',
                'booking_status' => 'confirmed'
            ]);
        } elseif ($totalPaid > 0) {
            $this->bookingModel->update($booking['booking_id'], [
                'payment_status' => 'partial'
            ]);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->failServerError('Failed to record payment');
        }

        return $this->respondCreated([
            'payment_id' => $paymentId,
            'booking_id' => $booking['booking_id'],
            'payment_status' => $totalPaid >= $booking['total_price'] ? 'paid' : 'partial'
        ], 'Payment recorded successfully');
    }

    public function updateStatus($id = null)
    {
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only admin can update payment status');
        }

        $rules = [
            'status' => 'required|in_list[pending,verified,rejected]',
            'notes' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $payment = $this->model->find($id);
        if (!$payment) {
            return $this->respondNotFound('Payment not found');
        }

        $status = $this->request->getVar('status');
        $notes = $this->request->getVar('notes');

        $this->db->transStart();

        $this->model->update($id, [
            'status' => $status,
            'notes' => $notes
        ]);

        // Update booking status if payment is verified
        if ($status === 'verified') {
            $booking = $this->bookingModel->find($payment['booking_id']);
            $totalPaid = $this->model->where('booking_id', $booking['booking_id'])
                                    ->where('status', 'verified')
                                    ->selectSum('amount')
                                    ->get()
                                    ->getRow()->amount;

            if ($totalPaid >= $booking['total_price']) {
                $this->bookingModel->update($booking['booking_id'], [
                    'payment_status' => 'paid',
                    'booking_status' => 'confirmed'
                ]);
            } elseif ($totalPaid > 0) {
                $this->bookingModel->update($booking['booking_id'], [
                    'payment_status' => 'partial'
                ]);
            }
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->failServerError('Failed to update payment status');
        }

        return $this->respondUpdated(['payment_id' => $id], 'Payment status updated');
    }
}