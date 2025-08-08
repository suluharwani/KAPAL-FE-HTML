<?php namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
// app/Filters/AuthFilter.php

public function before(RequestInterface $request, $arguments = null)
{
    $session = session();
    $currentRoute = $request->getUri()->getPath(); // Perubahan di sini
    
    // Daftar route yang boleh diakses tanpa login
    $allowedRoutes = ['login', 'forgot-password', 'reset-password'];
    
    if (!in_array($currentRoute, $allowedRoutes)) {
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
    }
    
    // Jika sudah login tapi mencoba akses halaman login
    if ($currentRoute === 'login' && $session->get('isLoggedIn')) {
        return redirect()->to('/admin/dashboard');
    }
}

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}