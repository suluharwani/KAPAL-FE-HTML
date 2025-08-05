<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Dashboard extends BaseController
{
    public function index()
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        $data = [
            'title' => 'Dashboard',
            'userData' => $session->get('userData')
        ];
        
        return view('dashboard_view', $data);
    }
}