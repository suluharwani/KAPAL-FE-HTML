<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Services;

class Boats extends BaseController
{
    protected $apiUrl;
    protected $session;

    public function __construct()
    {
        $this->apiUrl = getenv('API_BASE_URL') . '/api/boats';
        $this->session = session();
    }

    // Daftar Kapal
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

            $boats = json_decode($response->getBody(), true);

            return view('boats/index', [
                'title' => 'Daftar Kapal',
                'boats' => $boats['data'] ?? [],
            ]);

        } catch (\Exception $e) {
           return view('boats/index', [
                'title' => 'Daftar Kapal',
                'boats' => $boats['data'] ?? [],
            ]);
        }
    }

    // Form Tambah Kapal
    public function add()
    {
        return view('boats/add', [
            'title' => 'Tambah Kapal Baru',
            'token' => session()->get('token')
        ]);
    }

    // Simpan Kapal Baru
public function store()
{
    $validation = Services::validation();
    $validation->setRules([
        'boat_name' => 'required',
        'boat_type' => 'required',
        'capacity' => 'required|numeric',
        'price_per_trip' => 'required|numeric',
        'description' => 'permit_empty',
        'image' => 'uploaded[image]|max_size[image,2048]|is_image[image]'
    ]);

    if (!$validation->withRequest($this->request)->run()) {
        return redirect()->back()->withInput()->with('errors', $validation->getErrors());
    }

    $client = \Config\Services::curlrequest();
    $image = $this->request->getFile('image');

    try {
        // Prepare multipart form data
        $multipart = [
            [
                'name' => 'boat_name',
                'contents' => $this->request->getPost('boat_name')
            ],
            [
                'name' => 'boat_type',
                'contents' => $this->request->getPost('boat_type')
            ],
            [
                'name' => 'capacity',
                'contents' => $this->request->getPost('capacity')
            ],
            [
                'name' => 'price_per_trip',
                'contents' => $this->request->getPost('price_per_trip')
            ],
            [
                'name' => 'description',
                'contents' => $this->request->getPost('description') ?? ''
            ]
        ];

        // Add image file if uploaded
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $multipart[] = [
                'name' => 'image',
                'contents' => fopen($image->getRealPath(), 'r'),
                'filename' => $image->getName()
            ];
        }

        $response = $client->post($this->apiUrl, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->session->get('token')
            ],
            'multipart' => $multipart
        ]);

        $responseData = json_decode($response->getBody(), true);

        if ($response->getStatusCode() === 201) {
            return redirect()->to('/boats')->with('success', 'Kapal berhasil ditambahkan');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', $responseData['message'] ?? 'Gagal menambahkan kapal');
        }

    } catch (\Exception $e) {
        // Log the error for debugging
        log_message('error', 'Boat store error: ' . $e->getMessage());
        
        return redirect()->back()
            ->withInput()
            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

    // Form Edit Kapal
    public function edit($id)
    {
        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->get($this->apiUrl . '/' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);

            $boat = json_decode($response->getBody(), true);

            return view('boats/edit', [
                'title' => 'Edit Kapal',
                'boat' => $boat['data']
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat data kapal: ' . $e->getMessage());
        }
    }

    // Update Kapal
    public function update($id)
    {
        $validation = Services::validation();
        $validation->setRules([
            'boat_name' => 'required',
            'boat_type' => 'required',
            'capacity' => 'required|numeric',
            'price_per_trip' => 'required|numeric',
            'description' => 'permit_empty'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $client = \Config\Services::curlrequest();
        $image = $this->request->getFile('image');

        try {
            $data = [
                'boat_name' => $this->request->getPost('boat_name'),
                'boat_type' => $this->request->getPost('boat_type'),
                'capacity' => $this->request->getPost('capacity'),
                'price_per_trip' => $this->request->getPost('price_per_trip'),
                'description' => $this->request->getPost('description')
            ];

            $options = [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ],
                'json' => $data
            ];

            $response = $client->put($this->apiUrl . '/' . $id, $options);

            if ($response->getStatusCode() === 200) {
                return redirect()->to('/boats')->with('success', 'Data kapal berhasil diperbarui');
            } else {
                $error = json_decode($response->getBody(), true);
                return redirect()->back()->withInput()->with('error', $error['message'] ?? 'Gagal memperbarui kapal');
            }

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Hapus Kapal
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
                return redirect()->to('/boats')->with('success', 'Kapal berhasil dihapus');
            } else {
                $error = json_decode($response->getBody(), true);
                return redirect()->back()->with('error', $error['message'] ?? 'Gagal menghapus kapal');
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Detail Kapal
    public function view($id)
    {
        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->get($this->apiUrl . '/' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);

            $boat = json_decode($response->getBody(), true);

            return view('boats/view', [
                'title' => 'Detail Kapal',
                'boat' => $boat['data']
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}