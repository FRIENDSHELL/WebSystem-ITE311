<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// 🔹 Public pages
$routes->get('/', 'Home::index');
$routes->get('about', 'Home::about');
$routes->get('contact', 'Home::contact');

// 🔹 Auth routes
$routes->get('login', 'Auth::login');         // show login form
$routes->post('login', 'Auth::login');        // handle login submission
$routes->get('register', 'Auth::register');   // show register form
$routes->post('register', 'Auth::register');  // handle register submission
$routes->get('logout', 'Auth::logout');       // logout

$routes->get('dashboard', 'Auth::dashboard'); 
// $routes->get('instructor/dashboard', 'Dashboard::instructor');
// $routes->get('student/dashboard', 'Dashboard::student');
