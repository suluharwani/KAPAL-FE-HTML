<?php
// app/Controllers/Home.php
namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Home | Raja Ampat Boat Services',
            'featured_routes' => [
                [
                    'route' => 'Waigeo - Misool',
                    'schedule' => 'Senin, Rabu, Jumat (08:00 WIT)',
                    'duration' => '4-5 jam',
                    'price' => 'Rp 3.500.000'
                ],
                [
                    'route' => 'Waigeo - Salawati',
                    'schedule' => 'Setiap Hari (07:30 WIT)',
                    'duration' => '2-3 jam',
                    'price' => 'Rp 2.500.000'
                ],
                [
                    'route' => 'Misool - Batanta',
                    'schedule' => 'Selasa, Kamis, Sabtu (09:00 WIT)',
                    'duration' => '5-6 jam',
                    'price' => 'Rp 4.000.000'
                ]
            ],
            'features' => [
                [
                    'icon' => 'fas fa-ship',
                    'title' => 'Kapal Nyaman',
                    'description' => 'Kapal kami dilengkapi dengan perlengkapan keselamatan dan kenyamanan penumpang.'
                ],
                [
                    'icon' => 'fas fa-clock',
                    'title' => 'Tepat Waktu',
                    'description' => 'Jadwal keberangkatan yang teratur dan tepat waktu untuk kenyamanan perjalanan Anda.'
                ],
                [
                    'icon' => 'fas fa-shield-alt',
                    'title' => 'Aman Terpercaya',
                    'description' => 'Dilayani oleh awak kapal profesional dengan pengalaman bertahun-tahun.'
                ]
            ]
        ];

        return view('home/index', $data);
    }
}