<?php
// File: app/Controllers/Gallery.php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Services;

class Gallery extends BaseController
{
    protected $apiUrl;
    protected $session;

    public function __construct()
    {
        $this->apiUrl = getenv('API_BASE_URL') . '/api/gallery';
        $this->session = session();
    }

    // List all gallery items
    public function index()
    {
        $category = $this->request->getGet('category');
        $client = \Config\Services::curlrequest();
        
        try {
            $query = ['page' => $this->request->getGet('page') ?? 1];
            if ($category) {
                $query['category'] = $category;
            }

            // Get gallery items
            $response = $client->get($this->apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ],
                'query' => $query
            ]);

            // Get categories
            $categoriesResponse = $client->get($this->apiUrl . '/categories', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);

            $result = json_decode($response->getBody(), true);
            $categories = json_decode($categoriesResponse->getBody(), true)['data'] ?? [];

            return view('gallery/index', [
                'title' => 'Galeri',
                'galleryItems' => $result['data'] ?? [],
                'categories' => $categories,
                'selectedCategory' => $category,
                'pager' => $this->createPager($result['pagination'] ?? null)
            ]);

        } catch (\Exception $e) {
            return view('gallery/index', [
                'title' => 'Galeri',
                'galleryItems' => [],
                'categories' => [],
                'selectedCategory' => null,
                'pager' => null,
                'error' => 'Gagal memuat data galeri: ' . $e->getMessage()
            ]);
        }
    }

    // Show featured gallery items
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

            return view('gallery/featured', [
                'title' => 'Galeri Unggulan',
                'featuredItems' => $result['data'] ?? []
            ]);

        } catch (\Exception $e) {
            return view('gallery/featured', [
                'title' => 'Galeri Unggulan',
                'featuredItems' => [],
                'error' => 'Gagal memuat galeri unggulan: ' . $e->getMessage()
            ]);
        }
    }

    // Show form to add new gallery item (admin only)
    public function add()
    {
        if ($this->session->get('role') !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya admin yang dapat menambah item galeri.');
        }

        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->get($this->apiUrl . '/categories', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);

            $categories = json_decode($response->getBody(), true)['data'] ?? [];

            return view('gallery/add', [
                'title' => 'Tambah Item Galeri',
                'categories' => $categories
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat kategori: ' . $e->getMessage());
        }
    }

    // Process new gallery item (admin only)
    public function store()
    {
        if ($this->session->get('role') !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak. Hanya admin yang dapat menambah item galeri.'
            ]);
        }

        $validation = Services::validation();
        $validation->setRules([
            'title' => 'required',
            'category' => 'required',
            'description' => 'required',
            'is_featured' => 'permit_empty|in_list[0,1]',
            'image' => 'uploaded[image]|max_size[image,2048]|is_image[image]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => implode('<br>', $validation->getErrors())
            ]);
        }

        $client = \Config\Services::curlrequest();
        $image = $this->request->getFile('image');
        
        try {
            $options = [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ],
                'multipart' => [
                    [
                        'name' => 'title',
                        'contents' => $this->request->getPost('title')
                    ],
                    [
                        'name' => 'category',
                        'contents' => $this->request->getPost('category')
                    ],
                    [
                        'name' => 'description',
                        'contents' => $this->request->getPost('description')
                    ],
                    [
                        'name' => 'is_featured',
                        'contents' => $this->request->getPost('is_featured') ?? 0
                    ],
                    [
                        'name' => 'image',
                        'contents' => fopen($image->getRealPath(), 'r'),
                        'filename' => $image->getName()
                    ]
                ]
            ];

            $response = $client->post($this->apiUrl, $options);
            $result = json_decode($response->getBody(), true);

            if ($response->getStatusCode() === 201) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Item galeri berhasil ditambahkan',
                    'redirect' => '/gallery'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $result['message'] ?? 'Gagal menambahkan item galeri'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    // Delete gallery item (admin only)
    public function delete($id)
    {
        if ($this->session->get('role') !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya admin yang dapat menghapus item galeri.');
        }

        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->delete($this->apiUrl . '/' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);

            if ($response->getStatusCode() === 204) {
                return redirect()->to('/gallery')->with('success', 'Item galeri berhasil dihapus');
            } else {
                $result = json_decode($response->getBody(), true);
                return redirect()->back()->with('error', $result['message'] ?? 'Gagal menghapus item galeri');
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Helper method to create pager
    private function createPager($paginationData)
    {
        if (!$paginationData) return null;
        
        $pager = \Config\Services::pager();
        $pager->setPath('gallery');
        $pager->setPageCount($paginationData['total_pages'] ?? 1);
        $pager->setCurrentPage($paginationData['current_page'] ?? 1);
        $pager->setPerPage($paginationData['per_page'] ?? 10);
        $pager->setTotal($paginationData['total'] ?? 0);
        
        return $pager;
    }
}