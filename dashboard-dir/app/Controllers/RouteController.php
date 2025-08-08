<?php namespace App\Controllers;

use App\Models\RouteModel;
use App\Models\IslandModel;

class RouteController extends BaseController
{
    protected $routeModel;
    protected $islandModel;

    public function __construct()
    {
        $this->routeModel = new RouteModel();
        $this->islandModel = new IslandModel();
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        $data = [
            'title' => 'Manage Routes',
            'routes' => $this->routeModel->getRoutesWithIslands(),
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/routes/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Add New Route',
            'islands' => $this->islandModel->findAll(),
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/routes/create', $data);
    }

    public function store()
    {
        $rules = [
            'departure_island_id' => 'required|numeric',
            'arrival_island_id' => 'required|numeric|different[departure_island_id]',
            'estimated_duration' => 'required|max_length[50]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'departure_island_id' => $this->request->getPost('departure_island_id'),
            'arrival_island_id' => $this->request->getPost('arrival_island_id'),
            'estimated_duration' => $this->request->getPost('estimated_duration'),
            'distance' => $this->request->getPost('distance'),
            'notes' => $this->request->getPost('notes')
        ];

        if ($this->routeModel->insert($data)) {
            return redirect()->to('/admin/routes')->with('success', 'Route added successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to add route');
        }
    }

    public function edit($id)
    {
        $route = $this->routeModel->getRouteDetails($id);
        if (!$route) {
            return redirect()->to('/admin/routes')->with('error', 'Route not found');
        }

        $data = [
            'title' => 'Edit Route',
            'route' => $route,
            'islands' => $this->islandModel->findAll(),
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/routes/edit', $data);
    }

    public function update($id)
    {
        $rules = [
            'departure_island_id' => 'required|numeric',
            'arrival_island_id' => 'required|numeric|different[departure_island_id]',
            'estimated_duration' => 'required|max_length[50]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'departure_island_id' => $this->request->getPost('departure_island_id'),
            'arrival_island_id' => $this->request->getPost('arrival_island_id'),
            'estimated_duration' => $this->request->getPost('estimated_duration'),
            'distance' => $this->request->getPost('distance'),
            'notes' => $this->request->getPost('notes')
        ];

        if ($this->routeModel->update($id, $data)) {
            return redirect()->to('/admin/routes')->with('success', 'Route updated successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update route');
        }
    }

    public function delete($id)
    {
        if ($this->routeModel->delete($id)) {
            return redirect()->to('/admin/routes')->with('success', 'Route deleted successfully');
        } else {
            return redirect()->to('/admin/routes')->with('error', 'Failed to delete route');
        }
    }
}