<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table      = 'courses';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'course_id',
        'course_name',
        'title',
        'course_code',
        'credits',
        'description',
        'user_id',
        'semester_id',
        'term_id',
        'school_year_id',
        'semester',
        'term',
        'school_year',
        'class_schedule',
        'time_start',
        'time_end',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;

    /**
     * Return courses filtered by the optional search term.
     */
    public function search(?string $term = null): array
    {
        $builder = $this->builder()
            ->orderBy('title', 'ASC');

        if (!empty($term)) {
            $builder->groupStart()
                ->like('title', $term)
                ->orLike('description', $term)
                ->orLike('course_code', $term)
                ->groupEnd();
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get courses with related data
     */
    public function getCoursesWithRelations()
    {
        return $this->select('courses.*, semesters.name as semester_name, terms.name as term_name, school_years.year as school_year, users.name as teacher_name')
                    ->join('semesters', 'semesters.id = courses.semester_id', 'left')
                    ->join('terms', 'terms.id = courses.term_id', 'left')
                    ->join('school_years', 'school_years.id = courses.school_year_id', 'left')
                    ->join('users', 'users.id = courses.user_id', 'left')
                    ->orderBy('courses.course_name', 'ASC')
                    ->findAll();
    }

    /**
     * Get courses by teacher ID
     */
    public function getCoursesByTeacher($teacherId)
    {
        return $this->where('user_id', $teacherId)
                    ->orderBy('course_name', 'ASC')
                    ->findAll();
    }

    /**
     * Get course by ID with relations
     */
    public function getCourseWithRelations($id)
    {
        return $this->select('courses.*, semesters.name as semester_name, terms.name as term_name, school_years.year as school_year, users.name as teacher_name')
                    ->join('semesters', 'semesters.id = courses.semester_id', 'left')
                    ->join('terms', 'terms.id = courses.term_id', 'left')
                    ->join('school_years', 'school_years.id = courses.school_year_id', 'left')
                    ->join('users', 'users.id = courses.user_id', 'left')
                    ->find($id);
    }
}

