<?php

namespace App\Models;

use CodeIgniter\Model;

class SchoolYearModel extends Model
{
    protected $table      = 'school_years';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'year',
        'start_date',
        'end_date',
        'is_active',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;

    /**
     * Get all active school years
     */
    public function getActiveSchoolYears()
    {
        return $this->where('is_active', 1)->orderBy('year', 'DESC')->findAll();
    }

    /**
     * Get school years with course count
     */
    public function getSchoolYearsWithCourseCount()
    {
        return $this->select('school_years.*, COUNT(courses.id) as course_count')
                    ->join('courses', 'courses.school_year_id = school_years.id', 'left')
                    ->groupBy('school_years.id')
                    ->orderBy('school_years.year', 'DESC')
                    ->findAll();
    }

    /**
     * Get current active school year
     */
    public function getCurrentSchoolYear()
    {
        return $this->where('is_active', 1)
                    ->where('start_date <=', date('Y-m-d'))
                    ->where('end_date >=', date('Y-m-d'))
                    ->first();
    }
}