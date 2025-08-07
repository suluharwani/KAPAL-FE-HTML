<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
// app/Config/Routes.php
$routes->group('', ['filter' => 'customer'], function($routes) {
    $routes->get('booking', 'Booking::index');
    $routes->post('booking/create', 'Booking::create');
    $routes->get('booking/confirmation/(:num)', 'Booking::confirmation/$1');
    $routes->get('profile', 'Profile::index');
    $routes->post('profile/update', 'Profile::update');
});

$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::login');
$routes->get('register', 'Auth::register');
$routes->post('reg', 'Auth::register');
$routes->get('logout', 'Auth::logout');

$routes->get('/', 'Home::index');
// app/Config/Routes.php

$routes->group('', ['filter' => 'auth'], function($routes) {
    // Route yang membutuhkan autentikasi
    $routes->get('profile', 'Profile::index');
    $routes->get('logout', 'Auth::logout');
});

// Route auth publik

$routes->get('forgot-password', 'Auth::forgotPassword');
$routes->post('forgot-password', 'Auth::attemptForgotPassword');
// app/Config/Routes.php

$routes->get('/wisata', 'Wisata::index');
$routes->get('/blog', 'Blog::index');
$routes->get('/blog/(:segment)', 'Blog::detail/$1');
$routes->get('/tentang', 'Tentang::index');
$routes->get('/kontak', 'Kontak::index');
$routes->post('/kontak/submit', 'Kontak::submit');
$routes->get('/faq', 'Faq::index');
// $routes->get('/booking', 'Booking::form');
// $routes->post('/booking/process', 'Booking::process');
// $routes->get('/booking/confirm', 'Booking::confirm');
// $routes->get('/login', 'Auth::login');
// $routes->post('/login', 'Auth::login');
// $routes->get('/register', 'Auth::register');
// $routes->post('/register', 'Auth::register');
// $routes->get('/logout', 'Auth::logout');