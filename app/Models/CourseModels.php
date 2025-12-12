<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';        // your table name
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'course_name',
        'semester',
        'term',
        'school_year',
        'course_description'
    ];

    protected $useTimestamps = false; // change to true if you add created_at / updated_at columns
}
