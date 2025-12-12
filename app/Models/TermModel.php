<?php

namespace App\Models;

use CodeIgniter\Model;

class TermModel extends Model
{
    protected $table      = 'terms';
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
     * Get all active terms
     */
    public function getActiveTerms()
    {
        return $this->where('is_active', 1)->orderBy('name', 'ASC')->findAll();
    }

    /**
     * Get terms with course count
     */
    public function getTermsWithCourseCount()
    {
        return $this->select('terms.*, COUNT(courses.id) as course_count')
                    ->join('courses', 'courses.term_id = terms.id', 'left')
                    ->groupBy('terms.id')
                    ->orderBy('terms.name', 'ASC')
                    ->findAll();
    }
}