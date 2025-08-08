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
        $data = [
            'title' => 'System Settings',
            'settings' => $this->settingModel->getSettings(),
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
        
        if ($this->settingModel->updateSettings($data)) {
            return redirect()->to('/admin/settings')->with('success', 'Settings updated successfully');
        } else {
            return redirect()->to('/admin/settings')->with('error', 'Failed to update settings');
        }
    }
}