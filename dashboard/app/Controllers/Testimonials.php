<?php
// File: app/Controllers/Testimonials.php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Services;

class Testimonials extends BaseController
{
    protected $apiUrl;
    protected $session;

    public function __construct()
    {
        $this->apiUrl = getenv('API_BASE_URL') . '/api/testimonials';
        $this->session = session();
    }

    // List all testimonials
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

            return view('testimonials/index', [
                'title' => 'Testimoni',
                'testimonials' => $result['data'] ?? [],
                'pager' => $this->createPager($result['pagination'] ?? null)
            ]);

        } catch (\Exception $e) {
            return view('testimonials/index', [
                'title' => 'Testimoni',
                'testimonials' => [],
                'pager' => null,
                'error' => 'Gagal memuat testimoni: ' . $e->getMessage()
            ]);
        }
    }

    // Show approved testimonials
    public function approved()
    {
        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->get($this->apiUrl . '/approved', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);

            $result = json_decode($response->getBody(), true);

            return view('testimonials/approved', [
                'title' => 'Testimoni Disetujui',
                'testimonials' => $result['data'] ?? []
            ]);

        } catch (\Exception $e) {
            return view('testimonials/approved', [
                'title' => 'Testimoni Disetujui',
                'testimonials' => [],
                'error' => 'Gagal memuat testimoni: ' . $e->getMessage()
            ]);
        }
    }

    // Show form to create testimonial
    public function create()
    {
        return view('testimonials/create', [
            'title' => 'Buat Testimoni'
        ]);
    }

    // Process new testimonial
    public function store()
    {
        $validation = Services::validation();
        $validation->setRules([
            'content' => 'required|min_length[10]',
            'rating' => 'required|numeric|greater_than_equal_to[1]|less_than_equal_to[5]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => implode('<br>', $validation->getErrors())
            ]);
        }

        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->post($this->apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token'),
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'content' => $this->request->getPost('content'),
                    'rating' => $this->request->getPost('rating')
                ]
            ]);

            $result = json_decode($response->getBody(), true);

            if ($response->getStatusCode() === 201) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Testimoni berhasil dikirim. Menunggu persetujuan admin.',
                    'redirect' => '/testimonials'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $result['message'] ?? 'Gagal mengirim testimoni'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    // Update testimonial status (admin only)
    public function updateStatus($id)
    {
        if ($this->session->get('role') !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak. Hanya admin yang dapat mengubah status testimoni.'
            ]);
        }

        $validation = Services::validation();
        $validation->setRules([
            'status' => 'required|in_list[approved,rejected]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => implode('<br>', $validation->getErrors())
            ]);
        }

        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->put($this->apiUrl . '/' . $id . '/status', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token'),
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'status' => $this->request->getPost('status')
                ]
            ]);

            $result = json_decode($response->getBody(), true);

            if ($response->getStatusCode() === 200) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Status testimoni berhasil diperbarui',
                    'redirect' => '/testimonials'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $result['message'] ?? 'Gagal memperbarui status testimoni'
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
        $pager->setPath('testimonials');
        $pager->setPageCount($paginationData['total_pages'] ?? 1);
        $pager->setCurrentPage($paginationData['current_page'] ?? 1);
        $pager->setPerPage($paginationData['per_page'] ?? 10);
        $pager->setTotal($paginationData['total'] ?? 0);
        
        return $pager;
    }
}