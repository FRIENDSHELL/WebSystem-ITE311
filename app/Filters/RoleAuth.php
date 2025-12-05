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
        $uri = $request->getUri();
        $currentPath = $uri->getPath();
        
        // Get the route segments to check
        $segments = $uri->getSegments();
        
        // Admin can access any route starting with /admin
        if ($userRole === 'admin') {
            // Check if path starts with /admin or first segment is 'admin'
            if (strpos($currentPath, '/admin') === 0 || 
                (isset($segments[0]) && $segments[0] === 'admin')) {
                return; // Allow access
            }
        }

        // Teacher can only access routes starting with /teacher
        if ($userRole === 'teacher') {
            if (strpos($currentPath, '/teacher') === 0 || 
                (isset($segments[0]) && $segments[0] === 'teacher')) {
                return; // Allow access
            }
        }

        // Student can access routes starting with /student, /dashboard, and /announcements
        if ($userRole === 'student') {
            if (strpos($currentPath, '/student') === 0 || 
                (isset($segments[0]) && $segments[0] === 'student') ||
                $currentPath === '/dashboard' || 
                $currentPath === '/announcements' ||
                strpos($currentPath, '/materials') === 0) {
                return; // Allow access
            }
        }

        // If user tries to access a route not permitted for their role
        // Redirect based on user role to their appropriate dashboard
        if ($userRole === 'student') {
            return redirect()->to('/dashboard')->with('error', 'Access Denied: Insufficient Permissions');
        } elseif ($userRole === 'teacher') {
            return redirect()->to('/teacher/dashboard')->with('error', 'Access Denied: Insufficient Permissions');
        } elseif ($userRole === 'admin') {
            return redirect()->to('/admin/dashboard')->with('error', 'Access Denied: Insufficient Permissions');
        }
        
        return redirect()->to('/dashboard')->with('error', 'Access Denied: Insufficient Permissions');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after request
    }
}
