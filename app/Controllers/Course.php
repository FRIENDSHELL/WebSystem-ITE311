<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use CodeIgniter\Controller;

class Course extends BaseController
{
    public function enroll()
    {
        // ✅ Check session
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'You must be logged in to enroll.'
            ]);
        }

        $user_id = session()->get('id');
        $course_id = $this->request->getPost('course_id');

        // ✅ Validate course_id
        if (empty($course_id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No course selected.'
            ]);
        }

        $enrollmentModel = new EnrollmentModel();

        // ✅ Check if already enrolled
        if ($enrollmentModel->isAlreadyEnrolled($user_id, $course_id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'You are already enrolled in this course.'
            ]);
        }

        // ✅ Insert enrollment with proper status
        $data = [
            'user_id' => $user_id,
            'course_id' => $course_id,
            'enrollment_status' => 'Pending',
            'enrollment_date' => date('Y-m-d H:i:s')
        ];

        try {
            if ($enrollmentModel->createDetailedEnrollment($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Enrollment successful!'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Enrollment failed. Please try again.'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
 
 
    }

    public function search()
    {
        $searchTerm = trim(
            $this->request->getGet('term')
            ?? $this->request->getGet('search_term')
            ?? ''
        );

        $courseModel = new CourseModel();
        $courses = $courseModel->search($searchTerm);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($courses);
        }

        $canEnroll = session()->get('logged_in') && session()->get('role') === 'student';

        return view('course/index', [
            'courses'    => $courses,
            'searchTerm' => $searchTerm,
            'canEnroll'  => $canEnroll,
        ]);
    }
}