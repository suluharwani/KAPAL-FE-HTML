<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/login', 'Login::index');
$routes->post('/login/process', 'Login::process');
$routes->get('/logout', 'Login::logout');
$routes->get('/dashboard', 'Dashboard::index');
$routes->group('boats',function($routes) {
    $routes->get('/', 'Boats::index');
    $routes->get('add', 'Boats::add');
    $routes->post('store', 'Boats::store');
    $routes->get('edit/(:num)', 'Boats::edit/$1');
    $routes->post('update/(:num)', 'Boats::update/$1');
    $routes->get('delete/(:num)', 'Boats::delete/$1');
    $routes->get('view/(:num)', 'Boats::view/$1');
});
$routes->group('bookings', function($routes) {
    $routes->get('/', 'Bookings::index');
    $routes->get('new', 'Bookings::new');
    $routes->post('create', 'Bookings::create');
    $routes->get('view/(:num)', 'Bookings::view/$1');
    $routes->get('cancel/(:num)', 'Bookings::cancel/$1');
    $routes->get('invoice/(:num)', 'Bookings::invoice/$1');
});