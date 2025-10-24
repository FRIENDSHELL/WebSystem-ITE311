<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please log in first.');
        }

        $userRole = session()->get('role');
        $currentPath = $request->getUri()->getPath();

        // Admin can access any route starting with /admin
        if ($userRole === 'admin') {
            if (strpos($currentPath, '/admin') === 0) {
                return; // Allow access
            }
        }

        // Teacher can only access routes starting with /teacher
        if ($userRole === 'teacher') {
            if (strpos($currentPath, '/teacher') === 0) {
                return; // Allow access
            }
        }

        // Student can access routes starting with /student and /announcements
        if ($userRole === 'student') {
            if (strpos($currentPath, '/student') === 0 || $currentPath === '/announcements') {
                return; // Allow access
            }
        }

        // If user tries to access a route not permitted for their role
        return redirect()->to('/announcements')->with('error', 'Access Denied: Insufficient Permissions');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after request
    }
}
