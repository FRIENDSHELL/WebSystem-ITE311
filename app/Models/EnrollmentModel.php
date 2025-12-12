<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table = 'enrollments';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'course_id', 'student_id', 'first_name', 'last_name', 'middle_name',
        'age', 'birth_date', 'gender', 'contact_number', 'email_address', 'address',
        'guardian_name', 'guardian_contact', 'year_level', 'program', 'enrollment_status',
        'enrollment_date', 'enrolled_at', 'notes', 'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'enrolled_at';
    protected $updatedField = 'updated_at';

    public function enrollUser($data)
    {
        return $this->insert($data);
    }

    public function isAlreadyEnrolled($user_id, $course_id)
    {
        return $this->where('user_id', $user_id)
                    ->where('course_id', $course_id)
                    ->countAllResults() > 0;
    }

    /**
     * Get the list of user IDs enrolled in a course.
     */
    public function getUserIdsByCourse(int $courseId): array
    {
        return $this->where('course_id', $courseId)
                    ->findColumn('user_id') ?? [];
    }

    /**
     * Get all enrollments with student and course details for admin management
     */
    public function getAllEnrollmentsWithDetails()
    {
        return $this->select('enrollments.*, users.name as student_name, users.email as student_email, 
                             courses.title as course_title, courses.course_code, 
                             semesters.name as semester_name, terms.name as term_name, school_years.year as school_year')
                    ->join('users', 'users.id = enrollments.user_id')
                    ->join('courses', 'courses.id = enrollments.course_id')
                    ->join('semesters', 'semesters.id = courses.semester_id', 'left')
                    ->join('terms', 'terms.id = courses.term_id', 'left')
                    ->join('school_years', 'school_years.id = courses.school_year_id', 'left')
                    ->where('users.role', 'student')
                    ->orderBy('enrollments.enrolled_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get enrollments by course with student details
     */
    public function getEnrollmentsByCourse($courseId)
    {
        return $this->select('enrollments.*, users.name as student_name, users.email as student_email, users.id as student_id')
                    ->join('users', 'users.id = enrollments.user_id')
                    ->where('enrollments.course_id', $courseId)
                    ->where('users.role', 'student')
                    ->orderBy('users.name', 'ASC')
                    ->findAll();
    }

    /**
     * Get enrollment statistics
     */
    public function getEnrollmentStats()
    {
        $totalEnrollments = $this->countAllResults();
        
        $enrollmentsByStatus = $this->select('COUNT(*) as count')
                                   ->join('users', 'users.id = enrollments.user_id')
                                   ->where('users.role', 'student')
                                   ->get()->getRow()->count ?? 0;

        $recentEnrollments = $this->select('COUNT(*) as count')
                                 ->join('users', 'users.id = enrollments.user_id')
                                 ->where('users.role', 'student')
                                 ->where('enrollments.enrolled_at >=', date('Y-m-d', strtotime('-7 days')))
                                 ->get()->getRow()->count ?? 0;

        return [
            'total' => $totalEnrollments,
            'active_students' => $enrollmentsByStatus,
            'recent' => $recentEnrollments
        ];
    }

    /**
     * Get students not enrolled in a specific course
     */
    public function getStudentsNotEnrolledInCourse($courseId)
    {
        $enrolledUserIds = $this->where('course_id', $courseId)->findColumn('user_id');
        
        $userModel = new \App\Models\UserModel();
        $query = $userModel->where('role', 'student');
        
        if (!empty($enrolledUserIds)) {
            $query->whereNotIn('id', $enrolledUserIds);
        }
        
        return $query->orderBy('name', 'ASC')->findAll();
    }

    /**
     * Bulk enroll students to a course
     */
    public function bulkEnrollStudents($courseId, $studentIds)
    {
        $enrollmentData = [];
        foreach ($studentIds as $studentId) {
            if (!$this->isAlreadyEnrolled($studentId, $courseId)) {
                $enrollmentData[] = [
                    'user_id' => $studentId,
                    'course_id' => $courseId,
                    'enrolled_at' => date('Y-m-d H:i:s')
                ];
            }
        }
        
        if (!empty($enrollmentData)) {
            return $this->insertBatch($enrollmentData);
        }
        
        return true;
    }

    /**
     * Remove enrollment
     */
    public function removeEnrollment($userId, $courseId)
    {
        return $this->where('user_id', $userId)
                    ->where('course_id', $courseId)
                    ->delete();
    }

    /**
     * Create detailed enrollment
     */
    public function createDetailedEnrollment($data)
    {
        // Set default values
        $data['enrollment_date'] = date('Y-m-d H:i:s');
        $data['enrollment_status'] = $data['enrollment_status'] ?? 'Pending';
        
        return $this->insert($data);
    }

    /**
     * Update enrollment status
     */
    public function updateEnrollmentStatus($enrollmentId, $status, $notes = null)
    {
        $updateData = [
            'enrollment_status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($notes) {
            $updateData['notes'] = $notes;
        }
        
        return $this->update($enrollmentId, $updateData);
    }

    /**
     * Get detailed enrollment information
     */
    public function getDetailedEnrollments($status = null)
    {
        $query = $this->select('enrollments.*, courses.title as course_title, courses.course_code,
                               semesters.name as semester_name, terms.name as term_name, 
                               school_years.year as school_year')
                     ->join('courses', 'courses.id = enrollments.course_id')
                     ->join('semesters', 'semesters.id = courses.semester_id', 'left')
                     ->join('terms', 'terms.id = courses.term_id', 'left')
                     ->join('school_years', 'school_years.id = courses.school_year_id', 'left')
                     ->orderBy('enrollments.enrollment_date', 'DESC');
        
        if ($status) {
            $query->where('enrollments.enrollment_status', $status);
        }
        
        return $query->findAll();
    }

    /**
     * Get enrollment by ID with course details
     */
    public function getEnrollmentWithDetails($enrollmentId)
    {
        return $this->select('enrollments.*, courses.title as course_title, courses.course_code,
                             courses.description as course_description, courses.credits,
                             semesters.name as semester_name, terms.name as term_name, 
                             school_years.year as school_year, users.name as teacher_name')
                    ->join('courses', 'courses.id = enrollments.course_id')
                    ->join('semesters', 'semesters.id = courses.semester_id', 'left')
                    ->join('terms', 'terms.id = courses.term_id', 'left')
                    ->join('school_years', 'school_years.id = courses.school_year_id', 'left')
                    ->join('users', 'users.id = courses.user_id', 'left')
                    ->find($enrollmentId);
    }

    /**
     * Check if student ID already exists
     */
    public function isStudentIdExists($studentId, $excludeId = null)
    {
        $query = $this->where('student_id', $studentId);
        
        if ($excludeId) {
            $query->where('id !=', $excludeId);
        }
        
        return $query->countAllResults() > 0;
    }

    /**
     * Get enrollment statistics by status
     */
    public function getEnrollmentStatsByStatus()
    {
        return $this->select('enrollment_status, COUNT(*) as count')
                    ->groupBy('enrollment_status')
                    ->findAll();
    }
}