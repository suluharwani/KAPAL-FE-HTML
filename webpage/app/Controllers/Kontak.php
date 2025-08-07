<?php
namespace App\Controllers;

class Kontak extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Kontak Kami | Raja Ampat',
            'contact_info' => [
                'address' => 'Jl. Raya Waigeo, Raja Ampat, Papua Barat',
                'phone' => '+62 812-3456-7890',
                'email' => 'info@rajaampatboats.com',
                'hours' => 'Senin - Jumat: 08:00 - 17:00 WIT'
            ]
        ];

        return view('kontak/index', $data);
    }

    public function submit()
    {
        // Proses form kontak
        $validation = \Config\Services::validation();
        
        $rules = [
            'name' => 'required',
            'email' => 'required|valid_email',
            'message' => 'required|min_length[10]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Simpan pesan atau kirim email
        // ...

        return redirect()->to('/kontak')->with('success', 'Pesan Anda telah terkirim!');
    }
}