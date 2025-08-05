<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Services;

class Routes extends BaseController
{
    protected $apiUrl;
    protected $session;

    public function __construct()
    {
        $this->apiUrl = getenv('API_BASE_URL') . '/api/routes';
        $this->session = session();
    }

    // Daftar Rute
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
            $routes = $result['data'] ?? [];
            
            // Siapkan pager
            $pager = null;
            if (isset($result['pagination'])) {
                $pager = \Config\Services::pager();
                $pager->setPath('routes');

            }

            return view('routes/index', [
                'title' => 'Daftar Rute',
                'routes' => $routes,
                'pager' => $pager
            ]);

        } catch (\Exception $e) {
            return $this->handleError('Gagal memuat data rute: ' . $e->getMessage());
        }
    }

    // Form Tambah Rute
    public function add()
    {
        try {
            $client = \Config\Services::curlrequest();
            
            // Ambil daftar pulau
            $islandsResponse = $client->get(getenv('API_BASE_URL') . '/api/islands', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);
            
            $islands = json_decode($islandsResponse->getBody(), true)['data'] ?? [];

            return view('routes/add', [
                'title' => 'Tambah Rute Baru',
                'islands' => $islands
            ]);

        } catch (\Exception $e) {
            return $this->handleError('Gagal memuat data pulau: ' . $e->getMessage());
        }
    }

    // Simpan Rute Baru
    public function store()
    {
        $validation = Services::validation();
        $validation->setRules([
            'departure_island_id' => 'required|numeric',
            'arrival_island_id' => 'required|numeric|different[departure_island_id]',
            'estimated_duration' => 'required',
            'distance' => 'required|numeric',
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
            $data = [
                'departure_island_id' => $this->request->getPost('departure_island_id'),
                'arrival_island_id' => $this->request->getPost('arrival_island_id'),
                'estimated_duration' => $this->request->getPost('estimated_duration'),
                'distance' => $this->request->getPost('distance'),
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
                    'message' => 'Rute berhasil ditambahkan',
                    'redirect' => '/routes/view/' . $result['data']['id']
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $result['message'] ?? 'Gagal menambahkan rute'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    // Form Edit Rute
    public function edit($id)
    {
        try {
            $client = \Config\Services::curlrequest();
            
            // Ambil data rute
            $routeResponse = $client->get($this->apiUrl . '/' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);
            
            $route = json_decode($routeResponse->getBody(), true)['data'] ?? [];
            
            // Ambil daftar pulau
            $islandsResponse = $client->get(getenv('API_BASE_URL') . '/api/islands', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);
            
            $islands = json_decode($islandsResponse->getBody(), true)['data'] ?? [];

            return view('routes/edit', [
                'title' => 'Edit Rute',
                'route' => $route,
                'islands' => $islands
            ]);

        } catch (\Exception $e) {
            return $this->handleError('Gagal memuat data rute: ' . $e->getMessage());
        }
    }

    // Update Rute
    public function update($id)
    {
        $validation = Services::validation();
        $validation->setRules([
            'departure_island_id' => 'required|numeric',
            'arrival_island_id' => 'required|numeric|different[departure_island_id]',
            'estimated_duration' => 'required',
            'distance' => 'required|numeric',
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
            $data = [
                'departure_island_id' => $this->request->getPost('departure_island_id'),
                'arrival_island_id' => $this->request->getPost('arrival_island_id'),
                'estimated_duration' => $this->request->getPost('estimated_duration'),
                'distance' => $this->request->getPost('distance'),
                'notes' => $this->request->getPost('notes')
            ];

            $response = $client->put($this->apiUrl . '/' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token'),
                    'Content-Type' => 'application/json'
                ],
                'json' => $data
            ]);

            if ($response->getStatusCode() === 200) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Rute berhasil diperbarui',
                    'redirect' => '/routes/view/' . $id
                ]);
            } else {
                $error = json_decode($response->getBody(), true);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message'] ?? 'Gagal memperbarui rute'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    // Hapus Rute
    public function delete($id)
    {
        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->delete($this->apiUrl . '/' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);

            if ($response->getStatusCode() === 200) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Rute berhasil dihapus',
                    'redirect' => '/routes'
                ]);
            } else {
                $error = json_decode($response->getBody(), true);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message'] ?? 'Gagal menghapus rute'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    // Detail Rute
    public function view($id)
    {
        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->get($this->apiUrl . '/' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);

            $route = json_decode($response->getBody(), true)['data'] ?? [];

            return view('routes/view', [
                'title' => 'Detail Rute',
                'route' => $route
            ]);

        } catch (\Exception $e) {
            return $this->handleError('Gagal memuat detail rute: ' . $e->getMessage());
        }
    }

    // Helper untuk handle error
    private function handleError($message)
    {
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $message
            ]);
        }
        
        // Untuk non-AJAX, tampilkan error di view
        return view('routes/index', [
            'title' => 'Daftar Rute',
            'routes' => [],
            'pager' => null,
            'error' => $message
        ]);
    }
}