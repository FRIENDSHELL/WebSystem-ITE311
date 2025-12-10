<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AnnouncementModel;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;

class Teacher extends BaseController
{
    /**
     * Teacher Dashboard
     */
    public function dashboard()
    {
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
        if (!empty($myCourseIds)) {
            $myEnrollmentCount = $enrollmentModel
                ->whereIn('course_id', $myCourseIds)
                ->countAllResults();
        }

        // Debug: Log the announcements count
        log_message('info', 'Teacher Dashboard - Announcements count: ' . count($announcements));

        $data = [
            'title'               => 'Teacher Dashboard',
            'user_name'           => session()->get('name'),
            'user_role'           => session()->get('role'),
            'announcements'       => $announcements,
            'my_courses'          => $myCourses,
            'my_course_count'     => count($myCourses),
            'my_enrollment_count' => $myEnrollmentCount,
            'announcement_count'  => count($announcements),
        ];

        return view('teacher_dashboard', $data);
    }
}
