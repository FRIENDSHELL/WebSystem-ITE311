<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends BaseController
{
    protected $helpers = ['form', 'url'];

    /**
     * 🔹 LOGIN
     */
    public function login()
    {
        // If already logged in → go to dashboard
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }

        if ($this->request->getMethod() === 'POST') {
            // Validate form
            if (!$this->validate([
                'email'    => 'required|valid_email',
                'password' => 'required|min_length[4]',
            ])) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $userModel = new UserModel();
            $user = $userModel->findUserByEmail($this->request->getPost('email'));

            // Check credentials
            if (!$user || !password_verify($this->request->getPost('password'), $user['password'])) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Invalid email or password.');
            }

            // Start session
            session()->set([
                'id'        => $user['id'],
                'name'      => $user['name'],
                'email'     => $user['email'],
                'role'      => $user['role'],
                'logged_in' => true,
            ]);

            // Role-based redirection
            switch ($user['role']) {
                case 'student':
                    return redirect()->to('/dashboard');
                case 'teacher':
                    return redirect()->to('/teacher/dashboard');
                case 'admin':
                    return redirect()->to('/dashboard');
                default:
                    return redirect()->to('/dashboard');
            }
        }

        return view('auth/login');
    }

    /**
     * 🔹 REGISTER
     */
    public function register()
    {
        // If already logged in → go to dashboard
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }

        if ($this->request->getMethod() === 'POST') {
            // Validate form
            if (!$this->validate([
                'name'             => 'required|min_length[3]|max_length[50]',
                'email'            => 'required|valid_email|is_unique[users.email]',
                'password'         => 'required|min_length[4]',
                'confirm_password' => 'matches[password]',
            ])) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $userModel = new UserModel();
            $userData = [
                'name'     => $this->request->getPost('name'),
                'email'    => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'), // hashed in model
                'role'     => 'student',
            ];

            $userId = $userModel->createAccount($userData);

            if ($userId) {
                // Auto-login after register
                session()->set([
                    'id'        => $userId,
                    'name'      => $userData['name'],
                    'email'     => $userData['email'],
                    'role'      => $userData['role'],
                    'logged_in' => true,
                ]);

                // Role-based redirection
                return redirect()->to('/dashboard');
            }

            return redirect()->back()->with('error', 'Failed to register. Please try again.');
        }

        return view('auth/register');
    }

    /**
     * 🔹 LOGOUT
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('message', 'You have been logged out.');
    }

    /**
     * 🔹 DASHBOARD (Includes Enrollment Logic)
     */
    public function dashboard()
    {
        $session = session();

        // ✅ Check login
        if (!$session->get('logged_in') || !$session->get('id')) {
            return redirect()->to('/login')->with('error', 'Please log in first.');
        }

        $db = \Config\Database::connect();
        $user_id   = (int) $session->get('id');
        $user_name = $session->get('name');
        $user_role = $session->get('role');

        // ✅ Load all courses
        $courses = $db->table('courses')
            ->select('id, title, description')
            ->get()
            ->getResultArray();

        // ✅ Load user’s enrolled courses
        $enrolledCourses = $db->table('enrollments')
            ->select('courses.id, courses.title')
            ->join('courses', 'enrollments.course_id = courses.id')
            ->where('enrollments.user_id', $user_id)
            ->get()
            ->getResultArray();

        // ✅ Admin: view all users
        $users = [];
        if ($user_role === 'admin') {
            $users = $db->table('users')
                ->select('id, name, email, role')
                ->get()
                ->getResultArray();
        }

        // ✅ Fetch announcements for all users
        $announcementModel = new \App\Models\AnnouncementModel();
        $announcements = $announcementModel->orderBy('created_at', 'DESC')->findAll();

        // ✅ Send data to view
        $data = [
            'user_name'       => $user_name,
            'user_role'       => $user_role,
            'courses'         => $courses,
            'enrolledCourses' => $enrolledCourses,
            'users'           => $users,
            'announcements'   => $announcements,
        ];

        return view('auth/dashboard', $data);
    }

    /**
     * 🔹 ENROLL IN COURSE (Separate method for cleaner routing)
     */
    public function enroll()
    {
        $session = session();

        // ✅ Check login
        if (!$session->get('logged_in') || !$session->get('id')) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Please log in first.'
            ]);
        }

        $db = \Config\Database::connect();
        $user_id   = (int) $session->get('id');
        $course_id = (int) $this->request->getPost('course_id');

        // ✅ Validate input
        if (empty($course_id) || empty($user_id)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Invalid course or user.'
            ]);
        }

        // ✅ Check if already enrolled
        $exists = $db->table('enrollments')
            ->where('user_id', $user_id)
            ->where('course_id', $course_id)
            ->countAllResults();

        if ($exists > 0) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'You are already enrolled in this course.'
            ]);
        }

        // ✅ Insert enrollment safely
        try {
            $inserted = $db->table('enrollments')->insert([
                'user_id'    => $user_id,
                'course_id'  => $course_id,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            if ($inserted) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'You have successfully enrolled!'
                ]);
            }

            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Failed to enroll. Please try again.'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }
}
