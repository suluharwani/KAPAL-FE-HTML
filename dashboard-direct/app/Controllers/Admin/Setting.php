<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingModel;

class Setting extends BaseController
{
    protected $settingModel;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
    }

    public function index()
    {
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'site_name' => 'required|max_length[100]',
                'site_email' => 'required|valid_email',
                'site_phone' => 'required|max_length[20]',
                'site_address' => 'required|max_length[255]',
                'currency' => 'required|max_length[10]',
                'payment_bank_name' => 'required|max_length[50]',
                'payment_account_number' => 'required|max_length[50]',
                'payment_account_name' => 'required|max_length[100]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $data = [
                'site_name' => $this->request->getPost('site_name'),
                'site_email' => $this->request->getPost('site_email'),
                'site_phone' => $this->request->getPost('site_phone'),
                'site_address' => $this->request->getPost('site_address'),
                'currency' => $this->request->getPost('currency'),
                'payment_bank_name' => $this->request->getPost('payment_bank_name'),
                'payment_account_number' => $this->request->getPost('payment_account_number'),
                'payment_account_name' => $this->request->getPost('payment_account_name')
            ];

            if ($this->settingModel->updateSettings($data)) {
                return redirect()->to('/admin/setting')->with('success', 'Settings updated successfully');
            } else {
                return redirect()->back()->with('error', 'Failed to update settings');
            }
        }

        $data = [
            'settings' => $this->settingModel->getSettings()
        ];

        return view('admin/setting/index', $data);
    }
}