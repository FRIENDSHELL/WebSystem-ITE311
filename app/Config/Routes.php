<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('/', 'Home::index');
$routes->get('/about', 'Home::about');
$routes->get('/contact', 'Home::contact');

// Guest-only routes (not logged in)

$routes->get('login', 'Auth::login');        // show login form
$routes->post('login', 'Auth::login');       // handle login submission

$routes->get('register', 'Auth::register');  // show register form
$routes->post('register', 'Auth::register'); // handle register submission



$routes->get('dashboard', 'Auth::dashboard'); // one dashboard for all roles
$routes->get('logout', 'Auth::logout');       // logout
