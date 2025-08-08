<?php namespace App\Controllers;

use App\Models\OpenTripModel;
use App\Models\OpenTripScheduleModel;
use App\Models\BoatModel;
use App\Models\IslandModel;
use App\Models\BookingModel;

class OpenTrip extends BaseController
{
    public function index()
    {
        $openTripModel = new OpenTripScheduleModel();
        $islandModel = new IslandModel();

        $data = [
            'openTrips' => $openTripModel->getUpcomingOpenTrips(),
            'islands' => $islandModel->findAll()
        ];

        return view('open_trip_index', $data);
    }

    public function show($openTripId)
    {
        $openTripModel = new OpenTripScheduleModel();
        $openTrip = $openTripModel->getOpenTripWithDetails($openTripId);

        if (!$openTrip) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'openTrip' => $openTrip,
            'availableSeats' => $openTrip['available_seats']
        ];

        return view('open_trip_detail', $data);
    }

    public function join()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please login to join open trip');
        }

        $openTripId = $this->request->getGet('open_trip_id');
        $passengerCount = $this->request->getGet('passengers') ?? 1;

        $openTripModel = new OpenTripScheduleModel();
        $openTrip = $openTripModel->find($openTripId);

        if (!$openTrip) {
            return redirect()->back()->with('error', 'Open trip not found');
        }

        // Check availability
        if ($openTrip['available_seats'] < $passengerCount) {
            return redirect()->back()->with('error', 'Not enough available seats');
        }

        $scheduleModel = new ScheduleModel();
        $schedule = $scheduleModel->find($openTrip['schedule_id']);

        $boatModel = new BoatModel();
        $boat = $boatModel->find($schedule['boat_id']);

        $totalPrice = $boat['price_per_trip'] * $passengerCount;

        $data = [
            'openTrip' => $openTrip,
            'schedule' => $schedule,
            'boat' => $boat,
            'passenger_count' => $passengerCount,
            'total_price' => $totalPrice
        ];

        return view('open_trip_join', $data);
    }

    public function store()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please login to join open trip');
        }

        $rules = [
            'open_trip_id' => 'required|numeric',
            'passenger_count' => 'required|numeric|greater_than[0]',
            'passengers' => 'required',
            'passengers.*.full_name' => 'required|min_length[3]|max_length[100]',
            'passengers.*.identity_number' => 'permit_empty|max_length[50]',
            'passengers.*.phone' => 'permit_empty|max_length[20]',
            'passengers.*.age' => 'permit_empty|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $openTripModel = new OpenTripScheduleModel();
        $bookingModel = new BookingModel();
        $passengerModel = new PassengerModel();

        $openTripId = $this->request->getPost('open_trip_id');
        $passengerCount = $this->request->getPost('passenger_count');
        $passengers = $this->request->getPost('passengers');

        $openTrip = $openTripModel->find($openTripId);
        if (!$openTrip) {
            return redirect()->back()->with('error', 'Open trip not found');
        }

        // Check availability again
        if ($openTrip['available_seats'] < $passengerCount) {
            return redirect()->back()->with('error', 'Not enough available seats');
        }

        $scheduleModel = new ScheduleModel();
        $schedule = $scheduleModel->find($openTrip['schedule_id']);

        $boatModel = new BoatModel();
        $boat = $boatModel->find($schedule['boat_id']);

        $totalPrice = $boat['price_per_trip'] * $passengerCount;

        // Create booking
        $bookingData = [
            'booking_code' => $bookingModel->generateBookingCode(),
            'user_id' => session()->get('user_id'),
            'schedule_id' => $schedule['schedule_id'],
            'passenger_count' => $passengerCount,
            'total_price' => $totalPrice,
            'booking_status' => 'pending',
            'payment_method' => 'transfer',
            'payment_status' => 'pending',
            'is_open_trip' => 1,
            'open_trip_id' => $openTripId,
            'open_trip_type' => 'public'
        ];

        if ($bookingId = $bookingModel->insert($bookingData)) {
            // Add passengers
            $passengerModel->addPassengers($bookingId, $passengers);

            // Update available seats
            $openTripModel->decrementAvailableSeats($openTripId, $passengerCount);

            return redirect()->to('/booking/' . $bookingId)->with('success', 'Open trip booking created successfully');
        } else {
            return redirect()->back()->with('error', 'Failed to create open trip booking');
        }
    }

    public function request()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please login to request open trip');
        }

        $boatModel = new BoatModel();
        $islandModel = new IslandModel();
        $routeModel = new RouteModel();

        $data = [
            'boats' => $boatModel->findAll(),
            'islands' => $islandModel->findAll(),
            'routes' => $routeModel->findAll()
        ];

        return view('open_trip_request', $data);
    }

    public function storeRequest()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please login to request open trip');
        }

        $rules = [
            'boat_id' => 'required|numeric',
            'route_id' => 'required|numeric',
            'proposed_date' => 'required|valid_date',
            'proposed_time' => 'required',
            'min_passengers' => 'required|numeric|greater_than[0]',
            'max_passengers' => 'required|numeric|greater_than[0]|greater_than_equal_to[min_passengers]',
            'notes' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $openTripModel = new OpenTripModel();

        $data = [
            'user_id' => session()->get('user_id'),
            'boat_id' => $this->request->getPost('boat_id'),
            'route_id' => $this->request->getPost('route_id'),
            'proposed_date' => $this->request->getPost('proposed_date'),
            'proposed_time' => $this->request->getPost('proposed_time'),
            'min_passengers' => $this->request->getPost('min_passengers'),
            'max_passengers' => $this->request->getPost('max_passengers'),
            'notes' => $this->request->getPost('notes'),
            'status' => 'pending'
        ];

        if ($openTripModel->save($data)) {
            return redirect()->to('/open-trip')->with('success', 'Open trip request submitted successfully. We will notify you once approved.');
        } else {
            return redirect()->back()->with('error', 'Failed to submit open trip request');
        }
    }
}