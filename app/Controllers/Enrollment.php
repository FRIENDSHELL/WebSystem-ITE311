<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\CourseModel;
use App\Models\SemesterModel;
use App\Models\TermModel;
use App\Models\SchoolYearModel;

class Enrollment extends BaseController
{
    protected $helpers = ['form', 'url'];

    /**
     * Display enrollment form
     */
    public function index()
    {
        $courseModel = new CourseModel();
        $semesterModel = new SemesterModel();
        $termModel = new TermModel();
        $schoolYearModel = new SchoolYearModel();

        $courses = $courseModel->getCoursesWithRelations();
        $semesters = $semesterModel->getActiveSemesters();
        $terms = $termModel->getActiveTerms();
        $schoolYears = $schoolYearModel->getActiveSchoolYears();

        $data = [
            'title' => 'Student Enrollment Form',
            'courses' => $courses,
            'semesters' => $semesters,
            'terms' => $terms,
            'schoolYears' => $schoolYears,
        ];

        return view('enrollment/form', $data);
    }

    /**
     * Process enrollment form submission
     */
    public function submit()
    {
        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to('/enrollment');
        }

        // Validation rules
        $validationRules = [
            'course_id' => 'required|integer',
            'student_id' => 'required|min_length[3]|max_length[20]',
            'first_name' => 'required|min_length[2]|max_length[100]|alpha_space',
            'last_name' => 'required|min_length[2]|max_length[100]|alpha_space',
            'middle_name' => 'permit_empty|max_length[100]|alpha_space',
            'age' => 'required|integer|greater_than[15]|less_than[100]',
            'birth_date' => 'required|valid_date',
            'gender' => 'required|in_list[Male,Female,Other]',
            'contact_number' => 'required|min_length[10]|max_length[20]',
            'email_address' => 'required|valid_email|max_length[255]',
            'address' => 'required|min_length[10]',
            'guardian_name' => 'required|min_length[3]|max_length[255]|alpha_space',
            'guardian_contact' => 'required|min_length[10]|max_length[20]',
            'year_level' => 'required|max_length[20]',
            'program' => 'required|max_length[255]',
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $enrollmentModel = new EnrollmentModel();

        // Check if student ID already exists
        if ($enrollmentModel->isStudentIdExists($this->request->getPost('student_id'))) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Student ID already exists. Please use a different Student ID.');
        }

        // Prepare enrollment data
        $enrollmentData = [
            'course_id' => $this->request->getPost('course_id'),
            'student_id' => $this->request->getPost('student_id'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'middle_name' => $this->request->getPost('middle_name'),
            'age' => $this->request->getPost('age'),
            'birth_date' => $this->request->getPost('birth_date'),
            'gender' => $this->request->getPost('gender'),
            'contact_number' => $this->request->getPost('contact_number'),
            'email_address' => $this->request->getPost('email_address'),
            'address' => $this->request->getPost('address'),
            'guardian_name' => $this->request->getPost('guardian_name'),
            'guardian_contact' => $this->request->getPost('guardian_contact'),
            'year_level' => $this->request->getPost('year_level'),
            'program' => $this->request->getPost('program'),
            'enrollment_status' => 'Pending',
            'user_id' => session()->get('id'), // If user is logged in
        ];

        try {
            $enrollmentId = $enrollmentModel->createDetailedEnrollment($enrollmentData);

            if ($enrollmentId) {
                // Get course and teacher information for notification
                $courseModel = new CourseModel();
                $course = $courseModel->find($enrollmentData['course_id']);
                
                if ($course) {
                    // Log successful enrollment submission for teacher notification
                    log_message('info', "New enrollment request submitted - Student: {$enrollmentData['first_name']} {$enrollmentData['last_name']}, Course: {$course['course_name']}, Teacher ID: {$course['user_id']}");
                }

                return redirect()->to('/enrollment/success/' . $enrollmentId)
                    ->with('success', 'Enrollment application submitted successfully! Your application has been sent to the course teacher for approval.');
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Failed to submit enrollment. Please try again.');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Display enrollment success page
     */
    public function success($enrollmentId)
    {
        $enrollmentModel = new EnrollmentModel();
        $enrollment = $enrollmentModel->getEnrollmentWithDetails($enrollmentId);

        if (!$enrollment) {
            return redirect()->to('/enrollment')
                ->with('error', 'Enrollment not found.');
        }

        $data = [
            'title' => 'Enrollment Successful',
            'enrollment' => $enrollment,
        ];

        return view('enrollment/success', $data);
    }

    /**
     * Admin - Manage all enrollments
     */
    public function manage()
    {
        // Check if user is admin
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Access denied. Admin only.');
        }

        $enrollmentModel = new EnrollmentModel();
        $enrollments = $enrollmentModel->getDetailedEnrollments();
        $stats = $enrollmentModel->getEnrollmentStatsByStatus();

        $data = [
            'title' => 'Manage Enrollments',
            'user_name' => session()->get('name'),
            'user_role' => session()->get('role'),
            'enrollments' => $enrollments,
            'stats' => $stats,
        ];

        return view('admin/manage_enrollments', $data);
    }

    /**
     * Admin - View enrollment details only (no approval rights)
     */

    /**
     * Admin - View enrollment details
     */
    public function view($enrollmentId)
    {
        // Check if user is admin
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Access denied. Admin only.');
        }

        $enrollmentModel = new EnrollmentModel();
        $enrollment = $enrollmentModel->getEnrollmentWithDetails($enrollmentId);

        if (!$enrollment) {
            return redirect()->to('/admin/enrollments')
                ->with('error', 'Enrollment not found.');
        }

        $data = [
            'title' => 'Enrollment Details',
            'user_name' => session()->get('name'),
            'user_role' => session()->get('role'),
            'enrollment' => $enrollment,
        ];

        return view('admin/enrollment_details', $data);
    }

    /**
     * Admin - Delete enrollment
     */
    public function delete($enrollmentId)
    {
        // Check if user is admin
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Access denied. Admin only.');
        }

        $enrollmentModel = new EnrollmentModel();
        
        if ($enrollmentModel->delete($enrollmentId)) {
            return redirect()->to('/admin/enrollments')
                ->with('success', 'Enrollment deleted successfully!');
        } else {
            return redirect()->to('/admin/enrollments')
                ->with('error', 'Failed to delete enrollment.');
        }
    }
}