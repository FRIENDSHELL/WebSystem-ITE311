<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AnnouncementModel;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\SchoolYearModel;
use App\Models\SemesterModel;
use App\Models\TermModel;

class Teacher extends BaseController
{
    /**
     * Teacher Dashboard with Search
     */
    public function dashboard()
    {
        // Handle search if present
        $search = $this->request->getGet('search');
        if ($search) {
            return $this->searchDashboard($search);
        }
        // Only teachers may view this page; reroute other roles
        if (!session()->get('logged_in')) {
            return redirect()->to(site_url('login'))->with('error', 'Please log in as a teacher.');
        }

        $role = session()->get('role');
        if ($role !== 'teacher') {
            if ($role === 'admin') {
                return redirect()->to(site_url('admin/dashboard'))->with('error', 'Admins belong on the admin dashboard.');
            }
            return redirect()->to(site_url('dashboard'))->with('error', 'Access denied for your role.');
        }

        $userId = (int) session()->get('id');

        // Fetch announcements for the dashboard
        $announcementModel = new AnnouncementModel();
        $announcements = $announcementModel->orderBy('created_at', 'DESC')->findAll();

        // Fetch courses owned by this teacher
        $courseModel   = new CourseModel();
        $myCourses     = $courseModel->where('user_id', $userId)->findAll();
        $myCourseIds   = array_column($myCourses, 'id');

        // Count enrollments across teacher-owned courses
        $enrollmentModel    = new EnrollmentModel();
        $myEnrollmentCount  = 0;
        $pendingEnrollmentCount = 0;
        if (!empty($myCourseIds)) {
            $myEnrollmentCount = $enrollmentModel
                ->whereIn('course_id', $myCourseIds)
                ->where('enrollment_status', 'Approved')
                ->countAllResults();
            
            // Count pending enrollments for approval
            $pendingEnrollmentCount = $enrollmentModel
                ->whereIn('course_id', $myCourseIds)
                ->where('enrollment_status', 'Pending')
                ->countAllResults();
        }

        // Get recent pending enrollments for the dashboard
        $pendingEnrollments = [];
        if (!empty($myCourseIds)) {
            $pendingEnrollments = $enrollmentModel
                ->select('enrollments.*, courses.course_name, courses.course_id as course_code')
                ->join('courses', 'courses.id = enrollments.course_id')
                ->whereIn('enrollments.course_id', $myCourseIds)
                ->where('enrollments.enrollment_status', 'Pending')
                ->orderBy('enrollments.enrollment_date', 'DESC')
                ->limit(5)
                ->findAll();
        }

        // Debug: Force show all pending enrollments if none found for teacher
        if (empty($pendingEnrollments)) {
            $pendingEnrollments = $enrollmentModel
                ->select('enrollments.*, courses.course_name, courses.course_id as course_code')
                ->join('courses', 'courses.id = enrollments.course_id')
                ->where('enrollments.enrollment_status', 'Pending')
                ->orderBy('enrollments.enrollment_date', 'DESC')
                ->limit(5)
                ->findAll();
            
            // Also update the count
            $pendingEnrollmentCount = count($pendingEnrollments);
        }

        // Debug: Log the announcements count
        log_message('info', 'Teacher Dashboard - Announcements count: ' . count($announcements));

        $data = [
            'title'                    => 'Teacher Dashboard',
            'user_name'                => session()->get('name'),
            'user_role'                => session()->get('role'),
            'announcements'            => $announcements,
            'my_courses'               => $myCourses,
            'my_course_count'          => count($myCourses),
            'my_enrollment_count'      => $myEnrollmentCount,
            'pending_enrollment_count' => $pendingEnrollmentCount,
            'pending_enrollments'      => $pendingEnrollments,
            'announcement_count'       => count($announcements),
        ];

        return view('teacher_dashboard', $data);
    }

    /**
     * Search Dashboard for Teachers
     */
    public function searchDashboard($searchTerm)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'teacher') {
            return redirect()->to(site_url('login'))->with('error', 'Access denied.');
        }

        $userId = (int) session()->get('id');
        $courseModel = new CourseModel();
        $enrollmentModel = new EnrollmentModel();
        $announcementModel = new AnnouncementModel();

        // Search in courses
        $searchResults = [];
        
        // Search teacher's courses
        $courses = $courseModel->where('user_id', $userId)
            ->groupStart()
                ->like('course_name', $searchTerm)
                ->orLike('course_id', $searchTerm)
                ->orLike('description', $searchTerm)
            ->groupEnd()
            ->findAll();

        foreach ($courses as $course) {
            $searchResults[] = [
                'type' => 'course',
                'title' => $course['course_name'],
                'subtitle' => $course['course_id'],
                'description' => $course['description'],
                'url' => site_url('teacher/edit-course/' . $course['id']),
                'icon' => 'bi-book',
                'badge' => 'Course'
            ];
        }

        // Search in enrollments (if teacher has courses)
        if (!empty($courses)) {
            $myCourseIds = array_column($courses, 'id');
            $enrollments = $enrollmentModel
                ->select('enrollments.*, courses.course_name, courses.course_id as course_code')
                ->join('courses', 'courses.id = enrollments.course_id')
                ->whereIn('enrollments.course_id', $myCourseIds)
                ->groupStart()
                    ->like('enrollments.student_id', $searchTerm)
                    ->orLike('enrollments.first_name', $searchTerm)
                    ->orLike('enrollments.last_name', $searchTerm)
                    ->orLike('courses.course_name', $searchTerm)
                ->groupEnd()
                ->findAll();

            foreach ($enrollments as $enrollment) {
                $searchResults[] = [
                    'type' => 'enrollment',
                    'title' => ($enrollment['first_name'] ?? '') . ' ' . ($enrollment['last_name'] ?? ''),
                    'subtitle' => $enrollment['student_id'] ?? '',
                    'description' => 'Enrollment for ' . ($enrollment['course_name'] ?? ''),
                    'url' => site_url('teacher/view-enrollment/' . $enrollment['id']),
                    'icon' => 'bi-person-check',
                    'badge' => 'Enrollment'
                ];
            }
        }

        // Search in announcements
        $announcements = $announcementModel
            ->groupStart()
                ->like('title', $searchTerm)
                ->orLike('content', $searchTerm)
            ->groupEnd()
            ->orderBy('created_at', 'DESC')
            ->findAll();

        foreach ($announcements as $announcement) {
            $searchResults[] = [
                'type' => 'announcement',
                'title' => $announcement['title'] ?? '',
                'subtitle' => isset($announcement['created_at']) ? date('M d, Y', strtotime($announcement['created_at'])) : '',
                'description' => isset($announcement['content']) ? substr(strip_tags($announcement['content']), 0, 100) . '...' : '',
                'url' => '#',
                'icon' => 'bi-megaphone',
                'badge' => 'Announcement'
            ];
        }

        // Return to dashboard with search results
        $courseModel = new CourseModel();
        $myCourses = $courseModel->where('user_id', $userId)->findAll();
        $myCourseIds = array_column($myCourses, 'id');
        
        $enrollmentModel = new EnrollmentModel();
        $myEnrollmentCount = 0;
        $pendingEnrollmentCount = 0;
        if (!empty($myCourseIds)) {
            $myEnrollmentCount = $enrollmentModel
                ->whereIn('course_id', $myCourseIds)
                ->where('enrollment_status', 'Approved')
                ->countAllResults();
            
            $pendingEnrollmentCount = $enrollmentModel
                ->whereIn('course_id', $myCourseIds)
                ->where('enrollment_status', 'Pending')
                ->countAllResults();
        }

        $allAnnouncements = $announcementModel->orderBy('created_at', 'DESC')->findAll();

        $data = [
            'title' => 'Teacher Dashboard',
            'user_name' => session()->get('name'),
            'user_role' => session()->get('role'),
            'announcements' => $allAnnouncements,
            'my_courses' => $myCourses,
            'my_course_count' => count($myCourses),
            'my_enrollment_count' => $myEnrollmentCount,
            'pending_enrollment_count' => $pendingEnrollmentCount,
            'announcement_count' => count($allAnnouncements),
            'search_term' => $searchTerm,
            'search_results' => $searchResults,
        ];

        return view('teacher_dashboard', $data);
    }

    public function courses()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'teacher') {
            return redirect()->to(site_url('login'))->with('error', 'Access denied.');
        }

        $userId = (int) session()->get('id');
        $search = $this->request->getGet('search');

        $courseModel = new CourseModel();
        $builder = $courseModel->where('user_id', $userId);

        // Search functionality
        if (!empty($search)) {
            $builder->groupStart()
                ->like('course_name', $search)
                ->orLike('course_id', $search)
                ->orLike('description', $search)
                ->groupEnd();
        }

        $courses = $builder->orderBy('course_name', 'ASC')->findAll();

        $data = [
            'title' => 'My Courses',
            'courses' => $courses,
            'search' => $search,
            'user_name' => session()->get('name'),
            'user_role' => session()->get('role'),
        ];

        return view('teacher/courses', $data);
    }

    /**
     * Show Create Course Form
     */
    public function createCourse()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'teacher') {
            return redirect()->to(site_url('login'))->with('error', 'Access denied.');
        }

        // Get dropdown data
        $schoolYearModel = new SchoolYearModel();
        $semesterModel = new SemesterModel();
        $termModel = new TermModel();

        $data = [
            'title' => 'Create New Course',
            'school_years' => $schoolYearModel->getActiveSchoolYears(),
            'semesters' => $semesterModel->getActiveSemesters(),
            'terms' => $termModel->getActiveTerms(),
            'user_name' => session()->get('name'),
            'user_role' => session()->get('role'),
        ];

        return view('teacher/create_course', $data);
    }

    /**
     * Store New Course
     */
    public function storeCourse()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'teacher') {
            return redirect()->to(site_url('login'))->with('error', 'Access denied.');
        }

        helper(['form', 'url']);

        $validation = [
            'course_id' => 'required|min_length[2]|max_length[20]',
            'course_name' => 'required|min_length[2]|max_length[100]',
            'school_year' => 'required',
            'semester' => 'required',
            'term' => 'required',
            'time_start' => 'required',
            'time_end' => 'required',
        ];

        if (!$this->validate($validation)) {
            // Get dropdown data again for the form
            $schoolYearModel = new SchoolYearModel();
            $semesterModel = new SemesterModel();
            $termModel = new TermModel();

            $data = [
                'title' => 'Create New Course',
                'school_years' => $schoolYearModel->getActiveSchoolYears(),
                'semesters' => $semesterModel->getActiveSemesters(),
                'terms' => $termModel->getActiveTerms(),
                'validation' => $this->validator,
                'user_name' => session()->get('name'),
                'user_role' => session()->get('role'),
            ];

            return view('teacher/create_course', $data);
        }

        $courseModel = new CourseModel();
        $userId = (int) session()->get('id');

        // Process class schedule
        $classSchedule = $this->request->getPost('class_schedule');
        $scheduleString = is_array($classSchedule) ? implode(',', $classSchedule) : '';

        $data = [
            'course_id' => $this->request->getPost('course_id'),
            'course_name' => $this->request->getPost('course_name'),
            'school_year' => $this->request->getPost('school_year'),
            'semester' => $this->request->getPost('semester'),
            'term' => $this->request->getPost('term'),
            'class_schedule' => $scheduleString,
            'time_start' => $this->request->getPost('time_start'),
            'time_end' => $this->request->getPost('time_end'),
            'description' => $this->request->getPost('description'),
            'user_id' => $userId,
        ];

        if ($courseModel->insert($data)) {
            return redirect()->to(site_url('teacher/courses'))->with('success', 'Course created successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to create course. Please try again.');
        }
    }

    /**
     * Edit Course
     */
    public function editCourse($id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'teacher') {
            return redirect()->to(site_url('login'))->with('error', 'Access denied.');
        }

        $userId = (int) session()->get('id');
        $courseModel = new CourseModel();
        
        // Ensure teacher can only edit their own courses
        $course = $courseModel->where('id', $id)->where('user_id', $userId)->first();
        
        if (!$course) {
            return redirect()->to(site_url('teacher/courses'))->with('error', 'Course not found or access denied.');
        }

        // Get dropdown data
        $schoolYearModel = new SchoolYearModel();
        $semesterModel = new SemesterModel();
        $termModel = new TermModel();

        $data = [
            'title' => 'Edit Course',
            'course' => $course,
            'school_years' => $schoolYearModel->getActiveSchoolYears(),
            'semesters' => $semesterModel->getActiveSemesters(),
            'terms' => $termModel->getActiveTerms(),
            'user_name' => session()->get('name'),
            'user_role' => session()->get('role'),
        ];

        return view('teacher/edit_course', $data);
    }

    /**
     * Update Course
     */
    public function updateCourse($id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'teacher') {
            return redirect()->to(site_url('login'))->with('error', 'Access denied.');
        }

        $userId = (int) session()->get('id');
        $courseModel = new CourseModel();
        
        // Ensure teacher can only update their own courses
        $course = $courseModel->where('id', $id)->where('user_id', $userId)->first();
        
        if (!$course) {
            return redirect()->to(site_url('teacher/courses'))->with('error', 'Course not found or access denied.');
        }

        helper(['form', 'url']);

        $validation = [
            'course_id' => 'required|min_length[2]|max_length[20]',
            'course_name' => 'required|min_length[2]|max_length[100]',
            'school_year' => 'required',
            'semester' => 'required',
            'term' => 'required',
            'time_start' => 'required',
            'time_end' => 'required',
        ];

        if (!$this->validate($validation)) {
            return redirect()->back()->withInput()->with('error', 'Please check your input and try again.');
        }

        // Process class schedule
        $classSchedule = $this->request->getPost('class_schedule');
        $scheduleString = is_array($classSchedule) ? implode(',', $classSchedule) : '';

        $data = [
            'course_id' => $this->request->getPost('course_id'),
            'course_name' => $this->request->getPost('course_name'),
            'school_year' => $this->request->getPost('school_year'),
            'semester' => $this->request->getPost('semester'),
            'term' => $this->request->getPost('term'),
            'class_schedule' => $scheduleString,
            'time_start' => $this->request->getPost('time_start'),
            'time_end' => $this->request->getPost('time_end'),
            'description' => $this->request->getPost('description'),
        ];

        if ($courseModel->update($id, $data)) {
            return redirect()->to(site_url('teacher/courses'))->with('success', 'Course updated successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to update course. Please try again.');
        }
    }

    /**
     * Delete Course
     */
    public function deleteCourse($id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'teacher') {
            return redirect()->to(site_url('login'))->with('error', 'Access denied.');
        }

        $userId = (int) session()->get('id');
        $courseModel = new CourseModel();
        
        // Ensure teacher can only delete their own courses
        $course = $courseModel->where('id', $id)->where('user_id', $userId)->first();
        
        if (!$course) {
            return redirect()->to(site_url('teacher/courses'))->with('error', 'Course not found or access denied.');
        }

        if ($courseModel->delete($id)) {
            return redirect()->to(site_url('teacher/courses'))->with('success', 'Course deleted successfully!');
        } else {
            return redirect()->to(site_url('teacher/courses'))->with('error', 'Failed to delete course. Please try again.');
        }
    }

    /**
     * View Pending Enrollments
     */
    public function pendingEnrollments()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'teacher') {
            return redirect()->to(site_url('login'))->with('error', 'Access denied.');
        }

        $userId = (int) session()->get('id');
        $courseModel = new CourseModel();
        $enrollmentModel = new EnrollmentModel();

        // Get teacher's courses
        $myCourses = $courseModel->where('user_id', $userId)->findAll();
        $myCourseIds = array_column($myCourses, 'id');

        // Get pending enrollments for teacher's courses
        $pendingEnrollments = [];
        if (!empty($myCourseIds)) {
            $pendingEnrollments = $enrollmentModel
                ->select('enrollments.*, courses.course_name, courses.course_id as course_code')
                ->join('courses', 'courses.id = enrollments.course_id')
                ->whereIn('enrollments.course_id', $myCourseIds)
                ->where('enrollments.enrollment_status', 'Pending')
                ->orderBy('enrollments.enrollment_date', 'DESC')
                ->findAll();
        }

        // Debug: Show all pending enrollments if teacher has no courses or no enrollments
        if (empty($pendingEnrollments)) {
            $pendingEnrollments = $enrollmentModel
                ->select('enrollments.*, courses.course_name, courses.course_id as course_code')
                ->join('courses', 'courses.id = enrollments.course_id')
                ->where('enrollments.enrollment_status', 'Pending')
                ->orderBy('enrollments.enrollment_date', 'DESC')
                ->findAll();
        }

        $data = [
            'title' => 'Pending Enrollments',
            'pending_enrollments' => $pendingEnrollments,
            'user_name' => session()->get('name'),
            'user_role' => session()->get('role'),
        ];

        return view('teacher/pending_enrollments', $data);
    }

    /**
     * Approve Enrollment
     */
    public function approveEnrollment($enrollmentId)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'teacher') {
            return redirect()->to(site_url('login'))->with('error', 'Access denied.');
        }

        $userId = (int) session()->get('id');
        $enrollmentModel = new EnrollmentModel();
        $courseModel = new CourseModel();
        $notificationModel = new \App\Models\NotificationModel();

        // Get enrollment details with course info
        $enrollment = $enrollmentModel->select('enrollments.*, courses.course_name, courses.course_id as course_code')
            ->join('courses', 'courses.id = enrollments.course_id')
            ->where('enrollments.id', $enrollmentId)
            ->first();
            
        if (!$enrollment) {
            return redirect()->back()->with('error', 'Enrollment not found.');
        }

        // Check if already approved
        if ($enrollment['enrollment_status'] === 'Approved') {
            return redirect()->back()->with('info', 'This enrollment is already approved.');
        }

        // Verify that the course belongs to this teacher
        $course = $courseModel->where('id', $enrollment['course_id'])->where('user_id', $userId)->first();
        if (!$course) {
            return redirect()->back()->with('error', 'You can only approve enrollments for your own courses.');
        }

        // Update enrollment status to approved
        if ($enrollmentModel->updateEnrollmentStatus($enrollmentId, 'Approved', 'Approved by teacher')) {
            // Create notification for the student
            $studentId = $enrollment['user_id'];
            $courseName = $enrollment['course_name'] ?? $enrollment['course_code'] ?? 'the course';
            $notificationMessage = "Your enrollment application for {$courseName} has been approved! You can now access course materials and participate in class activities.";
            
            $notificationModel->insert([
                'user_id' => $studentId,
                'message' => $notificationMessage,
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            log_message('info', "Enrollment approved - ID: {$enrollmentId}, Student: {$studentId}, Course: {$courseName}");
            
            return redirect()->back()->with('success', 'Enrollment approved successfully! The student has been notified.');
        } else {
            return redirect()->back()->with('error', 'Failed to approve enrollment.');
        }
    }

    /**
     * Reject Enrollment
     */
    public function rejectEnrollment($enrollmentId)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'teacher') {
            return redirect()->to(site_url('login'))->with('error', 'Access denied.');
        }

        $userId = (int) session()->get('id');
        $enrollmentModel = new EnrollmentModel();
        $courseModel = new CourseModel();
        $notificationModel = new \App\Models\NotificationModel();

        // Get enrollment details with course info
        $enrollment = $enrollmentModel->select('enrollments.*, courses.course_name, courses.course_id as course_code')
            ->join('courses', 'courses.id = enrollments.course_id')
            ->where('enrollments.id', $enrollmentId)
            ->first();
            
        if (!$enrollment) {
            return redirect()->back()->with('error', 'Enrollment not found.');
        }

        // Verify that the course belongs to this teacher
        $course = $courseModel->where('id', $enrollment['course_id'])->where('user_id', $userId)->first();
        if (!$course) {
            return redirect()->back()->with('error', 'You can only reject enrollments for your own courses.');
        }

        // Check if already rejected
        if ($enrollment['enrollment_status'] === 'Rejected') {
            return redirect()->back()->with('info', 'This enrollment is already rejected.');
        }

        // Get rejection reason if provided
        $reason = $this->request->getPost('reason') ?? 'Rejected by teacher';

        // Update enrollment status to rejected
        if ($enrollmentModel->updateEnrollmentStatus($enrollmentId, 'Rejected', $reason)) {
            // Create notification for the student
            $studentId = $enrollment['user_id'];
            $courseName = $enrollment['course_name'] ?? $enrollment['course_code'] ?? 'the course';
            $notificationMessage = "Your enrollment application for {$courseName} was not approved. " . (!empty($reason) && $reason !== 'Rejected by teacher' ? "Reason: {$reason}" : "Please contact your teacher for more information.");
            
            $notificationModel->insert([
                'user_id' => $studentId,
                'message' => $notificationMessage,
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            log_message('info', "Enrollment rejected - ID: {$enrollmentId}, Student: {$studentId}, Course: {$courseName}");
            
            return redirect()->back()->with('success', 'Enrollment rejected successfully! The student has been notified.');
        } else {
            return redirect()->back()->with('error', 'Failed to reject enrollment.');
        }
    }

    /**
     * Debug method to check enrollment data
     */
    public function debugEnrollments()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'teacher') {
            return redirect()->to(site_url('login'))->with('error', 'Access denied.');
        }

        $enrollmentModel = new EnrollmentModel();
        $courseModel = new CourseModel();
        
        // Get all data for debugging
        $allEnrollments = $enrollmentModel->findAll();
        $allCourses = $courseModel->findAll();
        $pendingEnrollments = $enrollmentModel->where('enrollment_status', 'Pending')->findAll();
        
        $debugData = [
            'all_enrollments' => $allEnrollments,
            'all_courses' => $allCourses,
            'pending_enrollments' => $pendingEnrollments,
            'current_user_id' => session()->get('id'),
        ];
        
        // Return as JSON for easy viewing
        return $this->response->setJSON($debugData);
    }

    /**
     * View Enrollment Details
     */
    public function viewEnrollment($enrollmentId)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'teacher') {
            return redirect()->to(site_url('login'))->with('error', 'Access denied.');
        }

        $userId = (int) session()->get('id');
        $enrollmentModel = new EnrollmentModel();
        $courseModel = new CourseModel();

        // Get enrollment with details
        $enrollment = $enrollmentModel->getEnrollmentWithDetails($enrollmentId);
        if (!$enrollment) {
            return redirect()->back()->with('error', 'Enrollment not found.');
        }

        // Verify that the course belongs to this teacher
        $course = $courseModel->where('id', $enrollment['course_id'])->where('user_id', $userId)->first();
        if (!$course) {
            return redirect()->back()->with('error', 'You can only view enrollments for your own courses.');
        }

        $data = [
            'title' => 'Enrollment Details',
            'enrollment' => $enrollment,
            'user_name' => session()->get('name'),
            'user_role' => session()->get('role'),
        ];

        return view('teacher/enrollment_details', $data);
    }
}
