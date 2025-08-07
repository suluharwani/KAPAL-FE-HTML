<?php
namespace App\Controllers;

class Wisata extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Daftar Wisata | Raja Ampat',
            'destinations' => [
                [
                    'name' => 'Pianemo',
                    'image' => 'pianemo.jpg',
                    'description' => 'Puncak dengan pemandangan gugusan pulau karst yang menakjubkan'
                ],
                [
                    'name' => 'Pasir Timbul',
                    'image' => 'pasir-timbul.jpg',
                    'description' => 'Pulau pasir putih yang muncul saat air laut surut'
                ],
                [
                    'name' => 'Wayag',
                    'image' => 'wayag.jpg',
                    'description' => 'Gugusan pulau karst dengan pemandangan spektakuler'
                ]
            ]
        ];

        return view('wisata/index', $data);
    }
}