<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Services;

class Bookings extends BaseController
{
    protected $apiUrl;
    protected $session;

    public function __construct()
    {
        $this->apiUrl = getenv('API_BASE_URL') . '/api/bookings';
        $this->session = session();
    }

    // Daftar Pemesanan
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

            $bookings = json_decode($response->getBody(), true);

            return view('bookings/index', [
                'title' => 'Daftar Pemesanan',
                'bookings' => $bookings['data'] ?? [],
                'pager' => $bookings['pagination'] ?? null
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat data pemesanan: ' . $e->getMessage());
        }
    }

    // Form Pemesanan Baru
    public function new()
    {
        $client = \Config\Services::curlrequest();
        
        try {
            // Ambil data kapal dan jadwal
            $schedulesResponse = $client->get(getenv('API_BASE_URL') . '/api/schedules', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);

            $schedules = json_decode($schedulesResponse->getBody(), true);

            return view('bookings/new', [
                'title' => 'Pemesanan Baru',
                'schedules' => $schedules['data'] ?? []
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat data jadwal: ' . $e->getMessage());
        }
    }

    // Proses Pemesanan
public function create()
{
    $validation = Services::validation();
    $validation->setRules([
        'schedule_id' => 'required|numeric',
        'passenger_count' => 'required|numeric|greater_than[0]',
        'passengers' => 'required',
        'payment_method' => 'required'
    ]);

    if (!$validation->withRequest($this->request)->run()) {
        return $this->response->setJSON([
            'success' => false,
            'message' => implode('<br>', $validation->getErrors())
        ]);
    }

    $client = \Config\Services::curlrequest();
    
    try {
        $passengers = json_decode($this->request->getPost('passengers'), true);
        
        $data = [
            'schedule_id' => $this->request->getPost('schedule_id'),
            'passenger_count' => $this->request->getPost('passenger_count'),
            'passengers' => $passengers,
            'payment_method' => $this->request->getPost('payment_method'),
            'notes' => $this->request->getPost('notes')
        ];

        $response = $client->post($this->apiUrl, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->session->get('token'),
                'Content-Type' => 'application/json'
            ],
            'json' => $data
        ]);

        $result = json_decode($response->getBody(), true);

        if ($response->getStatusCode() === 201) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pemesanan berhasil dibuat',
                'redirect' => '/bookings/view/' . $result['data']['id']
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => $result['message'] ?? 'Gagal membuat pemesanan'
            ]);
        }

    } catch (\Exception $e) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ]);
    }
}

    // Detail Pemesanan
    public function view($id)
    {
        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->get($this->apiUrl . '/' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);

            $booking = json_decode($response->getBody(), true);

            return view('bookings/view', [
                'title' => 'Detail Pemesanan',
                'booking' => $booking['data']
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat detail pemesanan: ' . $e->getMessage());
        }
    }

    // Batalkan Pemesanan
   public function cancel($id)
{
    $client = \Config\Services::curlrequest();
    
    try {
        $response = $client->post($this->apiUrl . '/' . $id . '/cancel', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->session->get('token')
            ]
        ]);

        if ($response->getStatusCode() === 200) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pemesanan berhasil dibatalkan',
                'redirect' => '/bookings/view/' . $id
            ]);
        } else {
            $error = json_decode($response->getBody(), true);
            return $this->response->setJSON([
                'success' => false,
                'message' => $error['message'] ?? 'Gagal membatalkan pemesanan'
            ]);
        }

    } catch (\Exception $e) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ]);
    }
}

    // Invoice Pemesanan
    public function invoice($id)
    {
        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->get($this->apiUrl . '/' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);

            $booking = json_decode($response->getBody(), true);

            // Generate PDF invoice
            $dompdf = new \Dompdf\Dompdf();
            $html = view('bookings/invoice', ['booking' => $booking['data']]);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            $dompdf->stream('invoice-' . $booking['data']['id'] . '.pdf', ['Attachment' => 0]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat invoice: ' . $e->getMessage());
        }
    }
}