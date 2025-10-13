<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// 🔹 Public pages
$routes->get('/', 'Home::index');
$routes->get('about', 'Home::about');
$routes->get('contact', 'Home::contact');

// 🔹 Auth routes
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::login');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::register');
$routes->get('logout', 'Auth::logout');
$routes->match(['get', 'post'], 'auth/login', 'Auth::login');
$routes->match(['get', 'post'], 'auth/register', 'Auth::register');
$routes->get('auth/logout', 'Auth::logout');


$routes->get('dashboard', 'Auth::dashboard'); 
// $routes->get('instructor/dashboard', 'Dashboard::instructor');
// $routes->get('student/dashboard', 'Dashboard::student');

$routes->post('/course/enroll', 'Course::enroll'); 


$routes->get('/', 'Auth::login');
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::login');
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::register');
$routes->get('/logout', 'Auth::logout');
$routes->match(['get', 'post'], '/dashboard', 'Auth::dashboard');

