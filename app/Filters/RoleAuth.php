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
            return redirect()->to(site_url('login'))->with('error', 'Please log in first.');
        }

        $userRole = session()->get('role');
        $uri = $request->getUri();
        $currentPath = $uri->getPath();
        
        // Get the route segments to check
        $segments = $uri->getSegments();
        
        // Normalize path - remove base URL if present
        $pathWithoutBase = $currentPath;
        if (strpos($currentPath, '/ITE311-EGARAN/') === 0) {
            $pathWithoutBase = str_replace('/ITE311-EGARAN', '', $currentPath);
        }
        
        // Admin can access any route starting with /admin or /materials
        if ($userRole === 'admin') {
            // Check if path starts with /admin or first segment is 'admin'
            if (strpos($currentPath, '/admin') === 0 || 
                (isset($segments[0]) && $segments[0] === 'admin') ||
                strpos($currentPath, '/materials') === 0 ||
                (isset($segments[0]) && $segments[0] === 'materials')) {
                return; // Allow access
            }
        }

        // Teacher can access routes starting with /teacher or /materials
        if ($userRole === 'teacher') {
            if (strpos($currentPath, '/teacher') === 0 || 
                (isset($segments[0]) && $segments[0] === 'teacher') ||
                strpos($currentPath, '/materials') === 0 ||
                (isset($segments[0]) && $segments[0] === 'materials')) {
                return; // Allow access
            }
        }

        // Student can access routes starting with /student, /dashboard, /announcements, and /materials
        if ($userRole === 'student') {
            // Check segments first (more reliable)
            $firstSegment = isset($segments[0]) ? $segments[0] : '';
            
            if ($firstSegment === 'student' || 
                $firstSegment === 'materials' ||
                $firstSegment === 'dashboard' ||
                $firstSegment === 'announcements' ||
                strpos($currentPath, '/student') !== false || 
                strpos($currentPath, '/materials') !== false ||
                strpos($currentPath, '/dashboard') !== false ||
                strpos($currentPath, '/announcements') !== false ||
                strpos($pathWithoutBase, '/materials') === 0 ||
                strpos($pathWithoutBase, '/student') === 0 ||
                strpos($pathWithoutBase, '/dashboard') === 0 ||
                strpos($pathWithoutBase, '/announcements') === 0) {
                return; // Allow access
            }
        }

        // If user tries to access a route not permitted for their role
        // Redirect based on user role to their appropriate dashboard
        if ($userRole === 'student') {
            return redirect()->to(site_url('dashboard'))->with('error', 'Access Denied: Insufficient Permissions');
        } elseif ($userRole === 'teacher') {
            return redirect()->to(site_url('teacher/dashboard'))->with('error', 'Access Denied: Insufficient Permissions');
        } elseif ($userRole === 'admin') {
            return redirect()->to(site_url('admin/dashboard'))->with('error', 'Access Denied: Insufficient Permissions');
        }
        
        return redirect()->to(site_url('dashboard'))->with('error', 'Access Denied: Insufficient Permissions');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after request
    }
}
