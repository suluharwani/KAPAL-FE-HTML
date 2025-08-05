<?php
// File: app/Controllers/OpenTrip.php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Services;

class OpenTrip extends BaseController
{
    protected $apiUrl;
    protected $session;

    public function __construct()
    {
        $this->apiUrl = getenv('API_BASE_URL') . '/api/open-trips';
        $this->session = session();
    }

    // List all open trip requests
    public function index()
    {
        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->get($this->apiUrl . '/available', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ],
                'query' => [
                    'page' => $this->request->getGet('page') ?? 1,
                    'per_page' => 10
                ]
            ]);

            $result = json_decode($response->getBody(), true);
            $openTrips = $result['data'] ?? [];
            
            return view('open-trip/index', [
                'title' => 'Daftar Open Trip Tersedia',
                'openTrips' => $openTrips,
                'pager' => $this->createPager($result['pagination'] ?? null)
            ]);

        } catch (\Exception $e) {
            return view('open-trip/index', [
                'title' => 'Daftar Open Trip Tersedia',
                'openTrips' => [],
                'pager' => null,
                'error' => 'Gagal memuat data open trip: ' . $e->getMessage()
            ]);
        }
    }

    // Show form to create open trip request
    public function requestForm()
    {
        $client = \Config\Services::curlrequest();
        
        try {
            // Get boats and routes for dropdowns
            $boatsResponse = $client->get(getenv('API_BASE_URL') . '/api/boats', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);
            $boats = json_decode($boatsResponse->getBody(), true)['data'] ?? [];

            $routesResponse = $client->get(getenv('API_BASE_URL') . '/api/routes', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);
            $routes = json_decode($routesResponse->getBody(), true)['data'] ?? [];

            return view('open-trip/request', [
                'title' => 'Buat Permintaan Open Trip',
                'boats' => $boats,
                'routes' => $routes
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat data: ' . $e->getMessage());
        }
    }

    // Process open trip request
    public function submitRequest()
    {
        $validation = Services::validation();
        $validation->setRules([
            'boat_id' => 'required|numeric',
            'route_id' => 'required|numeric',
            'proposed_date' => 'required|valid_date',
            'proposed_time' => 'required',
            'min_passengers' => 'required|numeric|greater_than[0]',
            'max_passengers' => 'required|numeric|greater_than[min_passengers]',
            'notes' => 'permit_empty'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => implode('<br>', $validation->getErrors())
            ]);
        }

        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->post($this->apiUrl . '/request', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token'),
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'boat_id' => $this->request->getPost('boat_id'),
                    'route_id' => $this->request->getPost('route_id'),
                    'proposed_date' => $this->request->getPost('proposed_date'),
                    'proposed_time' => $this->request->getPost('proposed_time'),
                    'min_passengers' => $this->request->getPost('min_passengers'),
                    'max_passengers' => $this->request->getPost('max_passengers'),
                    'notes' => $this->request->getPost('notes')
                ]
            ]);

            $result = json_decode($response->getBody(), true);

            if ($response->getStatusCode() === 201) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Permintaan open trip berhasil diajukan',
                    'redirect' => '/open-trip/view/' . $result['data']['id']
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $result['message'] ?? 'Gagal mengajukan permintaan open trip'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    // View open trip details
    public function view($id)
    {
        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->get($this->apiUrl . '/' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);

            $openTrip = json_decode($response->getBody(), true);

            return view('open-trip/view', [
                'title' => 'Detail Open Trip',
                'openTrip' => $openTrip['data']
            ]);

        } catch (\Exception $e) {
            return redirect()->to('/open-trip')->with('error', 'Gagal memuat detail open trip: ' . $e->getMessage());
        }
    }

    // Join an open trip
    public function join($id)
    {
        $validation = Services::validation();
        $validation->setRules([
            'passenger_count' => 'required|numeric|greater_than[0]',
            'passengers' => 'required'
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
            
            $response = $client->post($this->apiUrl . '/' . $id . '/join', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token'),
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'passenger_count' => $this->request->getPost('passenger_count'),
                    'passengers' => $passengers
                ]
            ]);

            $result = json_decode($response->getBody(), true);

            if ($response->getStatusCode() === 201) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Berhasil bergabung dengan open trip',
                    'redirect' => '/bookings/view/' . $result['data']['booking_id']
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $result['message'] ?? 'Gagal bergabung dengan open trip'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    // Approve/reject open trip request (admin only)
    public function approveRequest($id)
    {
        if ($this->session->get('role') !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak. Hanya admin yang dapat menyetujui permintaan.'
            ]);
        }

        $validation = Services::validation();
        $validation->setRules([
            'status' => 'required|in_list[approved,rejected]',
            'admin_notes' => 'permit_empty'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => implode('<br>', $validation->getErrors())
            ]);
        }

        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->put($this->apiUrl . '/request/' . $id . '/approve', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token'),
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'status' => $this->request->getPost('status'),
                    'admin_notes' => $this->request->getPost('admin_notes')
                ]
            ]);

            $result = json_decode($response->getBody(), true);

            if ($response->getStatusCode() === 200) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Status permintaan berhasil diperbarui',
                    'redirect' => '/open-trip/view/' . $id
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $result['message'] ?? 'Gagal memperbarui status permintaan'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    // Helper method to create pager
    private function createPager($paginationData)
    {
        if (!$paginationData) return null;
        
        $pager = \Config\Services::pager();
        $pager->setPath('open-trip');
        $pager->setPageCount($paginationData['total_pages'] ?? 1);
        $pager->setCurrentPage($paginationData['current_page'] ?? 1);
        $pager->setPerPage($paginationData['per_page'] ?? 10);
        $pager->setTotal($paginationData['total'] ?? 0);
        
        return $pager;
    }
}