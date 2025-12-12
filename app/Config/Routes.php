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
    
    // Course management
    $routes->get('courses', 'Teacher::courses');
    $routes->get('create-course', 'Teacher::createCourse');
    $routes->post('store-course', 'Teacher::storeCourse');
    $routes->get('edit-course/(:num)', 'Teacher::editCourse/$1');
    $routes->post('update-course/(:num)', 'Teacher::updateCourse/$1');
    $routes->get('delete-course/(:num)', 'Teacher::deleteCourse/$1');
    
    // Enrollment management
    $routes->get('pending-enrollments', 'Teacher::pendingEnrollments');
    $routes->get('view-enrollment/(:num)', 'Teacher::viewEnrollment/$1');
    $routes->get('approve-enrollment/(:num)', 'Teacher::approveEnrollment/$1');
    $routes->post('reject-enrollment/(:num)', 'Teacher::rejectEnrollment/$1');
    
    // Debug route (temporary)
    $routes->get('debug-enrollments', 'Teacher::debugEnrollments');
});


// 🔹 Materials routes (accessible to teachers and admins)
$routes->group('materials', ['filter' => 'roleauth'], function($routes) {
    $routes->get('/', 'Materials::index');
    $routes->get('upload', 'Materials::upload');
    $routes->get('upload/(:num)', 'Materials::upload/$1');
    $routes->post('store', 'Materials::store');
    $routes->get('view/(:num)', 'Materials::view/$1');
    $routes->get('download/(:num)', 'Materials::download/$1');
    $routes->get('delete/(:num)', 'Materials::delete/$1');
});

// Legacy materials routes (for backward compatibility)
$routes->get('/admin/course/(:num)/upload', 'Materials::upload/$1');
$routes->post('/admin/course/(:num)/upload', 'Materials::upload/$1');

// 🔹 Admin routes (protected by RoleAuth filter)
$routes->group('admin', ['filter' => 'roleauth'], function($routes) {
    $routes->get('dashboard', 'Admin::dashboard');
    
    // User management
    $routes->get('users', 'Admin::users');
    $routes->post('users/add', 'Admin::addUser');
    $routes->post('users/update/(:num)', 'Admin::updateUser/$1');
    $routes->get('users/delete/(:num)', 'Admin::deleteUser/$1');
    
    // Course management
    $routes->get('courses', 'Admin::courses');
    $routes->post('courses/add', 'Admin::addCourse');
    $routes->post('courses/update/(:num)', 'Admin::updateCourse/$1');
    $routes->get('courses/delete/(:num)', 'Admin::deleteCourse/$1');
    
    // Semester management
    $routes->get('semesters', 'Admin::semesters');
    $routes->post('semesters/add', 'Admin::addSemester');
    $routes->post('semesters/update/(:num)', 'Admin::updateSemester/$1');
    $routes->get('semesters/delete/(:num)', 'Admin::deleteSemester/$1');
    
    // Term management
    $routes->get('terms', 'Admin::terms');
    $routes->post('terms/add', 'Admin::addTerm');
    $routes->post('terms/update/(:num)', 'Admin::updateTerm/$1');
    $routes->get('terms/delete/(:num)', 'Admin::deleteTerm/$1');
    
    // School year management
    $routes->get('school-years', 'Admin::schoolYears');
    $routes->post('school-years/add', 'Admin::addSchoolYear');
    $routes->post('school-years/update/(:num)', 'Admin::updateSchoolYear/$1');
    $routes->get('school-years/delete/(:num)', 'Admin::deleteSchoolYear/$1');
    
    // Enrollment management
    $routes->get('enrollments', 'Enrollment::manage');
    $routes->get('enrollments/view/(:num)', 'Enrollment::view/$1');
    // Note: Enrollment approval/rejection is handled by teachers, not admins
    $routes->get('enrollments/delete/(:num)', 'Enrollment::delete/$1');
    
    // Enrollment management
    $routes->get('enrollments', 'Admin::enrollments');
    $routes->get('course-roster/(:num)', 'Admin::courseRoster/$1');
    $routes->post('course-roster/(:num)/bulk-enroll', 'Admin::bulkEnrollStudents/$1');
    $routes->post('course-roster/(:num)/enroll-student', 'Admin::enrollStudentToCourse/$1');
    $routes->get('course-roster/(:num)/remove-student/(:num)', 'Admin::removeStudentFromCourse/$1/$2');
    $routes->get('enrollment-stats', 'Admin::getEnrollmentStats');
});


// 🔹 Enrollment routes
$routes->get('enrollment', 'Enrollment::index');
$routes->post('enrollment/submit', 'Enrollment::submit');
$routes->get('enrollment/success/(:num)', 'Enrollment::success/$1');

// 🔹 Student routes
$routes->group('student', ['filter' => 'roleauth'], function($routes) {
    $routes->get('dashboard', 'Student::dashboard');
    $routes->get('courses', 'Student::courses');
    $routes->get('enrollments', 'Student::enrollments');
    $routes->get('profile', 'Student::profile');
    $routes->get('check-updates', 'Student::checkEnrollmentUpdates');
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
$routes->post('/notifications/mark_unread/(:num)', 'Notifications::mark_as_unread/$1');


$routes->get('course/search', 'Course::search');

//course routes 
$routes->get('/course', 'Home::course');
$routes->get('/course/create', 'CourseController::create');
$routes->post('/course/store', 'CourseController::store');
$routes->get('/course/edit/(:num)', 'CourseController::edit/$1');
$routes->post('/course/update/(:num)', 'CourseController::update/$1');
$routes->get('/course/delete/(:num)', 'CourseController::delete/$1');
