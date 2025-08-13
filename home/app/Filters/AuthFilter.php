<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = \Config\Services::session();
        
        // Check if user is not logged in
        if (!$session->get('isLoggedIn')) {
            // Store the intended URL to redirect after login
            $session->set('redirect_url', current_url());
            
            return redirect()->to('/auth/login')
                ->with('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini');
        }

        // If roles are specified in the filter arguments
        if (!empty($arguments)) {
            $userRole = $session->get('userData')['role'] ?? null;
            
            // Check if user has one of the required roles
            if (!in_array($userRole, $arguments)) {
                return redirect()->back()
                    ->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here if needed after response
    }
}