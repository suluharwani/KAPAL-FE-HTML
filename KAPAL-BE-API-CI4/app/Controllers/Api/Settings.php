<?php namespace App\Controllers\Api;

use App\Models\SettingModel;

class Settings extends BaseApiController
{
    protected $modelName = SettingModel::class;

    public function __construct()
    {
        $this->model = new SettingModel();
    }

    public function index()
    {
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only admin can view all settings');
        }

        $settings = $this->model->orderBy('setting_key', 'asc')->findAll();

        return $this->respond([
            'status' => 200,
            'data' => $settings
        ]);
    }

    public function show($key = null)
    {
        $setting = $this->model->where('setting_key', $key)->first();

        if (!$setting) {
            return $this->respondNotFound('Setting not found');
        }

        // Check if setting is public or admin is accessing
        if (!$this->isPublicSetting($key) && $this->request->user->role !== 'admin') {
            return $this->failForbidden('You are not authorized to view this setting');
        }

        return $this->respond([
            'status' => 200,
            'data' => $setting
        ]);
    }

    public function update($key = null)
    {
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only admin can update settings');
        }

        $setting = $this->model->where('setting_key', $key)->first();
        if (!$setting) {
            return $this->respondNotFound('Setting not found');
        }

        $rules = [
            'setting_value' => 'required',
            'description' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'setting_value' => $this->request->getVar('setting_value'),
            'description' => $this->request->getVar('description') ?? $setting['description']
        ];

        if ($this->model->update($setting['setting_id'], $data)) {
            return $this->respondUpdated(['setting_key' => $key]);
        } else {
            return $this->failServerError('Failed to update setting');
        }
    }

    protected function isPublicSetting($key)
    {
        $publicSettings = [
            'site_title',
            'site_description',
            'contact_email',
            'contact_phone',
            'social_facebook',
            'social_instagram',
            'social_twitter'
        ];

        return in_array($key, $publicSettings);
    }
}