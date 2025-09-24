<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        // Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $data = [
            'name' => session()->get('name'),
            'role' => session()->get('role')
        ];

        // ✅ same dashboard view for all roles
        return view('auth/dashboard', $data);
    }
}
