<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/about', 'Home::about');
$routes->get('/blog', 'Home::blog');
$routes->get('/blog/(:any)', 'Home::blogSingle/$1');
// $routes->get('/gallery', 'Home::gallery');
// $routes->get('/contact', 'Home::contact');
// $routes->get('/faq', 'Home::faq');

// Auth routes
$routes->group('auth', function($routes) {
    $routes->get('login', 'Auth::login');
    $routes->post('login', 'Auth::attemptLogin');
    $routes->get('register', 'Auth::register');
    $routes->post('register', 'Auth::attemptRegister');
    $routes->get('logout', 'Auth::logout');
});
$routes->post('auth/attemptLogin', 'Auth::attemptLogin');
$routes->post('auth/attemptRegister', 'Auth::attemptRegister');
$routes->get('auth/verify/(:any)', 'Auth::verify/$1');
// Boats routes
$routes->group('boats', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Boats::index');
    $routes->get('schedule', 'Boats::schedule');
    $routes->get('open-trip', 'Boats::openTripSchedule');
    $routes->post('check', 'Boats::checkAvailability');
    $routes->post('book', 'Boats::book');
    $routes->post('request-open-trip', 'Boats::openTripRequest');
    $routes->get('my-open-trip-requests', 'Boats::openTripRequests');
});
// Halaman Wisata
$routes->group('tour', function($routes) {
    $routes->get('waigeo', 'Tour::waigeo');
    $routes->get('misool', 'Tour::misool');
    $routes->get('salawati', 'Tour::salawati');
    $routes->get('batanta', 'Tour::batanta');
    $routes->get('packages', 'Tour::packages');
});

// Tentang Kami
$routes->group('about', function($routes) {
    $routes->get('/', 'About::index');
    $routes->get('team', 'About::team');
    $routes->get('testimonials', 'About::testimonials');
});

// Kontak
$routes->group('contact', function($routes) {
    $routes->get('/', 'Contact::index');
    $routes->post('submit', 'Contact::submit');
});

// FAQ
$routes->get('faq', 'Faq::index');

// Blog
$routes->group('blog', function($routes) {
    $routes->get('/', 'Blog::index');
    $routes->get('(:any)', 'Blog::view/$1');
});