<?php namespace App\Controllers;

use App\Models\IslandModel;

class IslandController extends BaseController
{
    protected $islandModel;

    public function __construct()
    {
        $this->islandModel = new IslandModel();
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        $data = [
            'title' => 'Manage Islands',
            'islands' => $this->islandModel->getIslandsWithStats(),
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/islands/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Add New Island',
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/islands/create', $data);
    }

    public function store()
    {
        $rules = [
            'island_name' => 'required|max_length[100]|is_unique[islands.island_name]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle image upload
        $imageFile = $this->request->getFile('image');
        $imageUrl = null;

        if ($imageFile->isValid() && !$imageFile->hasMoved()) {
            $newName = $imageFile->getRandomName();
            $imageFile->move(ROOTPATH . 'public/uploads/islands', $newName);
            $imageUrl = 'uploads/islands/' . $newName;
        }

        $data = [
            'island_name' => $this->request->getPost('island_name'),
            'description' => $this->request->getPost('description'),
            'image_url' => $imageUrl
        ];

        if ($this->islandModel->insert($data)) {
            return redirect()->to('/admin/islands')->with('success', 'Island added successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to add island');
        }
    }

    public function edit($id)
    {
        $island = $this->islandModel->find($id);
        if (!$island) {
            return redirect()->to('/admin/islands')->with('error', 'Island not found');
        }

        $data = [
            'title' => 'Edit Island',
            'island' => $island,
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/islands/edit', $data);
    }

    public function update($id)
    {
        $rules = [
            'island_name' => "required|max_length[100]|is_unique[islands.island_name,island_id,{$id}]"
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $island = $this->islandModel->find($id);
        if (!$island) {
            return redirect()->to('/admin/islands')->with('error', 'Island not found');
        }

        // Handle image upload
        $imageFile = $this->request->getFile('image');
        $imageUrl = $island['image_url'];

        if ($imageFile->isValid() && !$imageFile->hasMoved()) {
            // Delete old image if exists
            if ($imageUrl && file_exists(ROOTPATH . 'public/' . $imageUrl)) {
                unlink(ROOTPATH . 'public/' . $imageUrl);
            }
            
            $newName = $imageFile->getRandomName();
            $imageFile->move(ROOTPATH . 'public/uploads/islands', $newName);
            $imageUrl = 'uploads/islands/' . $newName;
        }

        $data = [
            'island_name' => $this->request->getPost('island_name'),
            'description' => $this->request->getPost('description'),
            'image_url' => $imageUrl
        ];

        if ($this->islandModel->update($id, $data)) {
            return redirect()->to('/admin/islands')->with('success', 'Island updated successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update island');
        }
    }

    public function delete($id)
    {
        $island = $this->islandModel->find($id);
        if (!$island) {
            return redirect()->to('/admin/islands')->with('error', 'Island not found');
        }

        // Delete image if exists
        if ($island['image_url'] && file_exists(ROOTPATH . 'public/' . $island['image_url'])) {
            unlink(ROOTPATH . 'public/' . $island['image_url']);
        }

        if ($this->islandModel->delete($id)) {
            return redirect()->to('/admin/islands')->with('success', 'Island deleted successfully');
        } else {
            return redirect()->to('/admin/islands')->with('error', 'Failed to delete island');
        }
    }
}