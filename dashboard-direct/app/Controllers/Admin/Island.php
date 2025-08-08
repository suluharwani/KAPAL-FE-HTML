<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\IslandModel;

class Island extends BaseController
{
    protected $islandModel;

    public function __construct()
    {
        $this->islandModel = new IslandModel();
    }

    public function index()
    {
        $data = [
            'islands' => $this->islandModel->getIslandsWithRoutes()
        ];

        return view('admin/island/index', $data);
    }

    public function add()
    {
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'island_name' => 'required|min_length[3]|max_length[100]|is_unique[islands.island_name]',
                'description' => 'permit_empty',
                'image' => 'uploaded[image]|max_size[image,2048]|is_image[image]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $image = $this->request->getFile('image');
            $imageName = $image->getRandomName();
            $image->move(ROOTPATH . 'public/assets/img/islands', $imageName);

            $data = [
                'island_name' => $this->request->getPost('island_name'),
                'description' => $this->request->getPost('description'),
                'image_url' => 'assets/img/islands/' . $imageName
            ];

            if ($this->islandModel->save($data)) {
                return redirect()->to('/admin/island')->with('success', 'Island added successfully');
            } else {
                // Delete uploaded image if failed to save
                unlink(ROOTPATH . 'public/assets/img/islands/' . $imageName);
                return redirect()->back()->with('error', 'Failed to add island');
            }
        }

        return view('admin/island/add');
    }

    public function edit($id)
    {
        $island = $this->islandModel->find($id);
        if (!$island) {
            return redirect()->to('/admin/island')->with('error', 'Island not found');
        }

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'island_name' => "required|min_length[3]|max_length[100]|is_unique[islands.island_name,island_id,{$id}]",
                'description' => 'permit_empty',
                'image' => 'max_size[image,2048]|is_image[image]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $data = [
                'island_id' => $id,
                'island_name' => $this->request->getPost('island_name'),
                'description' => $this->request->getPost('description')
            ];

            $image = $this->request->getFile('image');
            if ($image && $image->isValid() && !$image->hasMoved()) {
                // Delete old image
                if ($island['image_url'] && file_exists(ROOTPATH . 'public/' . $island['image_url'])) {
                    unlink(ROOTPATH . 'public/' . $island['image_url']);
                }

                // Upload new image
                $imageName = $image->getRandomName();
                $image->move(ROOTPATH . 'public/assets/img/islands', $imageName);
                $data['image_url'] = 'assets/img/islands/' . $imageName;
            }

            if ($this->islandModel->save($data)) {
                return redirect()->to('/admin/island')->with('success', 'Island updated successfully');
            } else {
                return redirect()->back()->with('error', 'Failed to update island');
            }
        }

        return view('admin/island/edit', ['island' => $island]);
    }

    public function delete($id)
    {
        $island = $this->islandModel->find($id);
        if (!$island) {
            return redirect()->to('/admin/island')->with('error', 'Island not found');
        }

        // Check if island is used in routes
        $routeModel = new RouteModel();
        $usedAsDeparture = $routeModel->where('departure_island_id', $id)->countAllResults();
        $usedAsArrival = $routeModel->where('arrival_island_id', $id)->countAllResults();

        if ($usedAsDeparture > 0 || $usedAsArrival > 0) {
            return redirect()->to('/admin/island')->with('error', 'Cannot delete island because it is used in routes');
        }

        // Delete image if exists
        if ($island['image_url'] && file_exists(ROOTPATH . 'public/' . $island['image_url'])) {
            unlink(ROOTPATH . 'public/' . $island['image_url']);
        }

        if ($this->islandModel->delete($id)) {
            return redirect()->to('/admin/island')->with('success', 'Island deleted successfully');
        } else {
            return redirect()->to('/admin/island')->with('error', 'Failed to delete island');
        }
    }
}