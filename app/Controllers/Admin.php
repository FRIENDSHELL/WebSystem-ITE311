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

        $data = [
            'user_name' => session()->get('name'),
            'user_role' => session()->get('role'),
        ];

        return view('admin_dashboard', $data);
    }
}
