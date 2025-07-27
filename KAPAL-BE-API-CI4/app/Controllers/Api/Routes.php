<?php namespace App\Controllers\Api;

use App\Models\RouteModel;
use App\Models\IslandModel;
use App\Models\ScheduleModel;

class Routes extends BaseApiController
{
    protected $modelName = RouteModel::class;

    public function __construct()
    {
        $this->model = new RouteModel();
        $this->islandModel = new IslandModel();
        $this->scheduleModel = new ScheduleModel();
    }

    public function index()
    {
        $params = $this->getPaginationParams();
        
        // Additional filters for routes
        $params['departure_island'] = $this->request->getGet('departure_island');
        $params['arrival_island'] = $this->request->getGet('arrival_island');

        $routes = $this->model->getPaginated($params);

        return $this->respond([
            'status' => 200,
            'data' => $routes['data'],
            'pagination' => $routes['pagination']
        ]);
    }

    public function show($id = null)
    {
        $route = $this->model->getRouteDetails($id);

        if (!$route) {
            return $this->respondNotFound('Route not found');
        }

        // Get upcoming schedules
        $route['upcoming_schedules'] = $this->scheduleModel
            ->where('route_id', $id)
            ->where('departure_date >=', date('Y-m-d'))
            ->where('status', 'available')
            ->orderBy('departure_date', 'asc')
            ->orderBy('departure_time', 'asc')
            ->findAll();

        return $this->respond([
            'status' => 200,
            'data' => $route
        ]);
    }

    public function create()
    {
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only admin can create routes');
        }

        $rules = [
            'departure_island_id' => 'required|integer|is_not_unique[islands.island_id]',
            'arrival_island_id' => 'required|integer|is_not_unique[islands.island_id]|differs[departure_island_id]',
            'estimated_duration' => 'required|string|max_length[50]',
            'distance' => 'permit_empty|decimal',
            'notes' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        // Check if route already exists
        $existingRoute = $this->model
            ->where('departure_island_id', $this->request->getVar('departure_island_id'))
            ->where('arrival_island_id', $this->request->getVar('arrival_island_id'))
            ->first();

        if ($existingRoute) {
            return $this->fail('Route between these islands already exists', 400);
        }

        $data = [
            'departure_island_id' => $this->request->getVar('departure_island_id'),
            'arrival_island_id' => $this->request->getVar('arrival_island_id'),
            'estimated_duration' => $this->request->getVar('estimated_duration'),
            'distance' => $this->request->getVar('distance'),
            'notes' => $this->request->getVar('notes')
        ];

        $routeId = $this->model->insert($data);

        if ($routeId) {
            return $this->respondCreated(['route_id' => $routeId]);
        } else {
            return $this->failServerError('Failed to create route');
        }
    }

    public function update($id = null)
    {
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only admin can update routes');
        }

        $route = $this->model->find($id);
        if (!$route) {
            return $this->respondNotFound('Route not found');
        }

        $rules = [
            'departure_island_id' => 'permit_empty|integer|is_not_unique[islands.island_id]',
            'arrival_island_id' => 'permit_empty|integer|is_not_unique[islands.island_id]|differs[departure_island_id]',
            'estimated_duration' => 'permit_empty|string|max_length[50]',
            'distance' => 'permit_empty|decimal',
            'notes' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'departure_island_id' => $this->request->getVar('departure_island_id') ?? $route['departure_island_id'],
            'arrival_island_id' => $this->request->getVar('arrival_island_id') ?? $route['arrival_island_id'],
            'estimated_duration' => $this->request->getVar('estimated_duration') ?? $route['estimated_duration'],
            'distance' => $this->request->getVar('distance') ?? $route['distance'],
            'notes' => $this->request->getVar('notes') ?? $route['notes']
        ];

        // Check if route already exists (if islands are being changed)
        if ($data['departure_island_id'] != $route['departure_island_id'] || 
            $data['arrival_island_id'] != $route['arrival_island_id']) {
            
            $existingRoute = $this->model
                ->where('departure_island_id', $data['departure_island_id'])
                ->where('arrival_island_id', $data['arrival_island_id'])
                ->where('route_id !=', $id)
                ->first();

            if ($existingRoute) {
                return $this->fail('Route between these islands already exists', 400);
            }
        }

        if ($this->model->update($id, $data)) {
            return $this->respondUpdated(['route_id' => $id]);
        } else {
            return $this->failServerError('Failed to update route');
        }
    }

    public function delete($id = null)
    {
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only admin can delete routes');
        }

        $route = $this->model->find($id);
        if (!$route) {
            return $this->respondNotFound('Route not found');
        }

        // Check if route has schedules
        $scheduleCount = $this->scheduleModel->where('route_id', $id)->countAllResults();
        if ($scheduleCount > 0) {
            return $this->fail('Cannot delete route with associated schedules', 400);
        }

        if ($this->model->delete($id)) {
            return $this->respondDeleted();
        } else {
            return $this->failServerError('Failed to delete route');
        }
    }

    public function popular()
    {
        $limit = $this->request->getGet('limit') ?? 5;
        $popularRoutes = $this->model->getPopularRoutes($limit);

        return $this->respond([
            'status' => 200,
            'data' => $popularRoutes
        ]);
    }
}