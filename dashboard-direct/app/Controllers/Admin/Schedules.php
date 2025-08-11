<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ScheduleModel;
use App\Models\BoatModel;
use App\Models\RouteModel;
use App\Models\IslandModel;

class Schedules extends BaseController
{
    protected $scheduleModel;
    protected $boatModel;
    protected $routeModel;
    protected $islandModel;

    public function __construct()
    {
        $this->scheduleModel = new ScheduleModel();
        $this->boatModel = new BoatModel();
        $this->routeModel = new RouteModel();
        $this->islandModel = new IslandModel();
    }

    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/admin/login');
        }

        $data = [
            'schedules' => $this->scheduleModel->getSchedulesWithDetails(10),
            'pager' => $this->scheduleModel->pager
        ];

        return view('admin/schedules/index', $data);
    }

    public function add()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/admin/login');
        }

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'route_id' => 'required|numeric',
                'boat_id' => 'required|numeric',
                'departure_date' => 'required|valid_date',
                'departure_time' => 'required',
                'available_seats' => 'required|numeric',
                'status' => 'required|in_list[available,full,canceled]',
                'is_open_trip' => 'permit_empty'
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

            if ($this->scheduleModel->save($data)) {
                return redirect()->to('/admin/schedules')->with('success', 'Schedule added successfully');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to add schedule');
            }
        }

        $data = [
            'boats' => $this->boatModel->findAll(),
            'routes' => $this->routeModel->findAll(),
            'islands' => $this->islandModel->findAll()
        ];

        return view('admin/schedules/add', $data);
    }

    public function edit($id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/admin/login');
        }

        $schedule = $this->scheduleModel->find($id);
        if (!$schedule) {
            return redirect()->to('/admin/schedules')->with('error', 'Schedule not found');
        }

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'route_id' => 'required|numeric',
                'boat_id' => 'required|numeric',
                'departure_date' => 'required|valid_date',
                'departure_time' => 'required',
                'available_seats' => 'required|numeric',
                'status' => 'required|in_list[available,full,canceled]',
                'is_open_trip' => 'permit_empty'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $data = [
                'schedule_id' => $id,
                'route_id' => $this->request->getPost('route_id'),
                'boat_id' => $this->request->getPost('boat_id'),
                'departure_date' => $this->request->getPost('departure_date'),
                'departure_time' => $this->request->getPost('departure_time'),
                'available_seats' => $this->request->getPost('available_seats'),
                'status' => $this->request->getPost('status'),
                'is_open_trip' => $this->request->getPost('is_open_trip') ? 1 : 0
            ];

            if ($this->scheduleModel->save($data)) {
                return redirect()->to('/admin/schedules')->with('success', 'Schedule updated successfully');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to update schedule');
            }
        }

        $data = [
            'schedule' => $schedule,
            'boats' => $this->boatModel->findAll(),
            'routes' => $this->routeModel->findAll(),
            'islands' => $this->islandModel->findAll()
        ];

        return view('admin/schedules/edit', $data);
    }

    public function delete($id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/admin/login');
        }

        if ($this->scheduleModel->delete($id)) {
            return redirect()->to('/admin/schedules')->with('success', 'Schedule deleted successfully');
        } else {
            return redirect()->to('/admin/schedules')->with('error', 'Failed to delete schedule');
        }
    }
}