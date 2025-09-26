<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function admin()
    {
        $data = [
            'title' => 'Admin Dashboard',
            'name'  => session()->get('name'),
        ];
        return view('dashboard/admin', $data);
    }

    public function instructor()
    {
        $data = [
            'title' => 'Instructor Dashboard',
            'name'  => session()->get('name'),
        ];
        return view('dashboard/instructor', $data);
    }

    public function student()
    {
        $data = [
            'title' => 'Student Dashboard',
            'name'  => session()->get('name'),
        ];
        return view('dashboard/student', $data);
    }
}
