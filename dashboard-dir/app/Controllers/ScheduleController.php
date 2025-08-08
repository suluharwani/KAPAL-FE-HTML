<?php namespace App\Controllers;

use App\Models\ScheduleModel;
use App\Models\BoatModel;
use App\Models\RouteModel;

class ScheduleController extends BaseController
{
    protected $scheduleModel;
    protected $boatModel;
    protected $routeModel;

    public function __construct()
    {
        $this->scheduleModel = new ScheduleModel();
        $this->boatModel = new BoatModel();
        $this->routeModel = new RouteModel();
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        $data = [
            'title' => 'Manage Schedules',
            'schedules' => $this->scheduleModel->getSchedulesWithDetails(),
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/schedules/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Add New Schedule',
            'boats' => $this->boatModel->findAll(),
            'routes' => $this->routeModel->getRoutesWithIslands(),
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/schedules/create', $data);
    }

    public function store()
    {
        $rules = [
            'route_id' => 'required|numeric',
            'boat_id' => 'required|numeric',
            'departure_date' => 'required|valid_date',
            'departure_time' => 'required',
            'available_seats' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'route_id' => $this->request->getPost('route_id'),
            'boat_id' => $this->request->getPost('boat_id'),
            'departure_date' => $this->request->getPost('departure_date'),
            'departure_time' => $this->request->getPost('departure_time'),
            'available_seats' => $this->request->getPost('available_seats'),
            'status' => 'available',
            'is_open_trip' => $this->request->getPost('is_open_trip') ? 1 : 0
        ];

        if ($this->scheduleModel->insert($data)) {
            return redirect()->to('/admin/schedules')->with('success', 'Schedule added successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to add schedule');
        }
    }

    public function edit($id)
    {
        $schedule = $this->scheduleModel->getScheduleDetails($id);
        if (!$schedule) {
            return redirect()->to('/admin/schedules')->with('error', 'Schedule not found');
        }

        $data = [
            'title' => 'Edit Schedule',
            'schedule' => $schedule,
            'boats' => $this->boatModel->findAll(),
            'routes' => $this->routeModel->getRoutesWithIslands(),
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/schedules/edit', $data);
    }

    public function update($id)
    {
        $rules = [
            'route_id' => 'required|numeric',
            'boat_id' => 'required|numeric',
            'departure_date' => 'required|valid_date',
            'departure_time' => 'required',
            'available_seats' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'route_id' => $this->request->getPost('route_id'),
            'boat_id' => $this->request->getPost('boat_id'),
            'departure_date' => $this->request->getPost('departure_date'),
            'departure_time' => $this->request->getPost('departure_time'),
            'available_seats' => $this->request->getPost('available_seats'),
            'status' => $this->request->getPost('status'),
            'is_open_trip' => $this->request->getPost('is_open_trip') ? 1 : 0
        ];

        if ($this->scheduleModel->update($id, $data)) {
            return redirect()->to('/admin/schedules')->with('success', 'Schedule updated successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update schedule');
        }
    }

    public function delete($id)
    {
        if ($this->scheduleModel->delete($id)) {
            return redirect()->to('/admin/schedules')->with('success', 'Schedule deleted successfully');
        } else {
            return redirect()->to('/admin/schedules')->with('error', 'Failed to delete schedule');
        }
    }
}