<?php namespace App\Controllers;

use CodeIgniter\Controller;

class Admin extends BaseController
{
    protected $helpers = ['form', 'url'];
    
    public function __construct()
    {
        $this->session = \Config\Services::session();
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
    }

    public function dashboard()
    {
        $data = [
            'title' => 'Dashboard',
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/dashboard', $data);
    }

    // Contoh CRUD untuk Boats
    public function boats()
    {
        $boatModel = new \App\Models\BoatModel();
        $data = [
            'title' => 'Manage Boats',
            'boats' => $boatModel->findAll(),
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/boats/index', $data);
    }

    public function createBoat()
    {
        $data = [
            'title' => 'Add New Boat',
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/boats/create', $data);
    }

    public function storeBoat()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'boat_name' => 'required',
            'boat_type' => 'required|in_list[speedboat,traditional,luxury]',
            'capacity' => 'required|numeric',
            'price_per_trip' => 'required|numeric'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $boatModel = new \App\Models\BoatModel();
        $data = [
            'boat_name' => $this->request->getPost('boat_name'),
            'boat_type' => $this->request->getPost('boat_type'),
            'capacity' => $this->request->getPost('capacity'),
            'description' => $this->request->getPost('description'),
            'price_per_trip' => $this->request->getPost('price_per_trip'),
            'facilities' => $this->request->getPost('facilities')
        ];

        if ($boatModel->insert($data)) {
            return redirect()->to('/admin/boats')->with('success', 'Boat added successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to add boat');
        }
    }

    public function editBoat($id)
    {
        $boatModel = new \App\Models\BoatModel();
        $boat = $boatModel->find($id);
        
        if (!$boat) {
            return redirect()->to('/admin/boats')->with('error', 'Boat not found');
        }

        $data = [
            'title' => 'Edit Boat',
            'boat' => $boat,
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/boats/edit', $data);
    }

    public function updateBoat($id)
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'boat_name' => 'required',
            'boat_type' => 'required|in_list[speedboat,traditional,luxury]',
            'capacity' => 'required|numeric',
            'price_per_trip' => 'required|numeric'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $boatModel = new \App\Models\BoatModel();
        $data = [
            'boat_name' => $this->request->getPost('boat_name'),
            'boat_type' => $this->request->getPost('boat_type'),
            'capacity' => $this->request->getPost('capacity'),
            'description' => $this->request->getPost('description'),
            'price_per_trip' => $this->request->getPost('price_per_trip'),
            'facilities' => $this->request->getPost('facilities')
        ];

        if ($boatModel->update($id, $data)) {
            return redirect()->to('/admin/boats')->with('success', 'Boat updated successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update boat');
        }
    }

    public function deleteBoat($id)
    {
        $boatModel = new \App\Models\BoatModel();
        if ($boatModel->delete($id)) {
            return redirect()->to('/admin/boats')->with('success', 'Boat deleted successfully');
        } else {
            return redirect()->to('/admin/boats')->with('error', 'Failed to delete boat');
        }
    }
    // Blogs
public function blogs()
{
    $blogModel = new \App\Models\BlogModel();
    $data = [
        'title' => 'Manage Blogs',
        'blogs' => $blogModel->getBlogsWithCategory(),
        'user' => [
            'name' => $this->session->get('full_name'),
            'role' => $this->session->get('role')
        ]
    ];
    return view('admin/blogs/index', $data);
}
// Di Admin controller
public function users()
{
    $userModel = new \App\Models\UserModel();
    $role = $this->request->getGet('role');
    $search = $this->request->getGet('search');
    
    $data = [
        'title' => 'Manajemen User',
        'users' => $userModel->getAllUsers($role, $search),
        'user' => [
            'name' => session()->get('full_name'),
            'role' => session()->get('role')
        ]
    ];
    
    return view('admin/users/index', $data);
}
// Method create, store, edit, update, delete serupa dengan boats

    // Metode serupa untuk fitur lainnya (blogs, bookings, contacts, dll)
}