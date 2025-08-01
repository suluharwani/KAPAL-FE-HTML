<?php namespace App\Controllers;

class Admin extends BaseController
{
    public function __construct()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
    }

    public function dashboard()
    {
        $data = [
            'title' => 'Dashboard Admin'
        ];
        return view('admin/dashboard', $data);
    }

    public function boats()
    {
        // Ambil data boats dari API
        $apiUrl = getenv('API_BASE_URL') . '/api/boats?page=1&per_page=10';
        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->get($apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . session()->get('token')
                ]
            ]);
            
            $data = [
                'title' => 'Manajemen Kapal',
                'boats' => json_decode($response->getBody(), true)['data']
            ];
            
            return view('admin/boats', $data);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengambil data: ' . $e->getMessage());
        }
    }

    // Method untuk halaman lainnya (bookings, payments, routes, dll) dengan pola yang sama
    // ...
}