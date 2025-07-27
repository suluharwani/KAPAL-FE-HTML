<?php namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table = 'settings';
    protected $primaryKey = 'setting_id';
    protected $allowedFields = [
        'setting_key', 'setting_value', 'description'
    ];
    protected $useTimestamps = true;
    protected $updatedField = 'updated_at';

    public function getValue($key, $default = null)
    {
        $setting = $this->where('setting_key', $key)->first();
        return $setting ? $setting['setting_value'] : $default;
    }

    public function getSettingsGroup($prefix)
    {
        return $this->like('setting_key', $prefix . '_', 'after')
                   ->findAll();
    }
}