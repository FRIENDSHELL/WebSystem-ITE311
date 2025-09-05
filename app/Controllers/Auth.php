<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    // Show login form
    public function login()
    {
        return view('auth/login');
    }

    // Handle login
    public function loginPost()
    {
        $session   = session();
        $userModel = new UserModel();

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $userModel->where('email', $email)->first();

        if ($user && password_verify($password, $user['password'])) {
            // Set session data
            $session->set([
                'user_id'    => $user['id'],
                'name'       => $user['name'],
                'email'      => $user['email'],
                'role'       => $user['role'],
                'isLoggedIn' => true
            ]);

            return redirect()->to('/dashboard');
        }

        return redirect()->back()->with('error', 'Invalid email or password.');
    }

    // Show register form
    public function register()
    {
        return view('auth/register');
    }

    // Handle register
    public function registerPost()
    {
        $userModel = new UserModel();

        $data = [
            'name'     => $this->request->getPost('name'),
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'), // hashed in UserModel
            'role'     => $this->request->getPost('role') ?? 'student' // ✅ take role from form
        ];

        $userModel->save($data);

        return redirect()->to('/login')->with('success', 'Account created successfully! Please login.');
    }

    // Dashboard (shared by all roles)
    public function dashboard()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $data = [
            'name' => session()->get('name'),
            'role' => session()->get('role')
        ];

        return view('auth/dashboard', $data);
    }

    // Logout
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'You have been logged out.');
    }
}
