<?php
// File: app/Controllers/Faq.php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Services;

class Faq extends BaseController
{
    protected $apiUrl;
    protected $session;

    public function __construct()
    {
        $this->apiUrl = getenv('API_BASE_URL') . '/api/faqs';
        $this->session = session();
    }

    // List all FAQs
    public function index()
    {
        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->get($this->apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);

            $result = json_decode($response->getBody(), true);

            return view('faq/index', [
                'title' => 'FAQ',
                'faqs' => $result['data'] ?? [],
                'categories' => $this->getCategories()
            ]);

        } catch (\Exception $e) {
            return view('faq/index', [
                'title' => 'FAQ',
                'faqs' => [],
                'categories' => [],
                'error' => 'Gagal memuat FAQ: ' . $e->getMessage()
            ]);
        }
    }

    // Show featured FAQs
    public function featured()
    {
        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->get($this->apiUrl . '/featured', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);

            $result = json_decode($response->getBody(), true);

            return view('faq/featured', [
                'title' => 'FAQ Unggulan',
                'featuredFaqs' => $result['data'] ?? []
            ]);

        } catch (\Exception $e) {
            return view('faq/featured', [
                'title' => 'FAQ Unggulan',
                'featuredFaqs' => [],
                'error' => 'Gagal memuat FAQ unggulan: ' . $e->getMessage()
            ]);
        }
    }

    // Show form to add new FAQ (admin only)
    public function add()
    {
        if ($this->session->get('role') !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya admin yang dapat menambah FAQ.');
        }

        return view('faq/add', [
            'title' => 'Tambah FAQ',
            'categories' => $this->getCategories()
        ]);
    }

    // Process new FAQ (admin only)
    public function store()
    {
        if ($this->session->get('role') !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak. Hanya admin yang dapat menambah FAQ.'
            ]);
        }

        $validation = Services::validation();
        $validation->setRules([
            'question' => 'required',
            'answer' => 'required',
            'category' => 'required',
            'is_featured' => 'permit_empty|in_list[0,1]'
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
                    'question' => $this->request->getPost('question'),
                    'answer' => $this->request->getPost('answer'),
                    'category' => $this->request->getPost('category'),
                    'is_featured' => $this->request->getPost('is_featured') ?? 0
                ]
            ]);

            $result = json_decode($response->getBody(), true);

            if ($response->getStatusCode() === 201) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'FAQ berhasil ditambahkan',
                    'redirect' => '/faq'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $result['message'] ?? 'Gagal menambahkan FAQ'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    // Show form to edit FAQ (admin only)
    public function edit($id)
    {
        if ($this->session->get('role') !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya admin yang dapat mengedit FAQ.');
        }

        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->get($this->apiUrl . '/' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);

            $faq = json_decode($response->getBody(), true);

            return view('faq/edit', [
                'title' => 'Edit FAQ',
                'faq' => $faq['data'],
                'categories' => $this->getCategories()
            ]);

        } catch (\Exception $e) {
            return redirect()->to('/faq')->with('error', 'Gagal memuat data FAQ: ' . $e->getMessage());
        }
    }

    // Process FAQ update (admin only)
    public function update($id)
    {
        if ($this->session->get('role') !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak. Hanya admin yang dapat mengedit FAQ.'
            ]);
        }

        $validation = Services::validation();
        $validation->setRules([
            'question' => 'required',
            'answer' => 'required',
            'category' => 'required',
            'is_featured' => 'permit_empty|in_list[0,1]',
            'display_order' => 'permit_empty|numeric'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => implode('<br>', $validation->getErrors())
            ]);
        }

        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->put($this->apiUrl . '/' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token'),
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'question' => $this->request->getPost('question'),
                    'answer' => $this->request->getPost('answer'),
                    'category' => $this->request->getPost('category'),
                    'is_featured' => $this->request->getPost('is_featured') ?? 0,
                    'display_order' => $this->request->getPost('display_order')
                ]
            ]);

            $result = json_decode($response->getBody(), true);

            if ($response->getStatusCode() === 200) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'FAQ berhasil diperbarui',
                    'redirect' => '/faq'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $result['message'] ?? 'Gagal memperbarui FAQ'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    // Delete FAQ (admin only)
    public function delete($id)
    {
        if ($this->session->get('role') !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya admin yang dapat menghapus FAQ.');
        }

        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->delete($this->apiUrl . '/' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);

            if ($response->getStatusCode() === 204) {
                return redirect()->to('/faq')->with('success', 'FAQ berhasil dihapus');
            } else {
                $result = json_decode($response->getBody(), true);
                return redirect()->back()->with('error', $result['message'] ?? 'Gagal menghapus FAQ');
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Helper method to get FAQ categories
    private function getCategories()
    {
        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->get($this->apiUrl . '/categories', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);

            $result = json_decode($response->getBody(), true);
            return $result['data'] ?? [];

        } catch (\Exception $e) {
            return [];
        }
    }
}