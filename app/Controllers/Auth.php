<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected $helpers = ['form', 'url'];

    public function login()
    {
        // if already logged in → go dashboard
        if (session()->get('logged_in')) {
            return redirect()->to('dashboard');
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
                return redirect()->back()->withInput()->with('error', 'Invalid email or password.');
            }

            // start session
            session()->set([
                'id'        => $user['id'],
                'name'      => $user['name'],
                'email'     => $user['email'],
                'role'      => $user['role'],
                'logged_in' => true,
            ]);

            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    public function register()
    {
        // if already logged in → go dashboard
        if (session()->get('logged_in')) {
            return redirect()->to('dashboard');
        }

        if ($this->request->getMethod() === 'POST') {
            if (!$this->validate([
                'name'             => 'required|min_length[3]|max_length[50]',
                'email'            => 'required|valid_email|is_unique[users.email]',
                'password'         => 'required|min_length[4]',
                'confirm_password' => 'matches[password]',
                'role'             => 'required|in_list[student,instructor,admin]'
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
                // ✅ Auto login after registration
                session()->set([
                    'id'        => $userId,
                    'name'      => $userData['name'],
                    'email'     => $userData['email'],
                    'role'      => $userData['role'],
                    'logged_in' => true,
                ]);

                return view("auth/login");
            }
        }

        return view('auth/register');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'You have been logged out.');
    }

    public function dashboard()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please login first.');
        }

        $userModel = new UserModel();
        $stats = $userModel->getDashboardStats(session()->get('role'), session()->get('id'));
        $data = [
            'user_name'  => session()->get('name'),
            'user_role'  => session()->get('role'),
            'total_users' => $stats['total_users'] ?? 0,
        ];

        

        return view('auth/dashboard', $data);
    }
}

//practice commit