<?php
if (!function_exists('ensure_upload_directory')) {
    function ensure_upload_directory($path)
    {
        $fullPath = ROOTPATH . 'public/' . $path;
        
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0755, true);
            
            // Create .htaccess untuk security
            $htaccessContent = "Order deny,allow\nDeny from all\n<Files ~ \"\.(jpg|jpeg|png|gif)$\">\nAllow from all\n</Files>";
            file_put_contents($fullPath . '/.htaccess', $htaccessContent);
            
            // Create index.html untuk prevent directory listing
            file_put_contents($fullPath . '/index.html', '<!DOCTYPE html><html><head><title>403 Forbidden</title></head><body><p>Directory access is forbidden.</p></body></html>');
        }
        
        return $fullPath;
    }
}

if (!function_exists('delete_uploaded_file')) {
    function delete_uploaded_file($filePath)
    {
        $fullPath = ROOTPATH . 'public/' . $filePath;
        
        if (file_exists($fullPath) && is_file($fullPath)) {
            return unlink($fullPath);
        }
        
        return false;
    }
}