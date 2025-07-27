<?php namespace App\Controllers\Api;

use App\Models\IslandModel;
use App\Models\RouteModel;

class Islands extends BaseApiController
{
    protected $modelName = IslandModel::class;

    public function __construct()
    {
        $this->model = new IslandModel();
        $this->routeModel = new RouteModel();
    }

    public function index()
    {
        $params = $this->getPaginationParams();
        $islands = $this->model->getPaginated($params);

        return $this->respond([
            'status' => 200,
            'data' => $islands['data'],
            'pagination' => $islands['pagination']
        ]);
    }

    public function show($id = null)
    {
        $island = $this->model->find($id);

        if (!$island) {
            return $this->respondNotFound('Island not found');
        }

        // Get related routes
        $island['departure_routes'] = $this->routeModel
            ->select('routes.*, arrival.island_name as arrival_island')
            ->join('islands as arrival', 'arrival.island_id = routes.arrival_island_id')
            ->where('departure_island_id', $id)
            ->findAll();

        $island['arrival_routes'] = $this->routeModel
            ->select('routes.*, departure.island_name as departure_island')
            ->join('islands as departure', 'departure.island_id = routes.departure_island_id')
            ->where('arrival_island_id', $id)
            ->findAll();

        return $this->respond([
            'status' => 200,
            'data' => $island
        ]);
    }

    public function create()
    {
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only admin can create islands');
        }

        $rules = [
            'island_name' => 'required|min_length[3]|max_length[100]|is_unique[islands.island_name]',
            'description' => 'permit_empty',
            'image' => 'uploaded[image]|max_size[image,2048]|is_image[image]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'island_name' => $this->request->getVar('island_name'),
            'description' => $this->request->getVar('description')
        ];

        // Handle image upload
        if ($image = $this->request->getFile('image')) {
            if ($image->isValid() && !$image->hasMoved()) {
                $newName = $image->getRandomName();
                $image->move(ROOTPATH . 'public/uploads/islands', $newName);
                $data['image_url'] = 'uploads/islands/' . $newName;
            }
        }

        $islandId = $this->model->insert($data);

        if ($islandId) {
            return $this->respondCreated(['island_id' => $islandId]);
        } else {
            return $this->failServerError('Failed to create island');
        }
    }

    public function update($id = null)
    {
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only admin can update islands');
        }

        $island = $this->model->find($id);
        if (!$island) {
            return $this->respondNotFound('Island not found');
        }

        $rules = [
            'island_name' => 'permit_empty|min_length[3]|max_length[100]',
            'description' => 'permit_empty',
            'image' => 'permit_empty|uploaded[image]|max_size[image,2048]|is_image[image]'
        ];

        if ($this->request->getVar('island_name') && $this->request->getVar('island_name') !== $island['island_name']) {
            $rules['island_name'] .= '|is_unique[islands.island_name]';
        }

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'island_name' => $this->request->getVar('island_name') ?? $island['island_name'],
            'description' => $this->request->getVar('description') ?? $island['description']
        ];

        // Handle image upload
        if ($image = $this->request->getFile('image')) {
            if ($image->isValid() && !$image->hasMoved()) {
                // Delete old image if exists
                if ($island['image_url'] && file_exists(ROOTPATH . 'public/' . $island['image_url'])) {
                    unlink(ROOTPATH . 'public/' . $island['image_url']);
                }

                $newName = $image->getRandomName();
                $image->move(ROOTPATH . 'public/uploads/islands', $newName);
                $data['image_url'] = 'uploads/islands/' . $newName;
            }
        }

        if ($this->model->update($id, $data)) {
            return $this->respondUpdated(['island_id' => $id]);
        } else {
            return $this->failServerError('Failed to update island');
        }
    }

    public function delete($id = null)
    {
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only admin can delete islands');
        }

        $island = $this->model->find($id);
        if (!$island) {
            return $this->respondNotFound('Island not found');
        }

        // Check if island is used in routes
        $routeCount = $this->routeModel
            ->where('departure_island_id', $id)
            ->orWhere('arrival_island_id', $id)
            ->countAllResults();

        if ($routeCount > 0) {
            return $this->fail('Cannot delete island with associated routes', 400);
        }

        // Delete image if exists
        if ($island['image_url'] && file_exists(ROOTPATH . 'public/' . $island['image_url'])) {
            unlink(ROOTPATH . 'public/' . $island['image_url']);
        }

        if ($this->model->delete($id)) {
            return $this->respondDeleted();
        } else {
            return $this->failServerError('Failed to delete island');
        }
    }

    public function listWithRoutes()
    {
        $islands = $this->model->getIslandsWithRoutes();
        return $this->respond([
            'status' => 200,
            'data' => $islands
        ]);
    }
}