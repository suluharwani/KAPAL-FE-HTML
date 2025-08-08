<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\Database\BaseConnection;
use Config\Database;

class Backup extends BaseController
{
    public function index()
    {
        return view('admin/backup/index');
    }

    public function createBackup()
    {
        $db = Database::connect();
        $tables = $db->listTables();

        $return = "-- Raja Ampat Boats Database Backup\n";
        $return .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";

        foreach ($tables as $table) {
            $return .= "--\n-- Table structure for table `$table`\n--\n\n";
            $return .= "DROP TABLE IF EXISTS `$table`;\n";
            
            $createTable = $db->query("SHOW CREATE TABLE `$table`")->getRowArray();
            $return .= $createTable['Create Table'] . ";\n\n";

            $data = $db->table($table)->get()->getResultArray();
            if (count($data)) {
                $return .= "--\n-- Dumping data for table `$table`\n--\n\n";
                
                foreach ($data as $row) {
                    $return .= "INSERT INTO `$table` VALUES(";
                    $values = [];
                    foreach ($row as $value) {
                        $values[] = $db->escape($value);
                    }
                    $return .= implode(',', $values) . ");\n";
                }
                $return .= "\n";
            }
        }

        $filename = 'raja_ampat_boats_backup_' . date('Ymd_His') . '.sql';
        $filepath = WRITEPATH . 'backups/' . $filename;

        // Ensure backup directory exists
        if (!is_dir(WRITEPATH . 'backups')) {
            mkdir(WRITEPATH . 'backups', 0755, true);
        }

        // Write to file
        file_put_contents($filepath, $return);

        // Download the file
        return $this->response->download($filepath, null, true);
    }

    public function listBackups()
    {
        $backups = [];
        
        if (is_dir(WRITEPATH . 'backups')) {
            $files = array_diff(scandir(WRITEPATH . 'backups'), ['.', '..']);
            
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                    $filepath = WRITEPATH . 'backups/' . $file;
                    $backups[] = [
                        'name' => $file,
                        'size' => filesize($filepath),
                        'date' => date('Y-m-d H:i:s', filemtime($filepath))
                    ];
                }
            }
            
            // Sort by date descending
            usort($backups, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
        }

        return $this->response->setJSON($backups);
    }

    public function downloadBackup($filename)
    {
        $filepath = WRITEPATH . 'backups/' . $filename;
        
        if (file_exists($filepath)) {
            return $this->response->download($filepath, null, true);
        } else {
            return redirect()->back()->with('error', 'Backup file not found');
        }
    }

    public function deleteBackup($filename)
    {
        $filepath = WRITEPATH . 'backups/' . $filename;
        
        if (file_exists($filepath)) {
            unlink($filepath);
            return redirect()->back()->with('success', 'Backup file deleted');
        } else {
            return redirect()->back()->with('error', 'Backup file not found');
        }
    }
}