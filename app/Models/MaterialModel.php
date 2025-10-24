<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialModel extends Model
{
    protected $table = 'materials';
    protected $primaryKey = 'id';
    protected $allowedFields = ['course_id', 'file_name', 'file_path', 'created_at'];
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';

    /**
     * Insert a new material record
     * @param array $data
     * @return int|bool Insert ID or false on failure
     */
    public function insertMaterial($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->insert($data);
    }

    /**
     * Get all materials for a specific course
     * @param int $course_id
     * @return array
     */
    public function getMaterialsByCourse($course_id)
    {
        return $this->where('course_id', $course_id)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get a single material by ID
     * @param int $material_id
     * @return array|null
     */
    public function getMaterialById($material_id)
    {
        return $this->find($material_id);
    }

    /**
     * Delete a material record
     * @param int $material_id
     * @return bool
     */
    public function deleteMaterial($material_id)
    {
        return $this->delete($material_id);
    }

    /**
     * Get materials for courses a student is enrolled in
     * @param int $user_id
     * @return array
     */
    public function getMaterialsForEnrolledCourses($user_id)
    {
        return $this->select('materials.*, courses.title as course_title')
                    ->join('courses', 'courses.id = materials.course_id')
                    ->join('enrollments', 'enrollments.course_id = courses.id')
                    ->where('enrollments.user_id', $user_id)
                    ->orderBy('materials.created_at', 'DESC')
                    ->findAll();
    }
}
