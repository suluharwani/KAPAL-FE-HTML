<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('searchSchedules', 'Home::searchSchedules');
$routes->post('searchSchedules', 'Home::searchSchedules');

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
    $routes->post('get-booking-details/(:num)', 'Boats::getBookingDetails/$1');
    $routes->post('add-open-trip-guest', 'Boats::addOpenTripGuest');
    $routes->post('invite-to-open-trip', 'Boats::inviteToOpenTrip');

    $routes->get('get-request-details/(:num)', 'Boats::getRequestDetails/$1');
    $routes->post('cancel-request', 'Boats::cancelRequest');
    $routes->post('complete-request', 'Boats::completeRequest');
    $routes->post('update-request', 'Boats::updateRequest');
    $routes->get('open-trip-details/(:num)', 'Boats::openTripDetails/$1');
    $routes->get('get-open-trip-id', 'Boats::getOpenTripId');

    $routes->get('open-trip-members/(:num)', 'Boats::manageOpenTripMembers/$1');
    $routes->get('get-member-details/(:num)', 'Boats::getMemberDetails/$1');
    $routes->post('add-member', 'Boats::addMember');
    $routes->get('get-member-edit/(:num)', 'Boats::getMemberEdit/$1');
    $routes->post('update-member', 'Boats::updateMember');
    $routes->post('delete-member', 'Boats::deleteMember');
    $routes->post('update-open-trip-price', 'Boats::updateOpenTripPrice');
    $routes->post('delete-all-members', 'Boats::deleteAllMembers');
    $routes->get('print-tickets', 'Boats::printTickets');
    $routes->post('send-whatsapp-tickets', 'Boats::sendWhatsAppTickets');

    $routes->get('download-tickets-pdf/(:num)', 'Boats::downloadTicketsPdf/$1');
    $routes->get('download-tickets-pdf', 'Boats::downloadTicketsPdf');
    $routes->post('approve-open-trip-request/(:num)', 'Boats::approveOpenTripRequest/$1');
    $routes->post('reject-open-trip-request/(:num)', 'Boats::rejectOpenTripRequest/$1');
});
// Halaman Wisata
// Halaman Wisata
$routes->group('tour', function($routes) {
    $routes->get('waigeo', 'Tour::waigeo');
    $routes->get('misool', 'Tour::misool');
    $routes->get('salawati', 'Tour::salawati');
    $routes->get('batanta', 'Tour::batanta');
    $routes->get('packages', 'Tour::packages');
    $routes->get('detail/(:segment)', 'Tour::detail/$1');
});
// Tentang Kami
$routes->group('about', function($routes) {
    $routes->get('/', 'About::index');
    $routes->get('team', 'About::team');
    $routes->get('testimonials', 'About::testimonials');
});

// Kontak
// Kontak
$routes->group('contact', function($routes) {
    $routes->get('/', 'Contact::index');
    $routes->post('submit', 'Contact::submit');
    $routes->get('test', 'Contact::test'); // Untuk debugging
});

// FAQ
$routes->get('faq', 'Faq::index');

// Blog
$routes->group('blog', function($routes) {
    $routes->get('/', 'Blog::index');
    $routes->get('(:segment)', 'Blog::post/$1');
    $routes->get('category/(:segment)', 'Blog::category/$1');
});
// Gallery routes
$routes->get('gallery', 'Gallery::index');
$routes->get('gallery/category/(:segment)', 'Gallery::category/$1');
// Blog Routes
// Booking routes - require auth
$routes->group('booking', ['filter' => 'auth'], function($routes) {
    $routes->get('create/(:num)', 'Booking::create/$1'); // schedule_id
    $routes->post('process', 'Booking::process');
    $routes->get('success/(:any)', 'Booking::success/$1'); // booking_code
    $routes->get('my-bookings', 'Booking::myBookings');
    $routes->get('detail/(:any)', 'Booking::detail/$1'); // booking_code
    $routes->get('print/(:any)', 'Booking::printTicket/$1'); // booking_code
    $routes->post('cancel', 'Booking::cancel');
});
$routes->get('auth/check', 'Auth::check');