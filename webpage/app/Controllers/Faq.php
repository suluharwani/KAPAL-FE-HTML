<?php
namespace App\Controllers;

class Faq extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'FAQ | Raja Ampat',
            'faqs' => [
                [
                    'question' => 'Bagaimana cara memesan kapal?',
                    'answer' => 'Anda bisa memesan melalui website kami atau menghubungi langsung via telepon/WhatsApp.'
                ],
                [
                    'question' => 'Apa saja persyaratan untuk menyewa kapal?',
                    'answer' => 'Anda perlu menyiapkan identitas diri dan melakukan pembayaran DP minimal 50%.'
                ]
            ]
        ];

        return view('faq/index', $data);
    }
}