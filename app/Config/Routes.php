<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// 🔹 Default route
$routes->get('/', 'Auth::login');

// 🔹 Public / Auth routes
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::login');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::register');
$routes->get('logout', 'Auth::logout');

// 🔹 Announcements (visible to all logged-in users)
$routes->get('announcements', 'Announcement::index');

// 🔹 Role-based dashboards
$routes->get('dashboard', 'Auth::dashboard'); // optional: fallback dashboard
$routes->post('dashboard/enroll', 'Auth::enroll'); // Enrollment endpoint

// 🔹 Teacher routes (protected by RoleAuth filter)
$routes->group('teacher', ['filter' => 'roleauth'], function($routes) {
    $routes->get('dashboard', 'Teacher::dashboard');
    // Add more teacher-only routes here if needed
});


// 🔹 Materials routes
$routes->get('/admin/course/(:num)/upload', 'Materials::upload/$1');
$routes->post('/admin/course/(:num)/upload', 'Materials::upload/$1');
$routes->get('/materials/view/(:num)', 'Materials::view/$1');
$routes->get('/materials/delete/(:num)', 'Materials::delete/$1');
$routes->get('/materials/download/(:num)', 'Materials::download/$1');

// 🔹 Admin routes (protected by RoleAuth filter)
$routes->group('admin', ['filter' => 'roleauth'], function($routes) {
    $routes->get('dashboard', 'Admin::dashboard');
    // Add more admin-only routes here if needed
});


// 🔹 Example student routes (optional, can add more later)
$routes->group('student', ['filter' => 'roleauth'], function($routes) {
    // Example: $routes->get('profile', 'Student::profile');
});

// 🔹 Course enroll route (if used in your project)
$routes->post('course/enroll', 'Course::enroll');

// 🔹 Extra static pages (optional)
$routes->get('about', 'Home::about');
$routes->get('contact', 'Home::contact');

// 🔹 Test route for debugging
$routes->get('test/announcements', 'Test::announcements');

// notification routes
$routes->get('/notifications', 'Notifications::get');
$routes->post('/notifications/mark_read/(:num)', 'Notifications::mark_as_read/$1');
