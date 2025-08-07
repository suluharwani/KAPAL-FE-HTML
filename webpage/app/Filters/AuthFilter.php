<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Jika belum login dan mencoba mengakses halaman yang membutuhkan login
        if (!session()->get('isLoggedIn')) {
            // Simpan URL yang diminta untuk redirect setelah login
            session()->set('redirect_url', current_url());
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Jika sudah login tapi mencoba mengakses halaman login/register
        if (in_array($request->getUri()->getPath(), ['/login', '/register']) && session()->get('isLoggedIn')) {
            return redirect()->to('/')->with('message', 'Anda sudah login');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada tindakan setelah request
    }
}