<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialModel extends Model
{
    protected $table = 'materials';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'course_id', 'user_id', 'title', 'description', 'file_name', 
        'original_name', 'file_path', 'file_size', 'file_type', 
        'upload_date', 'is_active', 'download_count'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get materials with course and user information
     */
    public function getMaterialsWithDetails($userId = null, $role = null)
    {
        $builder = $this->select('materials.*, courses.course_name, courses.course_id as course_code, users.name as uploaded_by')
                        ->join('courses', 'courses.id = materials.course_id', 'left')
                        ->join('users', 'users.id = materials.user_id', 'left')
                        ->where('materials.is_active', 1);

        // If teacher, only show their materials
        if ($role === 'teacher' && $userId) {
            $builder->where('materials.user_id', $userId);
        }

        return $builder->orderBy('materials.created_at', 'DESC')->findAll();
    }

    /**
     * Search materials
     */
    public function searchMaterials($searchTerm, $userId = null, $role = null)
    {
        $builder = $this->select('materials.*, courses.course_name, courses.course_id as course_code, users.name as uploaded_by')
                        ->join('courses', 'courses.id = materials.course_id', 'left')
                        ->join('users', 'users.id = materials.user_id', 'left')
                        ->where('materials.is_active', 1);

        // If teacher, only show their materials
        if ($role === 'teacher' && $userId) {
            $builder->where('materials.user_id', $userId);
        }

        // Search in multiple fields
        $builder->groupStart()
                ->like('materials.title', $searchTerm)
                ->orLike('materials.description', $searchTerm)
                ->orLike('materials.original_name', $searchTerm)
                ->orLike('courses.course_name', $searchTerm)
                ->orLike('courses.course_id', $searchTerm)
                ->groupEnd();

        return $builder->orderBy('materials.created_at', 'DESC')->findAll();
    }

    /**
     * Get materials by course
     */
    public function getMaterialsByCourse($courseId)
    {
        return $this->select('materials.*, users.name as uploaded_by')
                    ->join('users', 'users.id = materials.user_id', 'left')
                    ->where('materials.course_id', $courseId)
                    ->where('materials.is_active', 1)
                    ->orderBy('materials.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Increment download count
     */
    public function incrementDownloadCount($materialId)
    {
        $material = $this->find($materialId);
        if ($material) {
            $newCount = ($material['download_count'] ?? 0) + 1;
            $this->update($materialId, ['download_count' => $newCount]);
        }
    }

    /**
     * Get material statistics
     */
    public function getMaterialStats($userId = null, $role = null)
    {
        $builder = $this->where('is_active', 1);
        
        if ($role === 'teacher' && $userId) {
            $builder->where('user_id', $userId);
        }

        $totalMaterials = $builder->countAllResults(false);
        $totalDownloads = $builder->selectSum('download_count')->get()->getRow()->download_count ?? 0;

        return [
            'total_materials' => $totalMaterials,
            'total_downloads' => $totalDownloads
        ];
    }
}