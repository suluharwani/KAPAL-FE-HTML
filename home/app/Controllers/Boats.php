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
}