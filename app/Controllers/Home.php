<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        return view('index', [
            'isLoggedIn' => session()->get('isLoggedIn'),
            'name'       => session()->get('name'),
            'role'       => session()->get('role')
        ]);
    }

    public function about()
    {
        return view('about', [
            'isLoggedIn' => session()->get('isLoggedIn'),
            'name'       => session()->get('name'),
            'role'       => session()->get('role')
        ]);
    }

    public function contact()
    {
        return view('contact', [
            'isLoggedIn' => session()->get('isLoggedIn'),
            'name'       => session()->get('name'),
            'role'       => session()->get('role')
        ]);
    }

    public function course()
    {
        return view('course/course_list', [
            'isLoggedIn' => session()->get('isLoggedIn'),
            'name'       => session()->get('name'),
            'role'       => session()->get('role')
        ]);
    }
}
