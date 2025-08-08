<?php namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table = 'settings';
    protected $primaryKey = 'setting_id';
    protected $allowedFields = ['setting_key', 'setting_value', 'description'];
    protected $useTimestamps = true;
    protected $updatedField = 'updated_at';
    
    public function getSettings()
    {
        $settings = $this->findAll();
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting['setting_key']] = $setting['setting_value'];
        }
        return $result;
    }
    
    public function updateSettings($data)
    {
        foreach ($data as $key => $value) {
            $this->where('setting_key', $key)->set('setting_value', $value)->update();
        }
        return true;
    }
}