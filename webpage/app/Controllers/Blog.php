<?php
namespace App\Controllers;

class Blog extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Blog & Artikel | Raja Ampat',
            'posts' => [
                [
                    'title' => 'Tips Berwisata ke Raja Ampat',
                    'excerpt' => 'Beberapa tips penting untuk kunjungan pertama ke Raja Ampat',
                    'date' => '15 Juni 2023',
                    'image' => 'tips.jpg'
                ],
                [
                    'title' => 'Spot Menyelam Terbaik',
                    'excerpt' => 'Daftar lokasi menyelam dengan keindahan bawah laut terbaik',
                    'date' => '2 Mei 2023',
                    'image' => 'diving.jpg'
                ]
            ]
        ];

        return view('blog/index', $data);
    }

    public function detail($slug)
    {
        // Contoh data - bisa diganti dengan query database
        $post = [
            'title' => 'Tips Berwisata ke Raja Ampat',
            'content' => '<p>Raja Ampat adalah destinasi wisata yang menakjubkan...</p>',
            'date' => '15 Juni 2023',
            'author' => 'Admin Raja Ampat',
            'image' => 'tips.jpg'
        ];

        $data = [
            'title' => $post['title'],
            'post' => $post
        ];

        return view('blog/detail', $data);
    }
}