<?php namespace App\Controllers\Api;

use App\Models\ScheduleModel;
use App\Models\RouteModel;
use App\Models\BoatModel;

class Schedules extends BaseApiController
{
    protected $modelName = ScheduleModel::class;

    public function __construct()
    {
        $this->model = new ScheduleModel();
        $this->routeModel = new RouteModel();
        $this->boatModel = new BoatModel();
    }

    public function index()
    {
        $params = $this->getPaginationParams();
        
        // Additional filters for schedules
        $params['date_from'] = $this->request->getGet('date_from');
        $params['date_to'] = $this->request->getGet('date_to');
        $params['route_id'] = $this->request->getGet('route_id');
        $params['status'] = $this->request->getGet('status');
        $params['min_seats'] = $this->request->getGet('min_seats');

        $schedules = $this->model->getPaginated($params);

        return $this->respond([
            'status' => 200,
            'data' => $schedules['data'],
            'pagination' => $schedules['pagination']
        ]);
    }

    public function show($id = null)
    {
        $schedule = $this->model->find($id);

        if (!$schedule) {
            return $this->respondNotFound('Schedule not found');
        }

        // Load related data
        $schedule['route'] = $this->routeModel->getRouteDetails($schedule['route_id']);
        $schedule['boat'] = $this->boatModel->find($schedule['boat_id']);

        return $this->respond([
            'status' => 200,
            'data' => $schedule
        ]);
    }

    public function create()
    {
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only admin can create schedules');
        }

        $rules = [
            'route_id' => 'required|integer',
            'boat_id' => 'required|integer',
            'departure_time' => 'required|valid_time',
            'departure_date' => 'required|valid_date',
            'available_seats' => 'required|integer'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        // Check boat capacity
        $boat = $this->boatModel->find($this->request->getVar('boat_id'));
        if (!$boat) {
            return $this->failNotFound('Boat not found');
        }

        $availableSeats = (int) $this->request->getVar('available_seats');
        if ($availableSeats > $boat['capacity']) {
            return $this->fail('Available seats cannot exceed boat capacity', 400);
        }

        $data = [
            'route_id' => $this->request->getVar('route_id'),
            'boat_id' => $this->request->getVar('boat_id'),
            'departure_time' => $this->request->getVar('departure_time'),
            'departure_date' => $this->request->getVar('departure_date'),
            'available_seats' => $availableSeats,
            'status' => 'available'
        ];

        $scheduleId = $this->model->insert($data);

        if ($scheduleId) {
            return $this->respondCreated(['schedule_id' => $scheduleId]);
        } else {
            return $this->failServerError('Failed to create schedule');
        }
    }

    public function update($id = null)
    {
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only admin can update schedules');
        }

        $schedule = $this->model->find($id);
        if (!$schedule) {
            return $this->respondNotFound('Schedule not found');
        }

        $rules = [
            'route_id' => 'permit_empty|integer',
            'boat_id' => 'permit_empty|integer',
            'departure_time' => 'permit_empty|valid_time',
            'departure_date' => 'permit_empty|valid_date',
            'available_seats' => 'permit_empty|integer',
            'status' => 'permit_empty|in_list[available,full,canceled]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'route_id' => $this->request->getVar('route_id') ?? $schedule['route_id'],
            'boat_id' => $this->request->getVar('boat_id') ?? $schedule['boat_id'],
            'departure_time' => $this->request->getVar('departure_time') ?? $schedule['departure_time'],
            'departure_date' => $this->request->getVar('departure_date') ?? $schedule['departure_date'],
            'status' => $this->request->getVar('status') ?? $schedule['status']
        ];

        // Handle available seats update
        if ($this->request->getVar('available_seats')) {
            $availableSeats = (int) $this->request->getVar('available_seats');
            $boat = $this->boatModel->find($data['boat_id']);
            
            if ($availableSeats > $boat['capacity']) {
                return $this->fail('Available seats cannot exceed boat capacity', 400);
            }
            
            $data['available_seats'] = $availableSeats;
        }

        if ($this->model->update($id, $data)) {
            return $this->respondUpdated(['schedule_id' => $id]);
        } else {
            return $this->failServerError('Failed to update schedule');
        }
    }

    public function delete($id = null)
    {
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only admin can delete schedules');
        }

        $schedule = $this->model->find($id);
        if (!$schedule) {
            return $this->respondNotFound('Schedule not found');
        }

        // Check if there are bookings for this schedule
        $bookingModel = new BookingModel();
        $hasBookings = $bookingModel->where('schedule_id', $id)->countAllResults() > 0;

        if ($hasBookings) {
            return $this->fail('Cannot delete schedule with existing bookings', 400);
        }

        if ($this->model->delete($id)) {
            return $this->respondDeleted();
        } else {
            return $this->failServerError('Failed to delete schedule');
        }
    }
}