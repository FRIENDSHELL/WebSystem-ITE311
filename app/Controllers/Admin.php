<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\CourseModel;
use App\Models\SemesterModel;
use App\Models\TermModel;
use App\Models\SchoolYearModel;
use App\Models\EnrollmentModel;

class Admin extends BaseController
{
    protected $helpers = ['form', 'url'];

    /**
     * Admin Dashboard
     */
    public function dashboard()
    {
        // Check if user is logged in and is an admin
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Please log in as an admin.');
        }

        $userModel = new UserModel();
        $announcementModel = new \App\Models\AnnouncementModel();
        $courseModel = new \App\Models\CourseModel();
        $enrollmentModel = new \App\Models\EnrollmentModel();

        // Fetch all users grouped by role
        $allUsers = $userModel->findAll();
        $totalUsers = count($allUsers);
        $totalStudents = count(array_filter($allUsers, fn($u) => $u['role'] === 'student'));
        $totalTeachers = count(array_filter($allUsers, fn($u) => $u['role'] === 'teacher'));
        $totalAdmins = count(array_filter($allUsers, fn($u) => $u['role'] === 'admin'));

        // Fetch announcements
        $totalAnnouncements = $announcementModel->countAllResults();
        $announcements = $announcementModel->orderBy('created_at', 'DESC')->limit(5)->findAll();

        // Fetch courses
        $courses = $courseModel->findAll();
        $totalCourses = count($courses);

        // Fetch enrollments
        $totalEnrollments = $enrollmentModel->countAllResults();

        $data = [
            'title' => 'Admin Dashboard',
            'user_name' => session()->get('name'),
            'user_role' => session()->get('role'),
            'announcements' => $announcements,
            'totalUsers' => $totalUsers,
            'totalStudents' => $totalStudents,
            'totalTeachers' => $totalTeachers,
            'totalAdmins' => $totalAdmins,
            'totalAnnouncements' => $totalAnnouncements,
            'totalCourses' => $totalCourses,
            'totalEnrollments' => $totalEnrollments,
        ];

        return view('admin_dashboard', $data);
    }

    /**
     * Manage Users - Display all users
     */
    public function users()
    {
        // Check if user is logged in and is an admin
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Please log in as an admin.');
        }

        $userModel = new UserModel();
        $users = $userModel->orderBy('created_at', 'DESC')->findAll();

        $data = [
            'title' => 'Manage Users',
            'user_name' => session()->get('name'),
            'user_role' => session()->get('role'),
            'users' => $users,
        ];

        return view('admin/manage_users', $data);
    }

    /**
     * Add User
     */
    public function addUser()
    {
        // Check if user is logged in and is an admin
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Please log in as an admin.');
        }

        if ($this->request->getMethod() === 'POST') {
            // Normalize inputs to avoid duplicates and special chars
            $postData = $this->request->getPost();
            $postData['name']  = trim($postData['name'] ?? '');
            $postData['email'] = strtolower(trim($postData['email'] ?? ''));
            $this->request->setGlobal('post', $postData);

            // Validate form
            if (!$this->validate([
                'name'     => 'required|min_length[3]|max_length[50]|alpha_space',
                'email'    => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[4]',
                'role'     => 'required|in_list[student,teacher,admin]',
            ])) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $userModel = new UserModel();
            $normalizedEmail = $this->request->getPost('email');
            if ($userModel->where('LOWER(email)', strtolower($normalizedEmail))->first()) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Email is already registered.');
            }

            $userData = [
                'name'     => $this->request->getPost('name'),
                'email'    => $normalizedEmail,
                'password' => $this->request->getPost('password'),
                'role'     => $this->request->getPost('role'),
            ];

            $userId = $userModel->createAccount($userData);

            if ($userId) {
                return redirect()->to('/admin/users')
                    ->with('success', 'User added successfully!');
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to add user. Please try again.');
        }

        return redirect()->to('/admin/users');
    }

    /**
     * Update User
     */
    public function updateUser($id)
    {
        // Check if user is logged in and is an admin
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Please log in as an admin.');
        }

        if ($this->request->getMethod() === 'POST') {
            $userModel = new UserModel();
            $user = $userModel->find($id);

            if (!$user) {
                return redirect()->to('/admin/users')
                    ->with('error', 'User not found.');
            }

            // Build validation rules
            $rules = [
                'name'  => 'required|min_length[3]|max_length[50]',
                'email' => 'required|valid_email',
                'role'  => 'required|in_list[student,teacher,admin]',
            ];

            // Only validate email uniqueness if it's different from current
            if ($this->request->getPost('email') !== $user['email']) {
                $rules['email'] = 'required|valid_email|is_unique[users.email]';
            }

            // Only validate password if provided
            if ($this->request->getPost('password')) {
                $rules['password'] = 'min_length[4]';
            }

            if (!$this->validate($rules)) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $updateData = [
                'name'  => $this->request->getPost('name'),
                'email' => $this->request->getPost('email'),
                'role'  => $this->request->getPost('role'),
            ];

            // Only update password if provided
            if ($this->request->getPost('password')) {
                $updateData['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
            }

            if ($userModel->update($id, $updateData)) {
                return redirect()->to('/admin/users')
                    ->with('success', 'User updated successfully!');
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update user. Please try again.');
        }

        return redirect()->to('/admin/users');
    }

    /**
     * Delete User
     */
    public function deleteUser($id)
    {
        // Check if user is logged in and is an admin
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Please log in as an admin.');
        }

        // Prevent deleting yourself
        if ($id == session()->get('id')) {
            return redirect()->to('/admin/users')
                ->with('error', 'You cannot delete your own account.');
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            return redirect()->to('/admin/users')
                ->with('error', 'User not found.');
        }

        if ($userModel->delete($id)) {
            return redirect()->to('/admin/users')
                ->with('success', 'User deleted successfully!');
        }

        return redirect()->to('/admin/users')
            ->with('error', 'Failed to delete user. Please try again.');
    }

    // ================= COURSE MANAGEMENT =================

    /**
     * Manage Courses
     */
    public function courses()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Please log in as an admin.');
        }

        $courseModel = new CourseModel();
        $semesterModel = new SemesterModel();
        $termModel = new TermModel();
        $schoolYearModel = new SchoolYearModel();
        $userModel = new UserModel();

        $courses = $courseModel->getCoursesWithRelations();
        $semesters = $semesterModel->getActiveSemesters();
        $terms = $termModel->getActiveTerms();
        $schoolYears = $schoolYearModel->getActiveSchoolYears();
        $teachers = $userModel->where('role', 'teacher')->orWhere('role', 'instructor')->findAll();

        $data = [
            'title' => 'Manage Courses',
            'user_name' => session()->get('name'),
            'user_role' => session()->get('role'),
            'courses' => $courses,
            'semesters' => $semesters,
            'terms' => $terms,
            'schoolYears' => $schoolYears,
            'teachers' => $teachers,
        ];

        return view('admin/manage_courses', $data);
    }

    /**
     * Add Course
     */
    public function addCourse()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Please log in as an admin.');
        }

        if ($this->request->getMethod() === 'POST') {
            if (!$this->validate([
                'title' => 'required|min_length[3]|max_length[255]',
                'course_code' => 'permit_empty|max_length[20]',
                'credits' => 'permit_empty|integer|greater_than[0]',
                'description' => 'permit_empty',
                'user_id' => 'required|integer',
                'semester_id' => 'permit_empty|integer',
                'term_id' => 'permit_empty|integer',
                'school_year_id' => 'permit_empty|integer',
            ])) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $courseModel = new CourseModel();
            $courseData = [
                'title' => $this->request->getPost('title'),
                'course_code' => $this->request->getPost('course_code') ?: null,
                'credits' => $this->request->getPost('credits') ?: 3,
                'description' => $this->request->getPost('description'),
                'user_id' => $this->request->getPost('user_id'),
                'semester_id' => $this->request->getPost('semester_id') ?: null,
                'term_id' => $this->request->getPost('term_id') ?: null,
                'school_year_id' => $this->request->getPost('school_year_id') ?: null,
            ];

            if ($courseModel->insert($courseData)) {
                return redirect()->to('/admin/courses')
                    ->with('success', 'Course added successfully!');
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to add course. Please try again.');
        }

        return redirect()->to('/admin/courses');
    }

    /**
     * Update Course
     */
    public function updateCourse($id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Please log in as an admin.');
        }

        if ($this->request->getMethod() === 'POST') {
            $courseModel = new CourseModel();
            $course = $courseModel->find($id);

            if (!$course) {
                return redirect()->to('/admin/courses')
                    ->with('error', 'Course not found.');
            }

            if (!$this->validate([
                'title' => 'required|min_length[3]|max_length[255]',
                'course_code' => 'permit_empty|max_length[20]',
                'credits' => 'permit_empty|integer|greater_than[0]',
                'description' => 'permit_empty',
                'user_id' => 'required|integer',
                'semester_id' => 'permit_empty|integer',
                'term_id' => 'permit_empty|integer',
                'school_year_id' => 'permit_empty|integer',
            ])) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $updateData = [
                'title' => $this->request->getPost('title'),
                'course_code' => $this->request->getPost('course_code') ?: null,
                'credits' => $this->request->getPost('credits') ?: 3,
                'description' => $this->request->getPost('description'),
                'user_id' => $this->request->getPost('user_id'),
                'semester_id' => $this->request->getPost('semester_id') ?: null,
                'term_id' => $this->request->getPost('term_id') ?: null,
                'school_year_id' => $this->request->getPost('school_year_id') ?: null,
            ];

            if ($courseModel->update($id, $updateData)) {
                return redirect()->to('/admin/courses')
                    ->with('success', 'Course updated successfully!');
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update course. Please try again.');
        }

        return redirect()->to('/admin/courses');
    }

    /**
     * Delete Course
     */
    public function deleteCourse($id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Please log in as an admin.');
        }

        $courseModel = new CourseModel();
        $course = $courseModel->find($id);

        if (!$course) {
            return redirect()->to('/admin/courses')
                ->with('error', 'Course not found.');
        }

        if ($courseModel->delete($id)) {
            return redirect()->to('/admin/courses')
                ->with('success', 'Course deleted successfully!');
        }

        return redirect()->to('/admin/courses')
            ->with('error', 'Failed to delete course. Please try again.');
    }

    // ================= SEMESTER MANAGEMENT =================

    /**
     * Manage Semesters
     */
    public function semesters()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Please log in as an admin.');
        }

        $semesterModel = new SemesterModel();
        $semesters = $semesterModel->getSemestersWithCourseCount();

        $data = [
            'title' => 'Manage Semesters',
            'user_name' => session()->get('name'),
            'user_role' => session()->get('role'),
            'semesters' => $semesters,
        ];

        return view('admin/manage_semesters', $data);
    }

    /**
     * Add Semester
     */
    public function addSemester()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Please log in as an admin.');
        }

        if ($this->request->getMethod() === 'POST') {
            if (!$this->validate([
                'name' => 'required|min_length[3]|max_length[100]',
                'description' => 'permit_empty',
                'is_active' => 'permit_empty|in_list[0,1]',
            ])) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $semesterModel = new SemesterModel();
            $semesterData = [
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            ];

            if ($semesterModel->insert($semesterData)) {
                return redirect()->to('/admin/semesters')
                    ->with('success', 'Semester added successfully!');
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to add semester. Please try again.');
        }

        return redirect()->to('/admin/semesters');
    }

    /**
     * Update Semester
     */
    public function updateSemester($id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Please log in as an admin.');
        }

        if ($this->request->getMethod() === 'POST') {
            $semesterModel = new SemesterModel();
            $semester = $semesterModel->find($id);

            if (!$semester) {
                return redirect()->to('/admin/semesters')
                    ->with('error', 'Semester not found.');
            }

            if (!$this->validate([
                'name' => 'required|min_length[3]|max_length[100]',
                'description' => 'permit_empty',
                'is_active' => 'permit_empty|in_list[0,1]',
            ])) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $updateData = [
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            ];

            if ($semesterModel->update($id, $updateData)) {
                return redirect()->to('/admin/semesters')
                    ->with('success', 'Semester updated successfully!');
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update semester. Please try again.');
        }

        return redirect()->to('/admin/semesters');
    }

    /**
     * Delete Semester
     */
    public function deleteSemester($id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Please log in as an admin.');
        }

        $semesterModel = new SemesterModel();
        $semester = $semesterModel->find($id);

        if (!$semester) {
            return redirect()->to('/admin/semesters')
                ->with('error', 'Semester not found.');
        }

        if ($semesterModel->delete($id)) {
            return redirect()->to('/admin/semesters')
                ->with('success', 'Semester deleted successfully!');
        }

        return redirect()->to('/admin/semesters')
            ->with('error', 'Failed to delete semester. Please try again.');
    }

    // ================= TERM MANAGEMENT =================

    /**
     * Manage Terms
     */
    public function terms()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Please log in as an admin.');
        }

        $termModel = new TermModel();
        $terms = $termModel->getTermsWithCourseCount();

        $data = [
            'title' => 'Manage Terms',
            'user_name' => session()->get('name'),
            'user_role' => session()->get('role'),
            'terms' => $terms,
        ];

        return view('admin/manage_terms', $data);
    }

    /**
     * Add Term
     */
    public function addTerm()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Please log in as an admin.');
        }

        if ($this->request->getMethod() === 'POST') {
            if (!$this->validate([
                'name' => 'required|min_length[3]|max_length[100]',
                'description' => 'permit_empty',
                'is_active' => 'permit_empty|in_list[0,1]',
            ])) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $termModel = new TermModel();
            $termData = [
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            ];

            if ($termModel->insert($termData)) {
                return redirect()->to('/admin/terms')
                    ->with('success', 'Term added successfully!');
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to add term. Please try again.');
        }

        return redirect()->to('/admin/terms');
    }

    /**
     * Update Term
     */
    public function updateTerm($id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Please log in as an admin.');
        }

        if ($this->request->getMethod() === 'POST') {
            $termModel = new TermModel();
            $term = $termModel->find($id);

            if (!$term) {
                return redirect()->to('/admin/terms')
                    ->with('error', 'Term not found.');
            }

            if (!$this->validate([
                'name' => 'required|min_length[3]|max_length[100]',
                'description' => 'permit_empty',
                'is_active' => 'permit_empty|in_list[0,1]',
            ])) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $updateData = [
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            ];

            if ($termModel->update($id, $updateData)) {
                return redirect()->to('/admin/terms')
                    ->with('success', 'Term updated successfully!');
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update term. Please try again.');
        }

        return redirect()->to('/admin/terms');
    }

    /**
     * Delete Term
     */
    public function deleteTerm($id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Please log in as an admin.');
        }

        $termModel = new TermModel();
        $term = $termModel->find($id);

        if (!$term) {
            return redirect()->to('/admin/terms')
                ->with('error', 'Term not found.');
        }

        if ($termModel->delete($id)) {
            return redirect()->to('/admin/terms')
                ->with('success', 'Term deleted successfully!');
        }

        return redirect()->to('/admin/terms')
            ->with('error', 'Failed to delete term. Please try again.');
    }

    // ================= SCHOOL YEAR MANAGEMENT =================

    /**
     * Manage School Years
     */
    public function schoolYears()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Please log in as an admin.');
        }

        $schoolYearModel = new SchoolYearModel();
        $schoolYears = $schoolYearModel->getSchoolYearsWithCourseCount();

        $data = [
            'title' => 'Manage School Years',
            'user_name' => session()->get('name'),
            'user_role' => session()->get('role'),
            'schoolYears' => $schoolYears,
        ];

        return view('admin/manage_school_years', $data);
    }

    /**
     * Add School Year
     */
    public function addSchoolYear()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Please log in as an admin.');
        }

        if ($this->request->getMethod() === 'POST') {
            if (!$this->validate([
                'year' => 'required|min_length[4]|max_length[20]',
                'start_date' => 'permit_empty|valid_date',
                'end_date' => 'permit_empty|valid_date',
                'is_active' => 'permit_empty|in_list[0,1]',
            ])) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $schoolYearModel = new SchoolYearModel();
            $schoolYearData = [
                'year' => $this->request->getPost('year'),
                'start_date' => $this->request->getPost('start_date') ?: null,
                'end_date' => $this->request->getPost('end_date') ?: null,
                'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            ];

            if ($schoolYearModel->insert($schoolYearData)) {
                return redirect()->to('/admin/school-years')
                    ->with('success', 'School year added successfully!');
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to add school year. Please try again.');
        }

        return redirect()->to('/admin/school-years');
    }

    /**
     * Update School Year
     */
    public function updateSchoolYear($id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Please log in as an admin.');
        }

        if ($this->request->getMethod() === 'POST') {
            $schoolYearModel = new SchoolYearModel();
            $schoolYear = $schoolYearModel->find($id);

            if (!$schoolYear) {
                return redirect()->to('/admin/school-years')
                    ->with('error', 'School year not found.');
            }

            if (!$this->validate([
                'year' => 'required|min_length[4]|max_length[20]',
                'start_date' => 'permit_empty|valid_date',
                'end_date' => 'permit_empty|valid_date',
                'is_active' => 'permit_empty|in_list[0,1]',
            ])) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $updateData = [
                'year' => $this->request->getPost('year'),
                'start_date' => $this->request->getPost('start_date') ?: null,
                'end_date' => $this->request->getPost('end_date') ?: null,
                'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            ];

            if ($schoolYearModel->update($id, $updateData)) {
                return redirect()->to('/admin/school-years')
                    ->with('success', 'School year updated successfully!');
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update school year. Please try again.');
        }

        return redirect()->to('/admin/school-years');
    }

    /**
     * Delete School Year
     */
    public function deleteSchoolYear($id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Please log in as an admin.');
        }

        $schoolYearModel = new SchoolYearModel();
        $schoolYear = $schoolYearModel->find($id);

        if (!$schoolYear) {
            return redirect()->to('/admin/school-years')
                ->with('error', 'School year not found.');
        }

        if ($schoolYearModel->delete($id)) {
            return redirect()->to('/admin/school-years')
                ->with('success', 'School year deleted successfully!');
        }

        return redirect()->to('/admin/school-years')
            ->with('error', 'Failed to delete school year. Please try again.');
    }

    // ================= ENROLLMENT MANAGEMENT =================

    /**
     * Manage All Enrollments - Central enrollment oversight
     */
    public function enrollments()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Please log in as an admin.');
        }

        $enrollmentModel = new EnrollmentModel();
        $courseModel = new CourseModel();

        $enrollments = $enrollmentModel->getAllEnrollmentsWithDetails();
        $courses = $courseModel->getCoursesWithRelations();
        $stats = $enrollmentModel->getEnrollmentStats();

        $data = [
            'title' => 'Manage Enrollments',
            'user_name' => session()->get('name'),
            'user_role' => session()->get('role'),
            'enrollments' => $enrollments,
            'courses' => $courses,
            'stats' => $stats,
        ];

        return view('admin/manage_enrollments', $data);
    }

    /**
     * Manage Course Roster - View and manage students in a specific course
     */
    public function courseRoster($courseId)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Please log in as an admin.');
        }

        $enrollmentModel = new EnrollmentModel();
        $courseModel = new CourseModel();

        $course = $courseModel->getCourseWithRelations($courseId);
        if (!$course) {
            return redirect()->to('/admin/enrollments')
                ->with('error', 'Course not found.');
        }

        $enrolledStudents = $enrollmentModel->getEnrollmentsByCourse($courseId);
        $availableStudents = $enrollmentModel->getStudentsNotEnrolledInCourse($courseId);

        $data = [
            'title' => 'Course Roster - ' . $course['title'],
            'user_name' => session()->get('name'),
            'user_role' => session()->get('role'),
            'course' => $course,
            'enrolledStudents' => $enrolledStudents,
            'availableStudents' => $availableStudents,
        ];

        return view('admin/course_roster', $data);
    }

    /**
     * Bulk Enroll Students to Course
     */
    public function bulkEnrollStudents($courseId)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Please log in as an admin.');
        }

        if ($this->request->getMethod() === 'POST') {
            $studentIds = $this->request->getPost('student_ids');
            
            if (empty($studentIds)) {
                return redirect()->back()
                    ->with('error', 'Please select at least one student to enroll.');
            }

            $enrollmentModel = new EnrollmentModel();
            
            if ($enrollmentModel->bulkEnrollStudents($courseId, $studentIds)) {
                $count = count($studentIds);
                return redirect()->to("/admin/course-roster/{$courseId}")
                    ->with('success', "Successfully enrolled {$count} student(s) to the course.");
            }

            return redirect()->back()
                ->with('error', 'Failed to enroll students. Please try again.');
        }

        return redirect()->to("/admin/course-roster/{$courseId}");
    }

    /**
     * Remove Student from Course
     */
    public function removeStudentFromCourse($courseId, $studentId)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Please log in as an admin.');
        }

        $enrollmentModel = new EnrollmentModel();
        $userModel = new UserModel();
        
        $student = $userModel->find($studentId);
        if (!$student) {
            return redirect()->back()
                ->with('error', 'Student not found.');
        }

        if ($enrollmentModel->removeEnrollment($studentId, $courseId)) {
            return redirect()->to("/admin/course-roster/{$courseId}")
                ->with('success', "Successfully removed {$student['name']} from the course.");
        }

        return redirect()->back()
            ->with('error', 'Failed to remove student from course. Please try again.');
    }

    /**
     * Enroll Single Student to Course
     */
    public function enrollStudentToCourse($courseId)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Please log in as an admin.');
        }

        if ($this->request->getMethod() === 'POST') {
            $studentId = $this->request->getPost('student_id');
            
            if (empty($studentId)) {
                return redirect()->back()
                    ->with('error', 'Please select a student to enroll.');
            }

            $enrollmentModel = new EnrollmentModel();
            $userModel = new UserModel();
            
            $student = $userModel->find($studentId);
            if (!$student) {
                return redirect()->back()
                    ->with('error', 'Student not found.');
            }

            if ($enrollmentModel->isAlreadyEnrolled($studentId, $courseId)) {
                return redirect()->back()
                    ->with('error', "{$student['name']} is already enrolled in this course.");
            }

            $enrollmentData = [
                'user_id' => $studentId,
                'course_id' => $courseId,
                'enrolled_at' => date('Y-m-d H:i:s')
            ];

            if ($enrollmentModel->enrollUser($enrollmentData)) {
                return redirect()->to("/admin/course-roster/{$courseId}")
                    ->with('success', "Successfully enrolled {$student['name']} to the course.");
            }

            return redirect()->back()
                ->with('error', 'Failed to enroll student. Please try again.');
        }

        return redirect()->to("/admin/course-roster/{$courseId}");
    }

    /**
     * Get enrollment statistics for dashboard
     */
    public function getEnrollmentStats()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $enrollmentModel = new EnrollmentModel();
        $stats = $enrollmentModel->getEnrollmentStats();
        
        return $this->response->setJSON($stats);
    }
}
