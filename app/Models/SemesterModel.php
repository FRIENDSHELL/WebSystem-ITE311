<?php

namespace App\Models;

use CodeIgniter\Model;

class SemesterModel extends Model
{
    protected $table      = 'semesters';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'name',
        'description',
        'is_active',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;

    /**
     * Get all active semesters
     */
    public function getActiveSemesters()
    {
        return $this->where('is_active', 1)->orderBy('name', 'ASC')->findAll();
    }

    /**
     * Get semester with course count
     */
    public function getSemestersWithCourseCount()
    {
        return $this->select('semesters.*, COUNT(courses.id) as course_count')
                    ->join('courses', 'courses.semester_id = semesters.id', 'left')
                    ->groupBy('semesters.id')
                    ->orderBy('semesters.name', 'ASC')
                    ->findAll();
    }
}