<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BoatModel;

class Boats extends BaseController
{
    protected $boatModel;

    public function __construct()
    {
        $this->boatModel = new BoatModel();
    }

    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/admin/login');
        }

        $data = [
            'boats' => $this->boatModel->getBoatsWithPagination(10),
            'pager' => $this->boatModel->pager
        ];

        return view('admin/boats/index', $data);
    }

    public function add()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/admin/login');
        }

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'boat_name' => 'required|min_length[3]|max_length[100]',
                'boat_type' => 'required|in_list[speedboat,traditional,luxury]',
                'capacity' => 'required|numeric',
                'price_per_trip' => 'required|numeric',
                'description' => 'permit_empty',
                'facilities' => 'permit_empty'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $data = [
                'boat_name' => $this->request->getPost('boat_name'),
                'boat_type' => $this->request->getPost('boat_type'),
                'capacity' => $this->request->getPost('capacity'),
                'description' => $this->request->getPost('description'),
                'price_per_trip' => $this->request->getPost('price_per_trip'),
                'facilities' => $this->request->getPost('facilities')
            ];

            // Handle image upload
            $image = $this->request->getFile('image');
            if ($image->isValid() && !$image->hasMoved()) {
                $newName = $image->getRandomName();
                $image->move(ROOTPATH . 'public/assets/img/boats', $newName);
                $data['image_url'] = 'assets/img/boats/' . $newName;
            }

            if ($this->boatModel->save($data)) {
                return redirect()->to('/admin/boats')->with('success', 'Boat added successfully');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to add boat');
            }
        }

        return view('admin/boats/add');
    }

    public function edit($id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/admin/login');
        }

        $boat = $this->boatModel->find($id);
        if (!$boat) {
            return redirect()->to('/admin/boats')->with('error', 'Boat not found');
        }

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'boat_name' => 'required|min_length[3]|max_length[100]',
                'boat_type' => 'required|in_list[speedboat,traditional,luxury]',
                'capacity' => 'required|numeric',
                'price_per_trip' => 'required|numeric',
                'description' => 'permit_empty',
                'facilities' => 'permit_empty'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $data = [
                'boat_id' => $id,
                'boat_name' => $this->request->getPost('boat_name'),
                'boat_type' => $this->request->getPost('boat_type'),
                'capacity' => $this->request->getPost('capacity'),
                'description' => $this->request->getPost('description'),
                'price_per_trip' => $this->request->getPost('price_per_trip'),
                'facilities' => $this->request->getPost('facilities')
            ];

            // Handle image upload
            $image = $this->request->getFile('image');
            if ($image && $image->isValid() && !$image->hasMoved()) {
                // Delete old image if exists
                if ($boat['image_url'] && file_exists(ROOTPATH . 'public/' . $boat['image_url'])) {
                    unlink(ROOTPATH . 'public/' . $boat['image_url']);
                }
                
                $newName = $image->getRandomName();
                $image->move(ROOTPATH . 'public/assets/img/boats', $newName);
                $data['image_url'] = 'assets/img/boats/' . $newName;
            }

            if ($this->boatModel->save($data)) {
                return redirect()->to('/admin/boats')->with('success', 'Boat updated successfully');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to update boat');
            }
        }

        return view('admin/boats/edit', ['boat' => $boat]);
    }

    public function delete($id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/admin/login');
        }

        $boat = $this->boatModel->find($id);
        if (!$boat) {
            return redirect()->to('/admin/boats')->with('error', 'Boat not found');
        }

        // Delete image if exists
        if ($boat['image_url'] && file_exists(ROOTPATH . 'public/' . $boat['image_url'])) {
            unlink(ROOTPATH . 'public/' . $boat['image_url']);
        }

        if ($this->boatModel->delete($id)) {
            return redirect()->to('/admin/boats')->with('success', 'Boat deleted successfully');
        } else {
            return redirect()->to('/admin/boats')->with('error', 'Failed to delete boat');
        }
    }
}