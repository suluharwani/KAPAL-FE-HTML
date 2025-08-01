<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Auth Routes
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');

// Admin Routes
$routes->group('admin', ['filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'Admin::dashboard');
    
    // Boats Management
    $routes->get('boats', 'Admin::boats');
    $routes->get('boats/add', 'Admin::addBoat');
    $routes->post('boats/save', 'Admin::saveBoat');
    $routes->get('boats/edit/(:num)', 'Admin::editBoat/$1');
    $routes->post('boats/update/(:num)', 'Admin::updateBoat/$1');
    $routes->get('boats/delete/(:num)', 'Admin::deleteBoat/$1');
    
    // Bookings Management
    $routes->get('bookings', 'Admin::bookings');
    $routes->get('bookings/(:num)', 'Admin::viewBooking/$1');
    $routes->post('bookings/update-status/(:num)', 'Admin::updateBookingStatus/$1');
    
    // Payments Management
    $routes->get('payments', 'Admin::payments');
    $routes->get('payments/(:num)', 'Admin::viewPayment/$1');
    $routes->post('payments/verify/(:num)', 'Admin::verifyPayment/$1');
    
    // Routes Management
    $routes->get('routes', 'Admin::routes');
    $routes->get('routes/add', 'Admin::addRoute');
    $routes->post('routes/save', 'Admin::saveRoute');
    $routes->get('routes/edit/(:num)', 'Admin::editRoute/$1');
    $routes->post('routes/update/(:num)', 'Admin::updateRoute/$1');
    $routes->get('routes/delete/(:num)', 'Admin::deleteRoute/$1');
    
    // Schedules Management
    $routes->get('schedules', 'Admin::schedules');
    $routes->get('schedules/add', 'Admin::addSchedule');
    $routes->post('schedules/save', 'Admin::saveSchedule');
    $routes->get('schedules/edit/(:num)', 'Admin::editSchedule/$1');
    $routes->post('schedules/update/(:num)', 'Admin::updateSchedule/$1');
    $routes->get('schedules/delete/(:num)', 'Admin::deleteSchedule/$1');
    
    // Islands Management
    $routes->get('islands', 'Admin::islands');
    $routes->get('islands/add', 'Admin::addIsland');
    $routes->post('islands/save', 'Admin::saveIsland');
    $routes->get('islands/edit/(:num)', 'Admin::editIsland/$1');
    $routes->post('islands/update/(:num)', 'Admin::updateIsland/$1');
    $routes->get('islands/delete/(:num)', 'Admin::deleteIsland/$1');
    
    // Gallery Management
    $routes->get('gallery', 'Admin::gallery');
    $routes->get('gallery/add', 'Admin::addGalleryItem');
    $routes->post('gallery/save', 'Admin::saveGalleryItem');
    $routes->get('gallery/delete/(:num)', 'Admin::deleteGalleryItem/$1');
    
    // FAQs Management
    $routes->get('faqs', 'Admin::faqs');
    $routes->get('faqs/add', 'Admin::addFaq');
    $routes->post('faqs/save', 'Admin::saveFaq');
    $routes->get('faqs/edit/(:num)', 'Admin::editFaq/$1');
    $routes->post('faqs/update/(:num)', 'Admin::updateFaq/$1');
    $routes->get('faqs/delete/(:num)', 'Admin::deleteFaq/$1');
    
    // Testimonials Management
    $routes->get('testimonials', 'Admin::testimonials');
    $routes->post('testimonials/approve/(:num)', 'Admin::approveTestimonial/$1');
    $routes->get('testimonials/delete/(:num)', 'Admin::deleteTestimonial/$1');
    
    // Settings Management
    $routes->get('settings', 'Admin::settings');
    $routes->post('settings/update', 'Admin::updateSettings');
    
    // Reports
    $routes->get('reports', 'Admin::reports');
    $routes->post('reports/generate', 'Admin::generateReport');
    
    // Open Trips Management
    $routes->get('open-trips', 'Admin::openTrips');
    $routes->get('open-trips/requests', 'Admin::openTripRequests');
    $routes->post('open-trips/approve/(:num)', 'Admin::approveOpenTrip/$1');
    $routes->post('open-trips/reject/(:num)', 'Admin::rejectOpenTrip/$1');
});

$routes->get('/', function() {
    return redirect()->to('/login');
});