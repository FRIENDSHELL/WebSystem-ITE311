<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $isLoggedIn = session()->get('isLoggedIn');
        $role       = session()->get('role');

        if (! $isLoggedIn) {
            return redirect()->to('/login')->with('error', 'Please login first.');
        }

        // If specific roles were provided in the routes filter (e.g., role:admin,instructor)
        if ($arguments && ! in_array($role, $arguments)) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }
}
