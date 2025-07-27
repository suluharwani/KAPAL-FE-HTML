<?php namespace App\Controllers\Api;

use App\Models\BoatModel;

class Boats extends BaseApiController
{
    protected $modelName = BoatModel::class;

    public function __construct()
    {
        $this->model = new BoatModel();
    }

    public function index()
    {
        $params = $this->getPaginationParams();
        $boats = $this->model->getPaginated($params);

        return $this->respond([
            'status' => 200,
            'data' => $boats['data'],
            'pagination' => $boats['pagination']
        ]);
    }

    public function show($id = null)
    {
        $boat = $this->model->find($id);

        if (!$boat) {
            return $this->respondNotFound('Boat not found');
        }

        return $this->respond([
            'status' => 200,
            'data' => $boat
        ]);
    }

    public function create()
    {
        $rules = [
            'boat_name' => 'required|min_length[3]|max_length[100]',
            'boat_type' => 'required|in_list[speedboat,traditional,luxury]',
            'capacity' => 'required|integer',
            'price_per_trip' => 'required|decimal'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'boat_name' => $this->request->getVar('boat_name'),
            'boat_type' => $this->request->getVar('boat_type'),
            'capacity' => $this->request->getVar('capacity'),
            'description' => $this->request->getVar('description'),
            'price_per_trip' => $this->request->getVar('price_per_trip'),
            'facilities' => $this->request->getVar('facilities')
        ];

        // Handle image upload
        if ($image = $this->request->getFile('image')) {
            if ($image->isValid() && !$image->hasMoved()) {
                $newName = $image->getRandomName();
                $image->move(ROOTPATH . 'public/uploads/boats', $newName);
                $data['image_url'] = 'uploads/boats/' . $newName;
            }
        }

        $boatId = $this->model->insert($data);

        if ($boatId) {
            return $this->respondCreated(['boat_id' => $boatId]);
        } else {
            return $this->failServerError('Failed to create boat');
        }
    }

    public function update($id = null)
    {
        $boat = $this->model->find($id);

        if (!$boat) {
            return $this->respondNotFound('Boat not found');
        }

        $rules = [
            'boat_name' => 'permit_empty|min_length[3]|max_length[100]',
            'boat_type' => 'permit_empty|in_list[speedboat,traditional,luxury]',
            'capacity' => 'permit_empty|integer',
            'price_per_trip' => 'permit_empty|decimal'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'boat_name' => $this->request->getVar('boat_name') ?? $boat['boat_name'],
            'boat_type' => $this->request->getVar('boat_type') ?? $boat['boat_type'],
            'capacity' => $this->request->getVar('capacity') ?? $boat['capacity'],
            'description' => $this->request->getVar('description') ?? $boat['description'],
            'price_per_trip' => $this->request->getVar('price_per_trip') ?? $boat['price_per_trip'],
            'facilities' => $this->request->getVar('facilities') ?? $boat['facilities']
        ];

        // Handle image upload
        if ($image = $this->request->getFile('image')) {
            if ($image->isValid() && !$image->hasMoved()) {
                // Delete old image if exists
                if ($boat['image_url'] && file_exists(ROOTPATH . 'public/' . $boat['image_url'])) {
                    unlink(ROOTPATH . 'public/' . $boat['image_url']);
                }

                $newName = $image->getRandomName();
                $image->move(ROOTPATH . 'public/uploads/boats', $newName);
                $data['image_url'] = 'uploads/boats/' . $newName;
            }
        }

        if ($this->model->update($id, $data)) {
            return $this->respondUpdated(['boat_id' => $id]);
        } else {
            return $this->failServerError('Failed to update boat');
        }
    }

    public function delete($id = null)
    {
        $boat = $this->model->find($id);

        if (!$boat) {
            return $this->respondNotFound('Boat not found');
        }

        // Delete associated image
        if ($boat['image_url'] && file_exists(ROOTPATH . 'public/' . $boat['image_url'])) {
            unlink(ROOTPATH . 'public/' . $boat['image_url']);
        }

        if ($this->model->delete($id)) {
            return $this->respondDeleted();
        } else {
            return $this->failServerError('Failed to delete boat');
        }
    }
}