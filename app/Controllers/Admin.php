<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

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
            // Validate form
            if (!$this->validate([
                'name'     => 'required|min_length[3]|max_length[50]',
                'email'    => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[4]',
                'role'     => 'required|in_list[student,teacher,admin]',
            ])) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $userModel = new UserModel();
            $userData = [
                'name'     => $this->request->getPost('name'),
                'email'    => $this->request->getPost('email'),
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
}
