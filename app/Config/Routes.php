<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('/', 'Home::index');
$routes->get('/about', 'Home::about');
$routes->get('/contact', 'Home::contact');

// Guest-only routes (not logged in)
$routes->group('', ['filter' => 'noauth'], function($routes) {
    $routes->get('login', 'Auth::login');               // show login form
    $routes->post('login', 'Auth::loginPost');          // handle login submission

    $routes->get('register', 'Auth::register');         // show register form
    $routes->post('register', 'Auth::registerPost');    // handle register submission
});

// Authenticated-only routes (logged in)
$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'Auth::dashboard');       // ✅ one dashboard for all roles
    $routes->get('logout', 'Auth::logout');             // logout
});
