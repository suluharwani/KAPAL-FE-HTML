<?php namespace App\Controllers;

use App\Models\SettingModel;

class SettingController extends BaseController
{
    protected $settingModel;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        // Get default values if settings don't exist
        $settings = $this->settingModel->getSettings();
        
        $defaultSettings = [
            'site_name' => 'Raja Ampat Boats',
            'site_email' => 'info@rajaampatboats.com',
            'site_phone' => '+62 812 3456 7890',
            'site_address' => 'Jl. Wisata Bahari, Raja Ampat, Papua Barat',
            'currency' => 'IDR',
            'timezone' => 'Asia/Jakarta',
            'maintenance_mode' => '0'
        ];
        
        // Merge with defaults
        $settings = array_merge($defaultSettings, $settings);

        $data = [
            'title' => 'System Settings',
            'settings' => $settings,
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/settings/index', $data);
    }

    public function update()
    {
        $data = $this->request->getPost();
        
        // Handle checkbox value
        $data['maintenance_mode'] = isset($data['maintenance_mode']) ? '1' : '0';
        
        if ($this->settingModel->updateSettings($data)) {
            return redirect()->to('/admin/settings')->with('success', 'Settings updated successfully');
        } else {
            return redirect()->to('/admin/settings')->with('error', 'Failed to update settings');
        }
    }
}