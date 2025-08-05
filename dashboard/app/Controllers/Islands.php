<?php
// File: app/Controllers/Islands.php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Services;

class Islands extends BaseController
{
    protected $apiUrl;
    protected $session;

    public function __construct()
    {
        $this->apiUrl = getenv('API_BASE_URL') . '/api/islands';
        $this->session = session();
    }

    // List all islands
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
            $islands = $result['data'] ?? [];
            
            // Create manual pager if not from API
            $pager = null;
            if (isset($result['pagination'])) {
                $pager = \Config\Services::pager();
                $pager->setPath('islands');
                $pager->setPageCount($result['pagination']['total_pages'] ?? 1);
                $pager->setCurrentPage($result['pagination']['current_page'] ?? 1);
                $pager->setPerPage($result['pagination']['per_page'] ?? 10);
                $pager->setTotal($result['pagination']['total'] ?? count($islands));
            }

            return view('islands/index', [
                'title' => 'Daftar Pulau',
                'islands' => $islands,
                'pager' => $pager
            ]);

        } catch (\Exception $e) {
            return view('islands/index', [
                'title' => 'Daftar Pulau',
                'islands' => [],
                'pager' => null,
                'error' => 'Gagal memuat data pulau: ' . $e->getMessage()
            ]);
        }
    }

    // Show add island form
    public function add()
    {
        if ($this->session->get('role') !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya admin yang dapat menambah pulau.');
        }

        return view('islands/add', [
            'title' => 'Tambah Pulau Baru'
        ]);
    }

    // Process island creation
    public function store()
    {
        if ($this->session->get('role') !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak. Hanya admin yang dapat menambah pulau.'
            ]);
        }

        $validation = Services::validation();
        $validation->setRules([
            'island_name' => 'required',
            'description' => 'required',
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
                        'name' => 'island_name',
                        'contents' => $this->request->getPost('island_name')
                    ],
                    [
                        'name' => 'description',
                        'contents' => $this->request->getPost('description')
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
                    'message' => 'Pulau berhasil ditambahkan',
                    'redirect' => '/islands/view/' . $result['data']['id']
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $result['message'] ?? 'Gagal menambahkan pulau'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    // Show island details
    public function view($id)
    {
        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->get($this->apiUrl . '/' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);

            $island = json_decode($response->getBody(), true);

            return view('islands/view', [
                'title' => 'Detail Pulau',
                'island' => $island['data']
            ]);

        } catch (\Exception $e) {
            return redirect()->to('/islands')->with('error', 'Gagal memuat detail pulau: ' . $e->getMessage());
        }
    }

    // Show edit island form
    public function edit($id)
    {
        if ($this->session->get('role') !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya admin yang dapat mengedit pulau.');
        }

        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->get($this->apiUrl . '/' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);

            $island = json_decode($response->getBody(), true);

            return view('islands/edit', [
                'title' => 'Edit Pulau',
                'island' => $island['data']
            ]);

        } catch (\Exception $e) {
            return redirect()->to('/islands')->with('error', 'Gagal memuat data pulau: ' . $e->getMessage());
        }
    }

    // Process island update
    public function update($id)
    {
        if ($this->session->get('role') !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak. Hanya admin yang dapat mengedit pulau.'
            ]);
        }

        $validation = Services::validation();
        $validation->setRules([
            'island_name' => 'required',
            'description' => 'required',
            'image' => 'max_size[image,2048]|is_image[image]'
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
            $data = [
                'island_name' => $this->request->getPost('island_name'),
                'description' => $this->request->getPost('description')
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

            // Add image file if uploaded
            if ($image && $image->isValid() && !$image->hasMoved()) {
                $options['multipart'][] = [
                    'name' => 'image',
                    'contents' => fopen($image->getRealPath(), 'r'),
                    'filename' => $image->getName()
                ];
            }

            $response = $client->put($this->apiUrl . '/' . $id, $options);
            $result = json_decode($response->getBody(), true);

            if ($response->getStatusCode() === 200) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Pulau berhasil diperbarui',
                    'redirect' => '/islands/view/' . $id
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $result['message'] ?? 'Gagal memperbarui pulau'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    // Delete island
    public function delete($id)
    {
        if ($this->session->get('role') !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya admin yang dapat menghapus pulau.');
        }

        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->delete($this->apiUrl . '/' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);

            if ($response->getStatusCode() === 204) {
                return redirect()->to('/islands')->with('success', 'Pulau berhasil dihapus');
            } else {
                $result = json_decode($response->getBody(), true);
                return redirect()->back()->with('error', $result['message'] ?? 'Gagal menghapus pulau');
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}