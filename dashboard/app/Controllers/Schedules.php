<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Services;

class Schedules extends BaseController
{
    protected $apiUrl;
    protected $session;

    public function __construct()
    {
        $this->apiUrl = getenv('API_BASE_URL') . '/api/schedules';
        $this->session = session();
    }

    // Daftar Jadwal
    public function index()
    {
        $client = \Config\Services::curlrequest();
        
        try {
            $page = $this->request->getGet('page') ?? 1;
            $perPage = 10;
            
            $response = $client->get($this->apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ],
                'query' => [
                    'page' => $page,
                    'per_page' => $perPage,
                    'date_from' => date('Y-m-d'),
                    'sort' => 'departure_date,asc'
                ]
            ]);

            $result = json_decode($response->getBody(), true);
            $schedules = $result['data'] ?? [];
            
            // Buat pager
            $pager = service('pager', [
                'total'   => $result['pagination']['total'] ?? count($schedules),
                'perPage' => $result['pagination']['per_page'] ?? $perPage,
                'current' => $result['pagination']['current_page'] ?? $page,
                'path'    => 'schedules'
            ]);

            return view('schedules/index', [
                'title' => 'Daftar Jadwal',
                'schedules' => $schedules,
                'pager' => $pager
            ]);

        } catch (\Exception $e) {
            return $this->handleError('Gagal memuat data jadwal: ' . $e->getMessage());
        }
    }

    // Form Tambah Jadwal
    public function add()
    {
        try {
            $client = \Config\Services::curlrequest();
            
            // Ambil data kapal dan rute
            $boatsResponse = $client->get(getenv('API_BASE_URL') . '/api/boats', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);
            
            $routesResponse = $client->get(getenv('API_BASE_URL') . '/api/routes', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);
            
            $boats = json_decode($boatsResponse->getBody(), true)['data'] ?? [];
            $routes = json_decode($routesResponse->getBody(), true)['data'] ?? [];

            return view('schedules/add', [
                'title' => 'Tambah Jadwal Baru',
                'boats' => $boats,
                'routes' => $routes
            ]);

        } catch (\Exception $e) {
            return $this->handleError('Gagal memuat data: ' . $e->getMessage());
        }
    }

    // Simpan Jadwal Baru
    public function store()
    {
        $validation = Services::validation();
        $validation->setRules([
            'route_id' => 'required|numeric',
            'boat_id' => 'required|numeric',
            'departure_date' => 'required|valid_date',
            'departure_time' => 'required',
            'available_seats' => 'required|numeric|greater_than[0]',
            'status' => 'required|in_list[available,full,cancelled]'
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
                'route_id' => $this->request->getPost('route_id'),
                'boat_id' => $this->request->getPost('boat_id'),
                'departure_date' => $this->request->getPost('departure_date'),
                'departure_time' => $this->request->getPost('departure_time'),
                'available_seats' => $this->request->getPost('available_seats'),
                'status' => $this->request->getPost('status'),
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
                    'message' => 'Jadwal berhasil ditambahkan',
                    'redirect' => '/schedules/view/' . $result['data']['id']
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $result['message'] ?? 'Gagal menambahkan jadwal'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    // Form Edit Jadwal
    public function edit($id)
    {
        try {
            $client = \Config\Services::curlrequest();
            
            // Ambil data jadwal
            $scheduleResponse = $client->get($this->apiUrl . '/' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);
            
            // Ambil data kapal dan rute
            $boatsResponse = $client->get(getenv('API_BASE_URL') . '/api/boats', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);
            
            $routesResponse = $client->get(getenv('API_BASE_URL') . '/api/routes', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);
            
            $schedule = json_decode($scheduleResponse->getBody(), true)['data'] ?? [];
            $boats = json_decode($boatsResponse->getBody(), true)['data'] ?? [];
            $routes = json_decode($routesResponse->getBody(), true)['data'] ?? [];

            return view('schedules/edit', [
                'title' => 'Edit Jadwal',
                'schedule' => $schedule,
                'boats' => $boats,
                'routes' => $routes
            ]);

        } catch (\Exception $e) {
            return $this->handleError('Gagal memuat data jadwal: ' . $e->getMessage());
        }
    }

    // Update Jadwal
    public function update($id)
    {
        $validation = Services::validation();
        $validation->setRules([
            'route_id' => 'required|numeric',
            'boat_id' => 'required|numeric',
            'departure_date' => 'required|valid_date',
            'departure_time' => 'required',
            'available_seats' => 'required|numeric|greater_than[0]',
            'status' => 'required|in_list[available,full,cancelled]'
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
                'route_id' => $this->request->getPost('route_id'),
                'boat_id' => $this->request->getPost('boat_id'),
                'departure_date' => $this->request->getPost('departure_date'),
                'departure_time' => $this->request->getPost('departure_time'),
                'available_seats' => $this->request->getPost('available_seats'),
                'status' => $this->request->getPost('status'),
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
                    'message' => 'Jadwal berhasil diperbarui',
                    'redirect' => '/schedules/view/' . $id
                ]);
            } else {
                $error = json_decode($response->getBody(), true);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message'] ?? 'Gagal memperbarui jadwal'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    // Hapus Jadwal
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
                    'message' => 'Jadwal berhasil dihapus',
                    'redirect' => '/schedules'
                ]);
            } else {
                $error = json_decode($response->getBody(), true);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message'] ?? 'Gagal menghapus jadwal'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    // Detail Jadwal
    public function view($id)
    {
        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->get($this->apiUrl . '/' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);

            $schedule = json_decode($response->getBody(), true)['data'] ?? [];

            return view('schedules/view', [
                'title' => 'Detail Jadwal',
                'schedule' => $schedule
            ]);

        } catch (\Exception $e) {
            return $this->handleError('Gagal memuat detail jadwal: ' . $e->getMessage());
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
        
        return view('schedules/index', [
            'title' => 'Daftar Jadwal',
            'schedules' => [],
            'pager' => null,
            'error' => $message
        ]);
    }
}