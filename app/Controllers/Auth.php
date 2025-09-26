<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected $helpers = ['form', 'url'];

    public function login()
    {
        // If already logged in → go role-based dashboard
        if (session()->get('logged_in')) {
            return redirect()->to("dashboard");
        }

        if ($this->request->getMethod() === 'POST') {
            if (!$this->validate([
                'email'    => 'required|valid_email',
                'password' => 'required|min_length[4]'
            ])) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $userModel = new UserModel();
            $user = $userModel->findUserByEmail($this->request->getPost('email'));

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

            return redirect()->to("dashboard");
        }

        return view('auth/login');
    }

    public function register()
    {
        // If already logged in → go role-based dashboard
        if (session()->get('logged_in')) {
            return redirect()->to("dashboard");
        }

        if ($this->request->getMethod() === 'POST') {
            if (!$this->validate([
                'name'             => 'required|min_length[3]|max_length[50]',
                'email'            => 'required|valid_email|is_unique[users.email]',
                'password'         => 'required|min_length[4]',
                'confirm_password' => 'matches[password]',
                // 'role'             => 'required|in_list[student,instructor,admin]'
            ])) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $userModel = new UserModel();
            $userData = [
                'name'     => $this->request->getPost('name'),
                'email'    => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'), // hashed sa model
                'role'     => 'student',
            ];

            $userId = $userModel->createAccount($userData);

            if ($userId) {
                // Optional: Auto-login after register
                session()->set([
                    'id'        => $userId,
                    'name'      => $userData['name'],
                    'email'     => $userData['email'],
                    'role'      => $userData['role'],
                    'logged_in' => true,
                ]);

                return redirect()->to("dashboard");
            }

            return redirect()->back()->with('error', 'Failed to register. Please try again.');
        }

        return view('auth/register');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('message', 'You have been logged out.');
    }

    /**
     * 🔹 Helper function: redirect user by role
     */
    public function dashboard()
    {
        if(!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $session = session();

        $data = [
            'name' => $session->get('name'),
            'role' => $session->get('role')
        ];

        return  view('template/header.php', $data) . view('auth/dashboard', $data);
    }
}