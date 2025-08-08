<?php namespace App\Controllers;

use App\Models\OpenTripModel;
use App\Models\RequestOpenTripModel;
use App\Models\ScheduleModel;
use App\Models\BoatModel;
use App\Models\RouteModel;
use App\Models\IslandModel;

class OpenTripController extends BaseController
{
    protected $openTripModel;
    protected $requestOpenTripModel;
    protected $scheduleModel;
    protected $boatModel;
    protected $routeModel;
    protected $islandModel;

    public function __construct()
    {
        $this->openTripModel = new OpenTripModel();
        $this->requestOpenTripModel = new RequestOpenTripModel();
        $this->scheduleModel = new ScheduleModel();
        $this->boatModel = new BoatModel();
        $this->routeModel = new RouteModel();
        $this->islandModel = new IslandModel();
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        $status = $this->request->getGet('status');
        
        $data = [
            'title' => 'Manage Open Trips',
            'openTrips' => $this->openTripModel->getOpenTripsWithDetails($status),
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/open-trips/index', $data);
    }

    public function show($id)
    {
        $openTrip = $this->openTripModel->getOpenTripDetails($id);
        if (!$openTrip) {
            return redirect()->to('/admin/open-trips')->with('error', 'Open trip not found');
        }

        $data = [
            'title' => 'Open Trip Details',
            'openTrip' => $openTrip,
            'bookings' => $this->openTripModel->getOpenTripBookings($id),
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/open-trips/show', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create Open Trip',
            'boats' => $this->boatModel->findAll(),
            'routes' => $this->routeModel->findAll(),
            'islands' => $this->islandModel->findAll(),
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/open-trips/create', $data);
    }

    public function store()
    {
        $rules = [
            'boat_id' => 'required|numeric',
            'route_id' => 'required|numeric',
            'departure_date' => 'required|valid_date',
            'departure_time' => 'required',
            'min_passengers' => 'required|numeric',
            'max_passengers' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Create schedule first
        $scheduleData = [
            'route_id' => $this->request->getPost('route_id'),
            'boat_id' => $this->request->getPost('boat_id'),
            'departure_date' => $this->request->getPost('departure_date'),
            'departure_time' => $this->request->getPost('departure_time'),
            'available_seats' => $this->request->getPost('max_passengers'),
            'is_open_trip' => 1
        ];

        $scheduleId = $this->scheduleModel->insert($scheduleData);

        // Create open trip request
        $requestData = [
            'user_id' => $this->session->get('user_id'),
            'boat_id' => $this->request->getPost('boat_id'),
            'route_id' => $this->request->getPost('route_id'),
            'proposed_date' => $this->request->getPost('departure_date'),
            'proposed_time' => $this->request->getPost('departure_time'),
            'min_passengers' => $this->request->getPost('min_passengers'),
            'max_passengers' => $this->request->getPost('max_passengers'),
            'notes' => $this->request->getPost('notes'),
            'status' => 'approved'
        ];

        $requestId = $this->requestOpenTripModel->insert($requestData);

        // Create open trip schedule
        $openTripData = [
            'request_id' => $requestId,
            'schedule_id' => $scheduleId,
            'reserved_seats' => 0,
            'available_seats' => $this->request->getPost('max_passengers'),
            'status' => 'upcoming'
        ];

        if ($this->openTripModel->insert($openTripData)) {
            return redirect()->to('/admin/open-trips')->with('success', 'Open trip created successfully');
        } else {
            // Clean up if failed
            $this->scheduleModel->delete($scheduleId);
            $this->requestOpenTripModel->delete($requestId);
            return redirect()->back()->withInput()->with('error', 'Failed to create open trip');
        }
    }

    public function updateStatus($id, $status)
    {
        $validStatuses = ['upcoming', 'ongoing', 'completed', 'canceled'];
        if (!in_array($status, $validStatuses)) {
            return redirect()->back()->with('error', 'Invalid status');
        }

        if ($this->openTripModel->update($id, ['status' => $status])) {
            return redirect()->back()->with('success', 'Status updated successfully');
        } else {
            return redirect()->back()->with('error', 'Failed to update status');
        }
    }
}