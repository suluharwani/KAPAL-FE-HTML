<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Services;

class Payments extends BaseController
{
    protected $apiUrl;
    protected $session;

    public function __construct()
    {
        $this->apiUrl = getenv('API_BASE_URL') . '/api/payments';
        $this->session = session();
    }

    // Daftar Pembayaran
    public function index()
{
    $client = \Config\Services::curlrequest();
    
    try {
        $response = $client->get($this->apiUrl, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->session->get('token')
            ],
            'query' => [
                'page' => $this->request->getGet('page') ?? 1,
                'per_page' => 10
            ]
        ]);

        $result = json_decode($response->getBody(), true);
        $payments = $result['data'] ?? [];
        
        // Buat pager manual jika tidak ada dari API
        $pager = null;
        if (isset($result['pagination'])) {
            $pager = \Config\Services::pager();
            $pager->setPath('payments'); // Set base URL untuk pagination
            
            // Simulasikan pager (sesuaikan dengan response API Anda)
            $pager->setPageCount($result['pagination']['total_pages'] ?? 1);
            $pager->setCurrentPage($result['pagination']['current_page'] ?? 1);
            $pager->setPerPage($result['pagination']['per_page'] ?? 10);
            $pager->setTotal($result['pagination']['total'] ?? count($payments));
        }

        return view('payments/index', [
            'title' => 'Daftar Pembayaran',
            'payments' => $payments,
            'pager' => $pager
        ]);

    } catch (\Exception $e) {
        return view('payments/index', [
            'title' => 'Daftar Pembayaran',
            'payments' => [],
            'pager' => null,
            'error' => 'Gagal memuat data pembayaran: ' . $e->getMessage()
        ]);
    }
}

    // Form Pembayaran Baru
    public function new($bookingId)
    {
        $client = \Config\Services::curlrequest();
        
        try {
            // Get booking details
            $bookingResponse = $client->get(getenv('API_BASE_URL') . '/api/bookings/' . $bookingId, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);

            $booking = json_decode($bookingResponse->getBody(), true);

            return view('payments/new', [
                'title' => 'Pembayaran Baru',
                'booking' => $booking['data'],
                'paymentMethods' => [
                    'transfer' => 'Transfer Bank',
                    'cash' => 'Tunai',
                    'qris' => 'QRIS',
                    'credit_card' => 'Kartu Kredit'
                ],
                'banks' => [
                    'BCA' => 'Bank Central Asia (BCA)',
                    'BRI' => 'Bank Rakyat Indonesia (BRI)',
                    'Mandiri' => 'Bank Mandiri',
                    'BNI' => 'Bank Negara Indonesia (BNI)'
                ]
            ]);

        } catch (\Exception $e) {
            $errorMsg = 'Gagal memuat data pemesanan: ' . $e->getMessage();
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $errorMsg
                ]);
            }
            
            return view('payments/new', [
                'title' => 'Pembayaran Baru',
                'error' => $errorMsg
            ]);
        }
    }

    // Proses Pembayaran
    public function create()
    {
        $validation = Services::validation();
        $validation->setRules([
            'booking_id' => 'required|numeric',
            'amount' => 'required|numeric',
            'payment_method' => 'required',
            'bank_name' => 'required_if[payment_method,transfer]',
            'account_number' => 'permit_empty',
            'receipt_image' => 'uploaded[receipt_image]|max_size[receipt_image,2048]|is_image[receipt_image]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => implode('<br>', $validation->getErrors())
            ]);
        }

        $client = \Config\Services::curlrequest();
        $receiptImage = $this->request->getFile('receipt_image');
        
        try {
            $data = [
                'booking_id' => $this->request->getPost('booking_id'),
                'amount' => $this->request->getPost('amount'),
                'payment_method' => $this->request->getPost('payment_method'),
                'bank_name' => $this->request->getPost('bank_name'),
                'account_number' => $this->request->getPost('account_number'),
                'notes' => $this->request->getPost('notes')
            ];

            $options = [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ],
                'multipart' => []
            ];

            // Add form data
            foreach ($data as $key => $value) {
                $options['multipart'][] = [
                    'name' => $key,
                    'contents' => $value
                ];
            }

            // Add image file
            if ($receiptImage->isValid() && !$receiptImage->hasMoved()) {
                $options['multipart'][] = [
                    'name' => 'receipt_image',
                    'contents' => fopen($receiptImage->getRealPath(), 'r'),
                    'filename' => $receiptImage->getName()
                ];
            }

            $response = $client->post($this->apiUrl, $options);
            $result = json_decode($response->getBody(), true);

            if ($response->getStatusCode() === 201) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Pembayaran berhasil disimpan',
                    'redirect' => '/payments/view/' . $result['data']['id']
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $result['message'] ?? 'Gagal menyimpan pembayaran'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    // Detail Pembayaran
    public function view($id)
    {
        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->get($this->apiUrl . '/' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);

            $payment = json_decode($response->getBody(), true);

            return view('payments/view', [
                'title' => 'Detail Pembayaran',
                'payment' => $payment['data']
            ]);

        } catch (\Exception $e) {
            $errorMsg = 'Gagal memuat detail pembayaran: ' . $e->getMessage();
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $errorMsg
                ]);
            }
            
            return view('payments/view', [
                'title' => 'Detail Pembayaran',
                'error' => $errorMsg
            ]);
        }
    }

    // Upload Bukti Pembayaran Tambahan
    public function uploadProof($paymentId)
    {
        $validation = Services::validation();
        $validation->setRules([
            'additional_proof' => 'uploaded[additional_proof]|max_size[additional_proof,2048]|is_image[additional_proof]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => implode('<br>', $validation->getErrors())
            ]);
        }

        $client = \Config\Services::curlrequest();
        $additionalProof = $this->request->getFile('additional_proof');
        
        try {
            $options = [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ],
                'multipart' => [
                    [
                        'name' => 'additional_proof',
                        'contents' => fopen($additionalProof->getRealPath(), 'r'),
                        'filename' => $additionalProof->getName()
                    ]
                ]
            ];

            $response = $client->post($this->apiUrl . '/' . $paymentId . '/additional-proof', $options);
            $result = json_decode($response->getBody(), true);

            if ($response->getStatusCode() === 200) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Bukti pembayaran tambahan berhasil diunggah',
                    'redirect' => '/payments/view/' . $paymentId
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $result['message'] ?? 'Gagal mengunggah bukti tambahan'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}