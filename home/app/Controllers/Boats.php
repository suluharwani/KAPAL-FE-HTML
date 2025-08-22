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
    $passengers = $this->request->getPost('passengers');
    
    if ($this->request->getPost('open_trip_id')) {
        $openTripModel = new \App\Models\OpenTripSchedulesModel();
        $openTrip = $openTripModel->select('price_per_person, agreed_price, commission_rate')
                       ->where('open_trip_id', $this->request->getPost('open_trip_id'))
                       ->first();
        
        // Gunakan harga per orang jika ada, jika tidak gunakan harga default
        if ($openTrip && $openTrip['price_per_person'] > 0) {
            return $openTrip['price_per_person'] * $passengers;
        } else {
            // Fallback ke harga default
            $openTrip = $openTripModel->select('b.price_per_trip')
                           ->join('schedules s', 's.schedule_id = open_trip_schedules.schedule_id')
                           ->join('boats b', 'b.boat_id = s.boat_id')
                           ->where('open_trip_id', $this->request->getPost('open_trip_id'))
                           ->first();
            
            return $openTrip['price_per_trip'] * $passengers;
        }
    } else {
        // Regular booking logic
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
    $db = \Config\Database::connect();
    
    // Get trip information
    $tripInfo = $openTripModel->getOpenTripDetails($openTripId);
    
    if (!$tripInfo) {
        return redirect()->back()->with('error', 'Open trip not found');
    }
    
    // Get members for this open trip menggunakan Query Builder
    $builder = $db->table('bookings');
    $builder->select('bookings.*, 
                     users.full_name, 
                     users.email, 
                     users.phone,
                     COUNT(passengers.passenger_id) as actual_passenger_count'); // Ubah alias untuk menghindari konflik
    $builder->join('users', 'users.user_id = bookings.user_id', 'left');
    $builder->join('passengers', 'passengers.booking_id = bookings.booking_id', 'left');
    $builder->where('bookings.open_trip_id', $openTripId);
    $builder->groupBy('bookings.booking_id');
    $builder->orderBy('bookings.created_at', 'DESC');
    
    $members = $builder->get()->getResultArray();
    
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
// Boats.php - Updated addMember method
// Boats.php - Perbaiki method addMember
public function addMember()
{
    // Check if request is AJAX - gunakan method yang lebih reliable
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    
    if (!$isAjax) {
        log_message('error', 'Non-AJAX request to addMember');
        return $this->response->setStatusCode(403)->setJSON([
            'success' => false, 
            'error' => 'Forbidden: Only AJAX requests are allowed'
        ]);
    }

    // Load necessary models
    $bookingModel = new \App\Models\BookingModel();
    $userModel = new \App\Models\UserModel();
    $openTripModel = new \App\Models\OpenTripSchedulesModel();
    $passengerModel = new \App\Models\PassengerModel();

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
            'rules' => 'permit_empty|min_length[2]|max_length[100]',
            'errors' => [
                'min_length' => 'Guest name must be at least 2 characters long',
                'max_length' => 'Guest name cannot exceed 100 characters'
            ]
        ],
        'phone' => [
            'label' => 'Phone Number',
            'rules' => 'required|min_length[8]|max_length[15]|regex_match[/^[0-9+() -]+$/]',
            'errors' => [
                'required' => 'Phone number is required',
                'min_length' => 'Phone number must be at least 8 digits',
                'max_length' => 'Phone number cannot exceed 15 digits',
                'regex_match' => 'Phone number contains invalid characters'
            ]
        ],
        'custom_price' => [
            'label' => 'Custom Price',
            'rules' => 'permit_empty|numeric|greater_than_equal_to[0]',
            'errors' => [
                'numeric' => 'Custom price must be a number',
                'greater_than_equal_to' => 'Custom price cannot be negative'
            ]
        ]
    ]);

    // Run validation
    if (!$validation->withRequest($this->request)->run()) {
        return $this->response->setJSON([
            'success' => false, 
            'errors' => $validation->getErrors(),
            'message' => 'Validation failed. Please check your input.'
        ]);
    }

    // Get POST data
    $openTripId = (int) $this->request->getPost('open_trip_id');
    $memberType = $this->request->getPost('member_type');
    $passengerCount = (int) $this->request->getPost('passenger_count');
    $email = $this->request->getPost('email');
    $guestName = $this->request->getPost('guest_name');
    $phone = $this->request->getPost('phone');
    $customPrice = $this->request->getPost('custom_price');

    // Start database transaction
    $db = \Config\Database::connect();
    $db->transStart();

    try {
        // Get complete open trip details
        $openTrip = $openTripModel->select('open_trip_schedules.*, 
                                          boats.capacity,
                                          boats.boat_name,
                                          schedules.schedule_id,
                                          schedules.departure_date,
                                          schedules.departure_time,
                                          routes.route_id,
                                          dep.island_name as departure_island,
                                          arr.island_name as arrival_island,
                                          open_trip_schedules.agreed_price,
                                          open_trip_schedules.commission_rate')
                                 ->join('schedules', 'schedules.schedule_id = open_trip_schedules.schedule_id')
                                 ->join('boats', 'boats.boat_id = schedules.boat_id')
                                 ->join('routes', 'routes.route_id = schedules.route_id')
                                 ->join('islands dep', 'dep.island_id = routes.departure_island_id')
                                 ->join('islands arr', 'arr.island_id = routes.arrival_island_id')
                                 ->where('open_trip_schedules.open_trip_id', $openTripId)
                                 ->first();

        if (!$openTrip) {
            throw new \Exception('Open trip not found. Please check the trip ID.');
        }

        // Check if open trip is still upcoming
        $currentDateTime = date('Y-m-d H:i:s');
        $tripDateTime = $openTrip['departure_date'] . ' ' . $openTrip['departure_time'];
        
        if (strtotime($tripDateTime) <= strtotime($currentDateTime)) {
            throw new \Exception('Cannot add members to a trip that has already departed or is in progress.');
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
        $fullName = '';
        
        if ($memberType === 'registered') {
            if (empty($email)) {
                throw new \Exception('Email is required for registered users.');
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
            $fullName = $user['full_name'];
            $phone = $user['phone'] ?? $phone;

        } else {
            // Guest user validation
            if (empty($guestName)) {
                throw new \Exception('Guest name is required for guest registration.');
            }

            // Create a temporary user account for the guest
            $tempUsername = 'guest_' . time() . '_' . rand(1000, 9999);
            $tempEmail = $tempUsername . '@guest.rajaampatboats.com';
            $tempPassword = bin2hex(random_bytes(12));

            $tempUserData = [
                'username' => $tempUsername,
                'email' => $tempEmail,
                'password' => $tempPassword,
                'full_name' => $guestName,
                'phone' => $phone,
                'role' => 'customer',
                'email_verified' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $userId = $userModel->insert($tempUserData);
            
            if (!$userId) {
                throw new \Exception('Failed to create temporary guest account. Please try again.');
            }
            
            $fullName = $guestName;
        }

        // Calculate price per person
        $pricePerPerson = 0;
        $agreedPrice = $openTrip['agreed_price'] ?? 0;
        
        // Use custom price if provided, otherwise calculate from agreed price
        if (!empty($customPrice) && $customPrice > 0) {
            $pricePerPerson = (float) $customPrice;
        } else if ($agreedPrice > 0) {
            $pricePerPerson = $agreedPrice / $openTrip['capacity'];
        } else {
            throw new \Exception('No price set for this trip. Please set a price first.');
        }

        // Calculate total price
        $totalPrice = $pricePerPerson * $passengerCount;

        // Generate unique booking code
        $bookingCode = 'BOOK-' . strtoupper(uniqid());

        // Prepare booking data
        $bookingData = [
            'booking_code' => $bookingCode,
            'user_id' => $userId,
            'schedule_id' => $openTrip['schedule_id'],
            'open_trip_id' => $openTripId,
            'passenger_count' => $passengerCount,
            'custom_price' => !empty($customPrice) && $customPrice > 0 ? $customPrice : null,
            'total_price' => $totalPrice,
            'booking_status' => 'confirmed',
            'payment_status' => 'paid',
            'payment_method' => 'cash',
            'is_open_trip' => 1,
            'open_trip_type' => $memberType === 'registered' ? 'registered' : 'guest',
            'notes' => 'Added by admin: ' . (session('userData')['full_name'] ?? 'System'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Insert booking
        $bookingId = $bookingModel->insert($bookingData);
        
        if (!$bookingId) {
            throw new \Exception('Failed to create booking. Please try again.');
        }

        // Add passenger details
        $passengerData = [
            'booking_id' => $bookingId,
            'full_name' => $fullName,
            'phone' => $phone,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if (!$passengerModel->insert($passengerData)) {
            throw new \Exception('Failed to add passenger details. Please try again.');
        }

        // Update available seats in the open trip schedule
        $openTripModel->set('available_seats', 'available_seats - ' . $passengerCount, false)
                     ->where('open_trip_id', $openTripId)
                     ->update();

        // Commit transaction
        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new \Exception('Transaction failed. Please try again.');
        }

        // Get updated available seats
        $updatedOpenTrip = $openTripModel->find($openTripId);
        $updatedAvailableSeats = $updatedOpenTrip['available_seats'];

        // Log the activity
        $this->logActivity('add_member', [
            'open_trip_id' => $openTripId,
            'booking_id' => $bookingId,
            'user_id' => $userId,
            'passenger_count' => $passengerCount,
            'total_price' => $totalPrice,
            'member_type' => $memberType
        ]);

        // Prepare success response
        $response = [
            'success' => true,
            'message' => 'Member successfully added to the trip',
            'data' => [
                'booking_id' => $bookingId,
                'booking_code' => $bookingCode,
                'member_type' => $memberType,
                'passenger_count' => $passengerCount,
                'price_per_person' => $pricePerPerson,
                'total_price' => $totalPrice,
                'user_id' => $userId,
                'available_seats' => $updatedAvailableSeats,
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
        log_message('error', 'Add Member Error: ' . $e->getMessage());
        log_message('error', 'Trace: ' . $e->getTraceAsString());

        return $this->response->setJSON([
            'success' => false,
            'error' => $e->getMessage(),
            'message' => 'Failed to add member. Please try again.'
        ]);
    }
}
// Boats.php - updateMember method
public function updateMember()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
    }

    $validation = \Config\Services::validation();
    $validation->setRules([
        'booking_id' => 'required|numeric',
        'passenger_count' => 'required|numeric|greater_than[0]',
        'custom_price' => 'permit_empty|numeric',
        'open_trip_id' => 'required|numeric'
    ]);

    if (!$validation->withRequest($this->request)->run()) {
        return $this->response->setStatusCode(400)->setJSON(['errors' => $validation->getErrors()]);
    }

    $bookingModel = new \App\Models\BookingModel();
    $openTripModel = new \App\Models\OpenTripSchedulesModel();
    
    $bookingId = $this->request->getPost('booking_id');
    $passengerCount = $this->request->getPost('passenger_count');
    $customPrice = $this->request->getPost('custom_price');
    $openTripId = $this->request->getPost('open_trip_id');
    
    // Get current booking
    $currentBooking = $bookingModel->find($bookingId);
    if (!$currentBooking) {
        return $this->response->setJSON(['success' => false, 'error' => 'Booking not found']);
    }
    
    // Calculate seat difference - FIXED LOGIC
    $seatDifference = $passengerCount - $currentBooking['passenger_count'];
    
    // Get current available seats
    $openTrip = $openTripModel->find($openTripId);
    if (!$openTrip) {
        return $this->response->setJSON(['success' => false, 'error' => 'Open trip not found']);
    }
    
    // Check available seats - FIXED: Use current available seats instead of capacity
    if ($openTrip['available_seats'] < $seatDifference) {
        return $this->response->setJSON([
            'success' => false, 
            'error' => 'Not enough available seats. Only ' . $openTrip['available_seats'] . ' seats left.'
        ]);
    }
    
    // Calculate total price
    $totalPrice = 0;
    if (!empty($customPrice) && $customPrice > 0) {
        $totalPrice = $customPrice * $passengerCount;
    } else {
        // Get default price per person from open trip details
        $tripDetails = $openTripModel->getOpenTripDetails($openTripId);
        if ($tripDetails && $tripDetails['agreed_price'] > 0 && $tripDetails['capacity'] > 0) {
            $pricePerPerson = $tripDetails['agreed_price'] / $tripDetails['capacity'];
            $totalPrice = $pricePerPerson * $passengerCount;
        } else {
            // Fallback to boat price
            $boatModel = new BoatModel();
            $boat = $boatModel->find($tripDetails['boat_id']);
            $pricePerPerson = $boat['price_per_trip'] / $tripDetails['capacity'];
            $totalPrice = $pricePerPerson * $passengerCount;
        }
    }
    
    // Update booking
    $updateData = [
        'passenger_count' => $passengerCount,
        'total_price' => $totalPrice,
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    if (!empty($customPrice)) {
        $updateData['custom_price'] = $customPrice;
    } else {
        $updateData['custom_price'] = null;
    }
    
    try {
        $bookingModel->update($bookingId, $updateData);
        
        // Update available seats - FIXED: Use correct calculation
        $newAvailableSeats = $openTrip['available_seats'] - $seatDifference;
        $openTripModel->update($openTripId, [
            'available_seats' => $newAvailableSeats
        ]);
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Member updated successfully',
            'data' => [
                'available_seats' => $newAvailableSeats
            ]
        ]);
    } catch (\Exception $e) {
        return $this->response->setJSON([
            'success' => false,
            'error' => 'Failed to update member: ' . $e->getMessage()
        ]);
    }
}

// Helper method untuk logging
private function logActivity($action, $details, $userId = null)
{
    $logModel = new \App\Models\LogModel();
    
    $logData = [
        'user_id' => $userId ?? session('userData')['user_id'],
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
// Boats.php - tambahkan method untuk mengelola harga open trip
public function updateOpenTripPrice($openTripId)
{
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
    }

    $validation = \Config\Services::validation();
    $validation->setRules([
        'agreed_price' => 'required|numeric',
        'commission_rate' => 'required|numeric|less_than_equal_to[100]',
        'show_contact_admin' => 'required|in_list[0,1]'
    ]);

    if (!$validation->withRequest($this->request)->run()) {
        return $this->response->setStatusCode(400)->setJSON(['errors' => $validation->getErrors()]);
    }

    $openTripModel = new \App\Models\OpenTripSchedulesModel();
    
    $data = [
        'agreed_price' => $this->request->getPost('agreed_price'),
        'commission_rate' => $this->request->getPost('commission_rate'),
        'show_contact_admin' => $this->request->getPost('show_contact_admin')
    ];

    // Hitung harga per orang
    $openTrip = $openTripModel->find($openTripId);
    if ($openTrip) {
        $capacity = $this->getBoatCapacity($openTripId);
        if ($capacity) {
            $data['price_per_person'] = $data['agreed_price'] / $capacity['capacity'];
        }
    }

    try {
        $openTripModel->update($openTripId, $data);
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Harga open trip berhasil diperbarui'
        ]);
    } catch (\Exception $e) {
        return $this->response->setStatusCode(500)->setJSON(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
}
public function deleteAllMembers()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
    }

    $validation = \Config\Services::validation();
    $validation->setRules([
        'open_trip_id' => 'required|numeric'
    ]);

    if (!$validation->withRequest($this->request)->run()) {
        return $this->response->setStatusCode(400)->setJSON(['errors' => $validation->getErrors()]);
    }

    $bookingModel = new \App\Models\BookingModel();
    $openTripModel = new \App\Models\OpenTripSchedulesModel();
    $passengerModel = new \App\Models\PassengerModel();
    
    $openTripId = $this->request->getPost('open_trip_id');
    
    // Get all bookings for this open trip
    $bookings = $bookingModel->where('open_trip_id', $openTripId)->findAll();
    
    if (empty($bookings)) {
        return $this->response->setJSON(['success' => false, 'error' => 'No members found to delete']);
    }
    
    // Get open trip details
    $openTrip = $openTripModel->find($openTripId);
    if (!$openTrip) {
        return $this->response->setJSON(['success' => false, 'error' => 'Open trip not found']);
    }
    
    // Calculate total seats to restore
    $totalSeatsToRestore = 0;
    foreach ($bookings as $booking) {
        $totalSeatsToRestore += $booking['passenger_count'];
    }
    
    try {
        // Delete all passengers for these bookings
        $bookingIds = array_column($bookings, 'booking_id');
        if (!empty($bookingIds)) {
            $passengerModel->whereIn('booking_id', $bookingIds)->delete();
        }
        
        // Delete all bookings
        $bookingModel->where('open_trip_id', $openTripId)->delete();
        
        // Restore available seats
        $newAvailableSeats = $openTrip['available_seats'] + $totalSeatsToRestore;
        $openTripModel->update($openTripId, [
            'available_seats' => $newAvailableSeats
        ]);
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'All members deleted successfully',
            'data' => [
                'deleted_count' => count($bookings),
                'available_seats' => $newAvailableSeats
            ]
        ]);
    } catch (\Exception $e) {
        return $this->response->setJSON([
            'success' => false,
            'error' => 'Failed to delete all members: ' . $e->getMessage()
        ]);
    }
}
public function printTickets()
{
    $bookingIds = $this->request->getGet('booking_ids');
    $openTripId = $this->request->getGet('open_trip_id');
    
    $bookingModel = new \App\Models\BookingModel();
    $passengerModel = new \App\Models\PassengerModel();
    $openTripModel = new \App\Models\OpenTripSchedulesModel();
    
    // Get bookings to print
    if (!empty($bookingIds)) {
        $bookingIds = is_array($bookingIds) ? $bookingIds : explode(',', $bookingIds);
        $bookings = $bookingModel->whereIn('booking_id', $bookingIds)->findAll();
    } else if (!empty($openTripId)) {
        // Get all bookings for this open trip
        $bookings = $bookingModel->where('open_trip_id', $openTripId)->findAll();
    } else {
        return redirect()->back()->with('error', 'No bookings selected for printing');
    }
    
    if (empty($bookings)) {
        return redirect()->back()->with('error', 'No bookings found');
    }
    
    // Get open trip details
    $openTripDetails = [];
    if (!empty($openTripId)) {
        $openTripDetails = $openTripModel->getOpenTripDetails($openTripId);
    }
    
    // Get passenger details for each booking
    foreach ($bookings as &$booking) {
        $booking['passengers'] = $passengerModel->where('booking_id', $booking['booking_id'])->findAll();
    }
    
    $data = [
        'title' => 'Print Tickets',
        'bookings' => $bookings,
        'open_trip_details' => $openTripDetails
    ];
    
    return view('boats/print_tickets', $data);
}
public function sendWhatsAppTickets()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
    }

    $validation = \Config\Services::validation();
    $validation->setRules([
        'booking_ids' => 'required',
        'open_trip_id' => 'required|numeric'
    ]);

    if (!$validation->withRequest($this->request)->run()) {
        return $this->response->setStatusCode(400)->setJSON(['errors' => $validation->getErrors()]);
    }

    $bookingIds = $this->request->getPost('booking_ids');
    $openTripId = $this->request->getPost('open_trip_id');
    
    $bookingModel = new \App\Models\BookingModel();
    $passengerModel = new \App\Models\PassengerModel();
    
    // Get bookings
    $bookingIds = is_array($bookingIds) ? $bookingIds : explode(',', $bookingIds);
    $bookings = $bookingModel->whereIn('booking_id', $bookingIds)->findAll();
    
    if (empty($bookings)) {
        return $this->response->setJSON(['success' => false, 'error' => 'No bookings found']);
    }
    
    $results = [];
    
    foreach ($bookings as $booking) {
        // Get passenger details
        $passengers = $passengerModel->where('booking_id', $booking['booking_id'])->findAll();
        
        if (!empty($passengers)) {
            $phone = $passengers[0]['phone'];
            
            // Format phone number (replace 0 with +62)
            if (substr($phone, 0, 1) === '0') {
                $phone = '+62' . substr($phone, 1);
            }
            
            // Create WhatsApp message
            $message = "Halo! Berikut adalah tiket perjalanan Anda:\n\n";
            $message .= "Kode Booking: " . $booking['booking_code'] . "\n";
            $message .= "Jumlah Penumpang: " . $booking['passenger_count'] . "\n";
            $message .= "Status: " . ucfirst($booking['booking_status']) . "\n\n";
            $message .= "Terima kasih telah menggunakan layanan kami!";
            
            // Create WhatsApp link
            $whatsappLink = "https://wa.me/" . $phone . "?text=" . urlencode($message);
            
            $results[] = [
                'booking_code' => $booking['booking_code'],
                'phone' => $phone,
                'whatsapp_link' => $whatsappLink,
                'status' => 'success'
            ];
        }
    }
    
    return $this->response->setJSON([
        'success' => true,
        'message' => 'WhatsApp links generated successfully',
        'data' => $results
    ]);
}
public function deleteMember()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
    }

    $validation = \Config\Services::validation();
    $validation->setRules([
        'booking_id' => 'required|numeric',
        'open_trip_id' => 'required|numeric'
    ]);

    if (!$validation->withRequest($this->request)->run()) {
        return $this->response->setStatusCode(400)->setJSON(['errors' => $validation->getErrors()]);
    }

    $bookingModel = new \App\Models\BookingModel();
    $passengerModel = new \App\Models\PassengerModel();
    $openTripModel = new \App\Models\OpenTripSchedulesModel();
    
    $bookingId = $this->request->getPost('booking_id');
    $openTripId = $this->request->getPost('open_trip_id');
    
    // Start database transaction
    $db = \Config\Database::connect();
    $db->transStart();

    try {
        // Get booking details
        $booking = $bookingModel->find($bookingId);
        if (!$booking) {
            throw new \Exception('Booking not found');
        }
        
        // Get open trip details
        $openTrip = $openTripModel->find($openTripId);
        if (!$openTrip) {
            throw new \Exception('Open trip not found');
        }
        
        $passengerCount = $booking['passenger_count'];
        
        // Delete all passengers for this booking
        $passengerModel->where('booking_id', $bookingId)->delete();
        
        // Delete the booking
        $bookingModel->delete($bookingId);
        
        // Restore available seats
        $newAvailableSeats = $openTrip['available_seats'] + $passengerCount;
        $openTripModel->update($openTripId, [
            'available_seats' => $newAvailableSeats
        ]);
        
        // Commit transaction
        $db->transComplete();
        
        if ($db->transStatus() === false) {
            throw new \Exception('Transaction failed');
        }
        
        // Log the activity
        $this->logActivity('delete_member', [
            'open_trip_id' => $openTripId,
            'booking_id' => $bookingId,
            'passenger_count' => $passengerCount,
            'restored_seats' => $passengerCount
        ]);
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Member successfully deleted',
            'data' => [
                'deleted_booking_id' => $bookingId,
                'restored_seats' => $passengerCount,
                'available_seats' => $newAvailableSeats
            ]
        ]);
        
    } catch (\Exception $e) {
        // Rollback transaction on error
        $db->transRollback();
        
        // Log the error
        log_message('error', 'Delete Member Error: ' . $e->getMessage());
        
        return $this->response->setJSON([
            'success' => false,
            'error' => $e->getMessage(),
            'message' => 'Failed to delete member. Please try again.'
        ]);
    }
}
}