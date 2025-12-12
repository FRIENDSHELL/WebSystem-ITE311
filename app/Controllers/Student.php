<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\CourseModel;
use App\Models\MaterialModel;

class Student extends BaseController
{
    protected $helpers = ['form', 'url'];

    /**
     * Student Dashboard
     */
    public function dashboard()
    {
        // Check if user is logged in and is a student
        if (!session()->get('logged_in') || session()->get('role') !== 'student') {
            return redirect()->to('/login')->with('error', 'Please log in as a student.');
        }

        $userId = session()->get('id');
        $enrollmentModel = new EnrollmentModel();
        $courseModel = new CourseModel();
        $materialModel = new MaterialModel();

        // Get student's enrollment applications with detailed information
        $enrollmentApplications = $enrollmentModel->select('enrollments.*, courses.title as course_title, courses.course_name, courses.course_code, users.name as teacher_name')
            ->join('courses', 'courses.id = enrollments.course_id', 'left')
            ->join('users', 'users.id = courses.user_id', 'left')
            ->where('enrollments.user_id', $userId)
            ->orderBy('enrollments.enrollment_date', 'DESC')
            ->findAll();

        // Get enrolled courses (approved/enrolled status)
        $enrolledCourses = $enrollmentModel->select('courses.id, courses.title, courses.course_code, courses.description')
            ->join('courses', 'courses.id = enrollments.course_id')
            ->where('enrollments.user_id', $userId)
            ->whereIn('enrollments.enrollment_status', ['Approved', 'Enrolled'])
            ->findAll();

        // Get available courses for enrollment
        $availableCourses = $courseModel->getCoursesWithRelations();

        // Get recent materials from enrolled courses
        $recentMaterials = [];
        if (!empty($enrolledCourses)) {
            $courseIds = array_column($enrolledCourses, 'id');
            $recentMaterials = $materialModel->select('materials.*, courses.title as course_title')
                ->join('courses', 'courses.id = materials.course_id')
                ->whereIn('materials.course_id', $courseIds)
                ->where('materials.is_active', 1)
                ->orderBy('materials.created_at', 'DESC')
                ->limit(5)
                ->findAll();
        }

        $data = [
            'title' => 'Student Dashboard',
            'user_name' => session()->get('name'),
            'user_role' => session()->get('role'),
            'enrollmentApplications' => $enrollmentApplications,
            'enrolledCourses' => $enrolledCourses,
            'availableCourses' => $availableCourses,
            'recentMaterials' => $recentMaterials,
        ];

        return view('student/dashboard', $data);
    }

    /**
     * View student's courses
     */
    public function courses()
    {
        // Check if user is logged in and is a student
        if (!session()->get('logged_in') || session()->get('role') !== 'student') {
            return redirect()->to('/login')->with('error', 'Please log in as a student.');
        }

        $userId = session()->get('id');
        $enrollmentModel = new EnrollmentModel();

        // Get enrolled courses with details
        $enrolledCourses = $enrollmentModel->select('courses.*, enrollments.enrollment_status, enrollments.enrollment_date,
                                                    semesters.name as semester_name, terms.name as term_name, 
                                                    school_years.year as school_year, users.name as teacher_name')
            ->join('courses', 'courses.id = enrollments.course_id')
            ->join('semesters', 'semesters.id = courses.semester_id', 'left')
            ->join('terms', 'terms.id = courses.term_id', 'left')
            ->join('school_years', 'school_years.id = courses.school_year_id', 'left')
            ->join('users', 'users.id = courses.user_id', 'left')
            ->where('enrollments.user_id', $userId)
            ->orderBy('enrollments.enrollment_date', 'DESC')
            ->findAll();

        $data = [
            'title' => 'My Courses',
            'user_name' => session()->get('name'),
            'user_role' => session()->get('role'),
            'enrolledCourses' => $enrolledCourses,
        ];

        return view('student/courses', $data);
    }

    /**
     * View enrollment applications
     */
    public function enrollments()
    {
        // Check if user is logged in and is a student
        if (!session()->get('logged_in') || session()->get('role') !== 'student') {
            return redirect()->to('/login')->with('error', 'Please log in as a student.');
        }

        $userId = session()->get('id');
        $enrollmentModel = new EnrollmentModel();

        // Get all enrollment applications for this student
        $enrollmentApplications = $enrollmentModel->select('enrollments.*, courses.title as course_title, courses.course_code,
                                                           courses.description as course_description, courses.credits,
                                                           semesters.name as semester_name, terms.name as term_name, 
                                                           school_years.year as school_year, users.name as teacher_name')
            ->join('courses', 'courses.id = enrollments.course_id', 'left')
            ->join('semesters', 'semesters.id = courses.semester_id', 'left')
            ->join('terms', 'terms.id = courses.term_id', 'left')
            ->join('school_years', 'school_years.id = courses.school_year_id', 'left')
            ->join('users', 'users.id = courses.user_id', 'left')
            ->where('enrollments.user_id', $userId)
            ->orderBy('enrollments.enrollment_date', 'DESC')
            ->findAll();

        $data = [
            'title' => 'My Enrollment Applications',
            'user_name' => session()->get('name'),
            'user_role' => session()->get('role'),
            'enrollmentApplications' => $enrollmentApplications,
        ];

        return view('student/enrollments', $data);
    }

    /**
     * View student profile
     */
    public function profile()
    {
        // Check if user is logged in and is a student
        if (!session()->get('logged_in') || session()->get('role') !== 'student') {
            return redirect()->to('/login')->with('error', 'Please log in as a student.');
        }

        $userId = session()->get('id');
        $userModel = new \App\Models\UserModel();
        $enrollmentModel = new EnrollmentModel();

        // Get user information
        $user = $userModel->find($userId);

        // Get enrollment statistics
        $enrollmentStats = [
            'total_applications' => $enrollmentModel->where('user_id', $userId)->countAllResults(),
            'pending' => $enrollmentModel->where('user_id', $userId)->where('enrollment_status', 'Pending')->countAllResults(),
            'approved' => $enrollmentModel->where('user_id', $userId)->where('enrollment_status', 'Approved')->countAllResults(),
            'enrolled' => $enrollmentModel->where('user_id', $userId)->where('enrollment_status', 'Enrolled')->countAllResults(),
            'rejected' => $enrollmentModel->where('user_id', $userId)->where('enrollment_status', 'Rejected')->countAllResults(),
        ];

        $data = [
            'title' => 'My Profile',
            'user_name' => session()->get('name'),
            'user_role' => session()->get('role'),
            'user' => $user,
            'enrollmentStats' => $enrollmentStats,
        ];

        return view('student/profile', $data);
    }

    /**
     * Check for enrollment status updates
     */
    public function checkEnrollmentUpdates()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'student') {
            return $this->response->setJSON(['error' => 'Access denied']);
        }

        $userId = session()->get('id');
        $enrollmentModel = new EnrollmentModel();

        // Get recent status updates (within last 24 hours)
        $recentUpdates = $enrollmentModel->select('enrollments.*, courses.title as course_title, courses.course_name')
            ->join('courses', 'courses.id = enrollments.course_id', 'left')
            ->where('enrollments.user_id', $userId)
            ->where('enrollments.updated_at >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
            ->whereIn('enrollments.enrollment_status', ['Approved', 'Rejected'])
            ->orderBy('enrollments.updated_at', 'DESC')
            ->findAll();

        return $this->response->setJSON([
            'updates' => $recentUpdates,
            'count' => count($recentUpdates)
        ]);
    }
}