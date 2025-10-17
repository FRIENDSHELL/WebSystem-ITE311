<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Teacher extends BaseController
{
    /**
     * Teacher Dashboard
     */
    public function dashboard()
    {
        // Check if user is logged in and is a teacher
        if (!session()->get('logged_in') || session()->get('role') !== 'teacher') {
            return redirect()->to('/login')->with('error', 'Please log in as a teacher.');
        }

        $data = [
            'user_name' => session()->get('name'),
            'user_role' => session()->get('role'),
        ];

        return view('teacher_dashboard', $data);
    }
}
