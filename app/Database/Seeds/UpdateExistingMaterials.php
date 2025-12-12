<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UpdateExistingMaterials extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Get all existing materials that need updating
        $materials = $db->table('materials')
                       ->where('title IS NULL OR title = ""')
                       ->get()
                       ->getResultArray();
        
        foreach ($materials as $material) {
            $updateData = [];
            
            // Set title from file_name if not set
            if (empty($material['title'])) {
                $updateData['title'] = pathinfo($material['file_name'], PATHINFO_FILENAME);
            }
            
            // Set original_name from file_name if not set
            if (empty($material['original_name'])) {
                $updateData['original_name'] = $material['file_name'];
            }
            
            // Set upload_date from created_at if not set
            if (empty($material['upload_date']) && !empty($material['created_at'])) {
                $updateData['upload_date'] = $material['created_at'];
            }
            
            // Set default user_id to 1 (admin) if not set
            if (empty($material['user_id'])) {
                $updateData['user_id'] = 1;
            }
            
            // Set file size if file exists
            if (!empty($material['file_path']) && file_exists($material['file_path'])) {
                $updateData['file_size'] = filesize($material['file_path']);
            } else {
                $updateData['file_size'] = 0;
            }
            
            // Set file type based on extension
            if (!empty($material['file_name'])) {
                $extension = strtolower(pathinfo($material['file_name'], PATHINFO_EXTENSION));
                $mimeTypes = [
                    'pdf' => 'application/pdf',
                    'doc' => 'application/msword',
                    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'ppt' => 'application/vnd.ms-powerpoint',
                    'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'xls' => 'application/vnd.ms-excel',
                    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'txt' => 'text/plain',
                    'zip' => 'application/zip',
                    'rar' => 'application/x-rar-compressed',
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                    'mp4' => 'video/mp4',
                    'avi' => 'video/x-msvideo',
                    'mov' => 'video/quicktime'
                ];
                $updateData['file_type'] = $mimeTypes[$extension] ?? 'application/octet-stream';
            }
            
            // Update the record
            if (!empty($updateData)) {
                $db->table('materials')
                   ->where('id', $material['id'])
                   ->update($updateData);
            }
        }
        
        echo "Updated " . count($materials) . " existing materials with missing data.\n";
    }
}
