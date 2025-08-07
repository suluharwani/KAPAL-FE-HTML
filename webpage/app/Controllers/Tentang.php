<?php
namespace App\Controllers;

class Tentang extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Tentang Kami | Raja Ampat',
            'about' => [
                'description' => 'Raja Ampat Boat Services adalah penyedia layanan transportasi kapal terpercaya di Kepulauan Raja Ampat sejak tahun 2010.',
                'mission' => 'Menyediakan layanan transportasi yang aman, nyaman, dan terjangkau bagi wisatawan dan penduduk lokal.',
                'team' => [
                    [
                        'name' => 'John Doe',
                        'position' => 'Founder & CEO',
                        'image' => 'john.jpg'
                    ],
                    [
                        'name' => 'Jane Smith',
                        'position' => 'Operational Manager',
                        'image' => 'jane.jpg'
                    ]
                ]
            ]
        ];

        return view('tentang/index', $data);
    }
}