<?php namespace App\Controllers;

use App\Models\BoatModel;
use App\Models\IslandModel;
use App\Models\RouteModel;
use App\Models\ScheduleModel;

class Boats extends BaseController
{
    public function index()
    {
        $model = new BoatModel();
        $islandModel = new IslandModel();
        
        $data = [
            'title' => 'Pesan Kapal - Raja Ampat Boat Services',
            'boats' => $model->findAll(),
            'islands' => $islandModel->findAll()
        ];
        
        $this->render('boats/index', $data);
    }

    public function schedule()
    {
        $routeModel = new RouteModel();
        $scheduleModel = new ScheduleModel();
        $boatModel = new BoatModel();
        $islandModel = new IslandModel();
        
        $data = [
            'title' => 'Jadwal Kapal - Raja Ampat Boat Services',
            'routes' => $routeModel->findAll(),
            'schedules' => $scheduleModel->getSchedulesWithDetails(),
            'boats' => $boatModel->findAll(),
            'islands' => $islandModel->findAll()
        ];
        
        $this->render('boats/schedule', $data);
    }

    public function checkAvailability()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'from_island' => 'required',
            'to_island' => 'required',
            'departure_date' => 'required|valid_date',
            'passengers' => 'required|numeric'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setStatusCode(400)->setJSON(['errors' => $validation->getErrors()]);
        }

        $scheduleModel = new ScheduleModel();
        $fromIsland = $this->request->getPost('from_island');
        $toIsland = $this->request->getPost('to_island');
        $departureDate = $this->request->getPost('departure_date');
        $passengers = $this->request->getPost('passengers');

        $schedules = $scheduleModel->getAvailableSchedules($fromIsland, $toIsland, $departureDate, $passengers);

        return $this->response->setJSON([
            'success' => true,
            'data' => $schedules
        ]);
    }

public function book()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
    }

    if (!$this->session->get('isLoggedIn')) {
        return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
    }

    $validation = \Config\Services::validation();
    $validation->setRules([
        'schedule_id' => 'permit_empty|numeric',
        'open_trip_id' => 'permit_empty|numeric',
        'passengers' => 'required|numeric|greater_than[0]',
        'passenger_names' => 'required'
    ]);

    if (!$validation->withRequest($this->request)->run()) {
        return $this->response->setStatusCode(400)->setJSON(['errors' => $validation->getErrors()]);
    }

    $bookingModel = new \App\Models\BookingModel();
    $bookingCode = 'BOOK-' . strtoupper(uniqid());
    
    $data = [
        'booking_code' => $bookingCode,
        'user_id' => $this->session->get('user_id'),
        'schedule_id' => $this->request->getPost('schedule_id'),
        'passenger_count' => $this->request->getPost('passengers'),
        'total_price' => $this->calculateTotalPrice(),
        'booking_status' => 'pending',
        'payment_status' => 'pending',
        'is_open_trip' => $this->request->getPost('open_trip_id') ? 1 : 0,
        'open_trip_id' => $this->request->getPost('open_trip_id'),
        'open_trip_type' => $this->request->getPost('open_trip_id') ? 'public' : null
    ];

    try {
        $bookingId = $bookingModel->insert($data);
        
        // Save passenger details
        $passengerModel = new \App\Models\PassengerModel();
        $passengerNames = $this->request->getPost('passenger_names');
        
        foreach ($passengerNames as $name) {
            $passengerModel->insert([
                'booking_id' => $bookingId,
                'full_name' => $name
            ]);
        }
        
        // Update available seats if open trip
        if ($this->request->getPost('open_trip_id')) {
            $openTripModel = new \App\Models\OpenTripSchedulesModel();
            $openTripModel->decrement('available_seats', $this->request->getPost('passengers'), 
                ['open_trip_id' => $this->request->getPost('open_trip_id')]);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Pemesanan berhasil. Silakan lakukan pembayaran.',
            'data' => [
                'booking_code' => $bookingCode,
                'schedule_id' => $this->request->getPost('schedule_id'),
                'open_trip_id' => $this->request->getPost('open_trip_id'),
                'passengers' => $this->request->getPost('passengers'),
                'total_price' => $data['total_price']
            ]
        ]);
    } catch (\Exception $e) {
        return $this->response->setStatusCode(500)->setJSON(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
}

private function calculateTotalPrice()
{
    // Calculate based on schedule or open trip price
    // This is a simplified version - adjust according to your business logic
    $passengers = $this->request->getPost('passengers');
    
    if ($this->request->getPost('open_trip_id')) {
        $openTripModel = new \App\Models\OpenTripSchedulesModel();
        $openTrip = $openTripModel->select('b.price_per_trip')
                       ->join('schedules s', 's.schedule_id = open_trip_schedules.schedule_id')
                       ->join('boats b', 'b.boat_id = s.boat_id')
                       ->where('open_trip_id', $this->request->getPost('open_trip_id'))
                       ->first();
        
        return $openTrip['price_per_trip'] * $passengers;
    } else {
        $scheduleModel = new \App\Models\ScheduleModel();
        $schedule = $scheduleModel->select('b.price_per_trip')
                      ->join('boats b', 'b.boat_id = schedules.boat_id')
                      ->where('schedule_id', $this->request->getPost('schedule_id'))
                      ->first();
        
        return $schedule['price_per_trip'] * $passengers;
    }
}
public function openTripRequest()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
    }

    if (!$this->session->get('isLoggedIn')) {
        return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
    }

    $validation = \Config\Services::validation();
    $validation->setRules([
        'boat_id' => 'required|numeric',
        'route_id' => 'required|numeric',
        'proposed_date' => 'required|valid_date',
        'proposed_time' => 'required',
        'min_passengers' => 'required|numeric|greater_than[1]',
        'max_passengers' => 'required|numeric|greater_than[1]',
        'notes' => 'permit_empty'
    ]);

    if (!$validation->withRequest($this->request)->run()) {
        return $this->response->setStatusCode(400)->setJSON(['errors' => $validation->getErrors()]);
    }

    $requestModel = new \App\Models\RequestOpenTripsModel();// Now properly imported
    
    $data = [
        'user_id' => $_SESSION['userData']['user_id'],
        'boat_id' => $this->request->getPost('boat_id'),
        'route_id' => $this->request->getPost('route_id'),
        'proposed_date' => $this->request->getPost('proposed_date'),
        'proposed_time' => $this->request->getPost('proposed_time'),
        'min_passengers' => $this->request->getPost('min_passengers'),
        'max_passengers' => $this->request->getPost('max_passengers'),
        'notes' => $this->request->getPost('notes'),
        'status' => 'pending'
    ];

    try {
        $requestId = $requestModel->insert($data);
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Permintaan open trip berhasil diajukan. Kami akan menghubungi Anda setelah verifikasi.',
            'request_id' => $requestId
        ]);
    } catch (\Exception $e) {
        return $this->response->setStatusCode(500)->setJSON(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
}

public function openTripSchedule()
{
    $routeModel = new \App\Models\RouteModel(); // Add full namespace
    $modelBoat = new BoatModel();
    $model = new \App\Models\OpenTripSchedulesModel();
    $data = [
        'title' => 'Open Trip - Raja Ampat Boat Services',
        'openTrips' => $model->getUpcomingOpenTrips(),
        'boats' => $modelBoat->findAll(),
        'routes' => $routeModel->getRoutesWithIslands(),
    ];
    
    $this->render('boats/open_trip', $data);
}
public function openTripRequests()
{
    if (!$this->session->get('isLoggedIn')) {
        return redirect()->to('/login');
    }

    $requestModel = new \App\Models\RequestOpenTripsModel();
    $boatModel = new BoatModel();
    $routeModel = new RouteModel();
    $data = [
        'title' => 'My Open Trip Requests - Raja Ampat Boat Services',
        'requests' => $requestModel->getUserRequests($_SESSION['userData']['user_id']),
        'boats' => $boatModel->findAll(),
        'routes' => $routeModel->getRoutesWithIslands(),
    ];
    
    $this->render('boats/open_trip_requests', $data);
}
public function manageOpenTripMembers($openTripId)
{
    if (!$this->session->get('isLoggedIn')) {
        return redirect()->to('/login');
    }

    $openTripModel = new \App\Models\OpenTripSchedulesModel();
    $bookingModel = new \App\Models\BookingModel();
    
    // Get trip information
    $tripInfo = $openTripModel->getOpenTripDetails($openTripId);
    
    if (!$tripInfo) {
        return redirect()->back()->with('error', 'Open trip not found');
    }
    
    // Get members for this open trip
    $members = $bookingModel->getOpenTripMembers($openTripId);
    
    // Get capacity information
    $capacityInfo = $this->getCapacityInfo($openTripId);
    
    $data = [
        'title' => 'Manage Open Trip Members',
        'tripInfo' => array_merge($tripInfo, $capacityInfo),
        'members' => $members
    ];
    
    $this->render('boats/open_trip_members', $data);
}
public function getBookingDetails($bookingId)
{
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
    }

    $bookingModel = new \App\Models\BookingModel();
    $passengerModel = new \App\Models\PassengerModel();
    $paymentModel = new \App\Models\PaymentModel();
    
    // Get booking details
    $booking = $bookingModel->getBookingWithUser($bookingId);
    if (!$booking) {
        return $this->response->setStatusCode(404)->setJSON(['error' => 'Booking not found']);
    }
    
    // Get passengers
    $passengers = $passengerModel->where('booking_id', $bookingId)->findAll();
    
    // Get payments
    $payments = $paymentModel->where('booking_id', $bookingId)->findAll();
    
    $html = view('boats/booking_details', [
        'booking' => $booking,
        'passengers' => $passengers,
        'payments' => $payments
    ]);
    
    return $this->response->setJSON([
        'success' => true,
        'html' => $html
    ]);
}

public function addOpenTripGuest()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
    }

    $validation = \Config\Services::validation();
    $validation->setRules([
        'open_trip_id' => 'required|numeric',
        'full_name' => 'required|min_length[3]',
        'phone' => 'required',
        'passenger_count' => 'required|numeric|greater_than[0]',
        'passenger_names' => 'required'
    ]);

    if (!$validation->withRequest($this->request)->run()) {
        return $this->response->setStatusCode(400)->setJSON(['errors' => $validation->getErrors()]);
    }

    $bookingModel = new \App\Models\BookingModel();
    $passengerModel = new \App\Models\PassengerModel();
    $openTripModel = new \App\Models\OpenTripSchedulesModel();
    
    $openTripId = $this->request->getPost('open_trip_id');
    $passengerCount = $this->request->getPost('passenger_count');
    
    // Check available seats
    $openTrip = $openTripModel->find($openTripId);
    if (!$openTrip || $openTrip['available_seats'] < $passengerCount) {
        return $this->response->setStatusCode(400)->setJSON(['error' => 'Not enough available seats']);
    }
    
    // Create booking
    $bookingCode = 'BOOK-' . strtoupper(uniqid());
    
    $bookingData = [
        'booking_code' => $bookingCode,
        'open_trip_id' => $openTripId,
        'passenger_count' => $passengerCount,
        'total_price' => $openTrip['price_per_trip'] * $passengerCount,
        'booking_status' => 'pending',
        'payment_status' => 'pending',
        'is_open_trip' => 1,
        'open_trip_type' => 'public'
    ];
    
    try {
        $bookingId = $bookingModel->insert($bookingData);
        
        // Save passenger details
        $passengerNames = $this->request->getPost('passenger_names');
        
        foreach ($passengerNames as $name) {
            $passengerModel->insert([
                'booking_id' => $bookingId,
                'full_name' => $name
            ]);
        }
        
        // Update available seats
        $openTripModel->decrement('available_seats', $passengerCount, ['open_trip_id' => $openTripId]);
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Guest member added successfully'
        ]);
    } catch (\Exception $e) {
        return $this->response->setStatusCode(500)->setJSON(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
}

public function inviteToOpenTrip()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
    }

    $validation = \Config\Services::validation();
    $validation->setRules([
        'open_trip_id' => 'required|numeric',
        'email' => 'required|valid_email',
        'passenger_count' => 'required|numeric|greater_than[0]'
    ]);

    if (!$validation->withRequest($this->request)->run()) {
        return $this->response->setStatusCode(400)->setJSON(['errors' => $validation->getErrors()]);
    }

    $userModel = new \App\Models\UserModel();
    $notificationModel = new \App\Models\NotificationModel();
    $openTripModel = new \App\Models\OpenTripSchedulesModel();
    
    $openTripId = $this->request->getPost('open_trip_id');
    $email = $this->request->getPost('email');
    $passengerCount = $this->request->getPost('passenger_count');
    
    // Check user exists
    $user = $userModel->where('email', $email)->first();
    if (!$user) {
        return $this->response->setStatusCode(400)->setJSON(['error' => 'User not found']);
    }
    
    // Check available seats
    $openTrip = $openTripModel->find($openTripId);
    if (!$openTrip || $openTrip['available_seats'] < $passengerCount) {
        return $this->response->setStatusCode(400)->setJSON(['error' => 'Not enough available seats']);
    }
    
    // Create invitation notification
    $notificationData = [
        'user_id' => $user['user_id'],
        'title' => 'Open Trip Invitation',
        'message' => 'You have been invited to join an open trip',
        'type' => 'open_trip_invitation',
        'reference_id' => $openTripId,
        'is_read' => 0,
        'metadata' => json_encode([
            'passenger_count' => $passengerCount,
            'invited_by' => $this->session->get('user_id')
        ])
    ];
    
    try {
        $notificationModel->insert($notificationData);
        
        // Here you would typically also send an email notification
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Invitation sent successfully'
        ]);
    } catch (\Exception $e) {
        return $this->response->setStatusCode(500)->setJSON(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
}
public function getOpenTripId()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
    }

    $requestId = $this->request->getGet('request_id');
    $openTripModel = new \App\Models\OpenTripSchedulesModel();
    
    $openTrip = $openTripModel->where('request_id', $requestId)->first();
    
    if ($openTrip) {
        return $this->response->setJSON([
            'success' => true,
            'open_trip_id' => $openTrip['open_trip_id']
        ]);
    }
    
    return $this->response->setJSON([
        'success' => false,
        'error' => 'Open trip not found for this request'
    ]);
}
// public function manageOpenTripMembers($openTripId)
// {
//     if (!$this->session->get('isLoggedIn')) {
//         return redirect()->to('/login');
//     }

//     $openTripModel = new \App\Models\OpenTripSchedulesModel();
//     $bookingModel = new \App\Models\BookingModel();
    
//     // Get trip information
//     $tripInfo = $openTripModel->getOpenTripDetails($openTripId);
//     if (!$tripInfo) {
//         return redirect()->back()->with('error', 'Open trip not found');
//     }
    
//     // Get members for this open trip
//     $members = $bookingModel->getOpenTripMembers($openTripId);
    
//     $data = [
//         'title' => 'Manage Open Trip Members',
//         'tripInfo' => $tripInfo,
//         'members' => $members
//     ];
    
//     return view('boats/open_trip_members', $data);
// }

public function getMemberDetails($bookingId)
{
    $bookingModel = new \App\Models\BookingModel();
    $passengerModel = new \App\Models\PassengerModel();
    
    $member = $bookingModel->getBookingWithUser($bookingId);
    if (!$member) {
        return $this->response->setJSON(['success' => false, 'error' => 'Member not found']);
    }
    
    $passengers = $passengerModel->where('booking_id', $bookingId)->findAll();
    
    $html = view('boats/member_details', [
        'member' => $member,
        'passengers' => $passengers
    ]);
    
    return $this->response->setJSON(['success' => true, 'html' => $html]);
}

// Boats.php - full version of addMember method
public function addMember()
{
    // Check if request is AJAX
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(403)->setJSON([
            'success' => false, 
            'error' => 'Forbidden: Only AJAX requests are allowed'
        ]);
    }

    // Load necessary models
    $bookingModel = new \App\Models\BookingModel();
    $userModel = new \App\Models\UserModel();
    $openTripModel = new \App\Models\OpenTripSchedulesModel();
    $scheduleModel = new \App\Models\ScheduleModel();
    $passengerModel = new \App\Models\PassengerModel();
    $routeModel = new \App\Models\RouteModel();
    $islandModel = new \App\Models\IslandModel();

    // Set validation rules
    $validation = \Config\Services::validation();
    $validation->setRules([
        'open_trip_id' => [
            'label' => 'Open Trip ID',
            'rules' => 'required|numeric',
            'errors' => [
                'required' => 'Open Trip ID is required',
                'numeric' => 'Open Trip ID must be a number'
            ]
        ],
        'member_type' => [
            'label' => 'Member Type',
            'rules' => 'required|in_list[registered,guest]',
            'errors' => [
                'required' => 'Member type is required',
                'in_list' => 'Member type must be either registered or guest'
            ]
        ],
        'passenger_count' => [
            'label' => 'Passenger Count',
            'rules' => 'required|numeric|greater_than[0]',
            'errors' => [
                'required' => 'Passenger count is required',
                'numeric' => 'Passenger count must be a number',
                'greater_than' => 'Passenger count must be greater than 0'
            ]
        ],
        'email' => [
            'label' => 'Email',
            'rules' => 'permit_empty|valid_email',
            'errors' => [
                'valid_email' => 'Please provide a valid email address'
            ]
        ],
        'guest_name' => [
            'label' => 'Guest Name',
            'rules' => 'permit_empty|min_length[3]|max_length[100]',
            'errors' => [
                'min_length' => 'Guest name must be at least 3 characters long',
                'max_length' => 'Guest name cannot exceed 100 characters'
            ]
        ],
        'phone' => [
            'label' => 'Phone Number',
            'rules' => 'permit_empty|min_length[10]|max_length[15]|regex_match[/^[0-9+() -]+$/]',
            'errors' => [
                'min_length' => 'Phone number must be at least 10 digits',
                'max_length' => 'Phone number cannot exceed 15 digits',
                'regex_match' => 'Phone number contains invalid characters'
            ]
        ]
    ]);

    // Run validation
    if (!$validation->withRequest($this->request)->run()) {
        return $this->response->setJSON([
            'success' => false, 
            'errors' => $validation->getErrors(),
            'message' => 'Validation failed'
        ]);
    }

    // Get POST data
    $openTripId = (int) $this->request->getPost('open_trip_id');
    $memberType = $this->request->getPost('member_type');
    $passengerCount = (int) $this->request->getPost('passenger_count');
    $email = $this->request->getPost('email');
    $guestName = $this->request->getPost('guest_name');
    $phone = $this->request->getPost('phone');

    // Start database transaction
    $db = \Config\Database::connect();
    $db->transStart();

    try {
        // Get complete open trip details with route information
        $openTrip = $openTripModel->select('open_trip_schedules.*, 
                                          boats.price_per_trip, 
                                          boats.capacity,
                                          boats.boat_name,
                                          schedules.schedule_id,
                                          schedules.departure_date,
                                          schedules.departure_time,
                                          routes.route_id,
                                          routes.departure_island_id,
                                          routes.arrival_island_id,
                                          dep.island_name as departure_island,
                                          arr.island_name as arrival_island')
                                 ->join('schedules', 'schedules.schedule_id = open_trip_schedules.schedule_id')
                                 ->join('boats', 'boats.boat_id = schedules.boat_id')
                                 ->join('routes', 'routes.route_id = schedules.route_id')
                                 ->join('islands dep', 'dep.island_id = routes.departure_island_id')
                                 ->join('islands arr', 'arr.island_id = routes.arrival_island_id')
                                 ->where('open_trip_schedules.open_trip_id', $openTripId)
                                 ->first();

        if (!$openTrip) {
            throw new \Exception('Open trip not found or has been deleted');
        }

        // Check if open trip is still upcoming
        $currentDateTime = date('Y-m-d H:i:s');
        $tripDateTime = $openTrip['departure_date'] . ' ' . $openTrip['departure_time'];
        
        if (strtotime($tripDateTime) <= strtotime($currentDateTime)) {
            throw new \Exception('Cannot add members to a trip that has already departed or is in progress');
        }

        // Check total booked seats for this open trip
        $totalBookedResult = $bookingModel->where('open_trip_id', $openTripId)
                                        ->selectSum('passenger_count')
                                        ->first();

        $totalBookedSeats = (int) ($totalBookedResult['passenger_count'] ?? 0);
        $availableSeats = $openTrip['capacity'] - $totalBookedSeats;

        // Validate available seats
        if ($availableSeats <= 0) {
            throw new \Exception('Trip is already fully booked. No seats available.');
        }

        if ($passengerCount > $availableSeats) {
            throw new \Exception('Not enough available seats. Only ' . $availableSeats . ' seat(s) left. Please reduce the number of passengers.');
        }

        // Validate member type specific requirements
        $userId = null;
        
        if ($memberType === 'registered') {
            if (empty($email)) {
                throw new \Exception('Email is required for registered users');
            }

            $user = $userModel->where('email', $email)->first();
            
            if (!$user) {
                throw new \Exception('User not found with email: ' . $email . '. Please check the email address or use guest registration.');
            }

            // Check if user already has a booking for this trip
            $existingBooking = $bookingModel->where('open_trip_id', $openTripId)
                                          ->where('user_id', $user['user_id'])
                                          ->first();
            
            if ($existingBooking) {
                throw new \Exception('This user already has a booking for this trip. Booking ID: ' . $existingBooking['booking_code']);
            }

            $userId = $user['user_id'];

        } else {
            // Guest user validation
            if (empty($guestName)) {
                throw new \Exception('Guest name is required');
            }

            if (empty($phone)) {
                throw new \Exception('Phone number is required for guest registration');
            }

            // Validate phone format
            if (!preg_match('/^[0-9+() -]+$/', $phone)) {
                throw new \Exception('Phone number contains invalid characters. Only numbers, plus, parentheses, spaces, and hyphens are allowed.');
            }

            // Create a temporary user account for the guest
            $tempUsername = 'guest_' . time() . '_' . rand(1000, 9999);
            $tempEmail = $tempUsername . '@guest.rajaampatboats.com';
            $tempPassword = bin2hex(random_bytes(12)); // Generate secure random password

            $tempUserData = [
                'username' => $tempUsername,
                'email' => $tempEmail,
                'password' => password_hash($tempPassword, PASSWORD_DEFAULT),
                'full_name' => $guestName,
                'phone' => $phone,
                'role' => 'customer',
                'email_verified' => 1, // Mark as verified since we're creating it
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $userId = $userModel->insert($tempUserData);
            
            if (!$userId) {
                throw new \Exception('Failed to create temporary guest account');
            }
        }

        // Calculate total price
        $pricePerTrip = (float) $openTrip['price_per_trip'];
        $totalPrice = $pricePerTrip * $passengerCount;

        // Generate unique booking code
        $bookingCode = 'BOOK-' . strtoupper(uniqid()) . '-' . strtoupper(substr(md5(time()), 0, 4));

        // Prepare booking data
        $bookingData = [
            'booking_code' => $bookingCode,
            'user_id' => $userId,
            'schedule_id' => $openTrip['schedule_id'],
            'open_trip_id' => $openTripId,
            'passenger_count' => $passengerCount,
            'total_price' => $totalPrice,
            'booking_status' => 'confirmed',
            'payment_status' => 'paid',
            'payment_method' => 'cash', // Assuming cash payment for admin-added members
            'is_open_trip' => 1,
            'open_trip_type' => $memberType === 'registered' ? 'reserved' : 'public',
            'notes' => 'Added by admin: ' . $this->session->get('userData')['full_name'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Insert booking
        $bookingId = $bookingModel->insert($bookingData);
        
        if (!$bookingId) {
            throw new \Exception('Failed to create booking. Please try again.');
        }

        // Add passenger details (for both registered and guest users)
        $passengerData = [
            'booking_id' => $bookingId,
            'full_name' => $memberType === 'registered' ? $user['full_name'] : $guestName,
            'phone' => $memberType === 'registered' ? $user['phone'] : $phone,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $passengerModel->insert($passengerData);

        // Update available seats in the open trip schedule
        $openTripModel->set('available_seats', 'available_seats - ' . $passengerCount, false)
                     ->where('open_trip_id', $openTripId)
                     ->update();

        // Commit transaction
        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new \Exception('Transaction failed. Please try again.');
        }

        // Prepare success response
        $response = [
            'success' => true,
            'message' => 'Member successfully added to the trip',
            'data' => [
                'booking_id' => $bookingId,
                'booking_code' => $bookingCode,
                'member_type' => $memberType,
                'passenger_count' => $passengerCount,
                'total_price' => number_format($totalPrice, 2),
                'user_id' => $userId,
                'available_seats' => $availableSeats - $passengerCount,
                'trip_details' => [
                    'route' => $openTrip['departure_island'] . ' - ' . $openTrip['arrival_island'],
                    'date' => $openTrip['departure_date'],
                    'time' => $openTrip['departure_time'],
                    'boat' => $openTrip['boat_name']
                ]
            ]
        ];

        return $this->response->setJSON($response);

    } catch (\Exception $e) {
        // Rollback transaction on error
        $db->transRollback();

        // Log the error
        log_message('error', 'Add Member Error: ' . $e->getMessage() . ' - ' . $e->getTraceAsString());

        return $this->response->setJSON([
            'success' => false,
            'error' => $e->getMessage(),
            'message' => 'Failed to add member. Please try again.'
        ]);
    }
}
// Boats.php - helper method for logging
private function logActivity($action, $details, $userId = null)
{
    $logModel = new \App\Models\LogModel(); // Anda perlu membuat model Log jika belum ada
    
    $logData = [
        'user_id' => $userId ?? $this->session->get('userData')['user_id'],
        'action' => $action,
        'details' => is_array($details) ? json_encode($details) : $details,
        'ip_address' => $this->request->getIPAddress(),
        'user_agent' => $this->request->getUserAgent()->getAgentString(),
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    try {
        $logModel->insert($logData);
    } catch (\Exception $e) {
        // Silent fail for logging errors
    }
}
public function getCapacityInfo($openTripId)
{
    $openTripModel = new \App\Models\OpenTripSchedulesModel();
    $bookingModel = new \App\Models\BookingModel();
    
    $openTrip = $openTripModel->select('boats.capacity')
                             ->join('schedules', 'schedules.schedule_id = open_trip_schedules.schedule_id')
                             ->join('boats', 'boats.boat_id = schedules.boat_id')
                             ->where('open_trip_schedules.open_trip_id', $openTripId)
                             ->first();
    
    if (!$openTrip) {
        return false;
    }
    
    $totalBookedResult = $bookingModel->where('open_trip_id', $openTripId)
                                    ->selectSum('passenger_count')
                                    ->first();
    
    $totalBookedSeats = (int) ($totalBookedResult['passenger_count'] ?? 0);
    $availableSeats = $openTrip['capacity'] - $totalBookedSeats;
    
    return [
        'capacity' => $openTrip['capacity'],
        'booked' => $totalBookedSeats,
        'available' => $availableSeats
    ];
}
// Add other CRUD methods (update, delete) similarly
}