<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Admin extends BaseController
{
    /**
     * Admin Dashboard
     */
    public function dashboard()
    {
        // Check if user is logged in and is an admin
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Please log in as an admin.');
        }

        // Fetch announcements for the dashboard
        $announcementModel = new \App\Models\AnnouncementModel();
        $announcements = $announcementModel->orderBy('created_at', 'DESC')->findAll();

        // Debug: Log the announcements count
        log_message('info', 'Admin Dashboard - Announcements count: ' . count($announcements));

        $data = [
            'user_name' => session()->get('name'),
            'user_role' => session()->get('role'),
            'announcements' => $announcements,
        ];

        return view('admin_dashboard', $data);
    }
}
