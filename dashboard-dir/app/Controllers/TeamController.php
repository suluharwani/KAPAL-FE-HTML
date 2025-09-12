<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TeamModel;

class TeamController extends BaseController
{
    protected $teamModel;
    protected $session;

    public function __construct()
    {
        $this->teamModel = new TeamModel();
        $this->session = \Config\Services::session();
        helper(['upload', 'text']);
    }

    public function index()
    {
        $data = [
            'title' => 'Team Management',
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ],
            'teams' => $this->teamModel->orderBy('display_order', 'ASC')->findAll()
        ];

        return view('admin/teams/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Add Team Member',
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ],
        ];

        return view('admin/teams/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => 'required|max_length[255]',
            'position' => 'required|max_length[255]',
            'image' => 'uploaded[image]|max_size[image,2048]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]',
            'bio' => 'required|min_length[10]'
        ], [
            'image' => [
                'uploaded' => 'Please select an image to upload',
                'max_size' => 'Image size should not exceed 2MB',
                'is_image' => 'Please upload a valid image file',
                'mime_in' => 'Only JPG, JPEG and PNG images are allowed'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $image = $this->request->getFile('image');
        $imageName = null;

        if ($image->isValid() && !$image->hasMoved()) {
            // Pastikan folder uploads/team ada
            $uploadPath = ROOTPATH . 'public/uploads/team';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Generate unique filename
            $imageName = 'team_' . time() . '_' . random_string('alnum', 8) . '.' . $image->getExtension();
            
            try {
                if ($image->move($uploadPath, $imageName)) {
                    $imageName = 'uploads/team/' . $imageName;
                    
                    // Resize image jika perlu
                    $this->resizeImage($uploadPath . '/' . $imageName, 500, 500);
                } else {
                    throw new \Exception('Failed to move uploaded file');
                }
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->with('error', 'Failed to upload image: ' . $e->getMessage());
            }
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'position' => $this->request->getPost('position'),
            'image' => $imageName,
            'bio' => $this->request->getPost('bio'),
            'social_facebook' => $this->request->getPost('social_facebook'),
            'social_twitter' => $this->request->getPost('social_twitter'),
            'social_instagram' => $this->request->getPost('social_instagram'),
            'social_linkedin' => $this->request->getPost('social_linkedin'),
            'display_order' => $this->request->getPost('display_order') ?? 0,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->teamModel->insert($data)) {
            return redirect()->to('/admin/teams')->with('success', 'Team member added successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to add team member');
    }

    public function edit($id)
    {
        $team = $this->teamModel->find($id);
        
        if (!$team) {
            return redirect()->to('/admin/teams')->with('error', 'Team member not found');
        }

        $data = [
            'title' => 'Edit Team Member',
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ],
            'team' => $team
        ];

        return view('admin/teams/edit', $data);
    }

    public function update($id)
    {
        $team = $this->teamModel->find($id);
        
        if (!$team) {
            return redirect()->to('/admin/teams')->with('error', 'Team member not found');
        }

        $validationRules = [
            'name' => 'required|max_length[255]',
            'position' => 'required|max_length[255]',
            'bio' => 'required|min_length[10]'
        ];

        // Hanya validasi image jika diupload
        if ($this->request->getFile('image')->isValid()) {
            $validationRules['image'] = 'max_size[image,2048]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]';
        }

        $validation = \Config\Services::validation();
        $validation->setRules($validationRules, [
            'image' => [
                'max_size' => 'Image size should not exceed 2MB',
                'is_image' => 'Please upload a valid image file',
                'mime_in' => 'Only JPG, JPEG and PNG images are allowed'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $image = $this->request->getFile('image');
        $imageName = $team['image'];

        if ($image->isValid() && !$image->hasMoved()) {
            // Pastikan folder uploads/team ada
            $uploadPath = ROOTPATH . 'public/uploads/team';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Hapus gambar lama jika ada
            if ($imageName && file_exists(ROOTPATH . 'public/' . $imageName)) {
                unlink(ROOTPATH . 'public/' . $imageName);
            }
            
            try {
                // Generate unique filename
                $newImageName = 'team_' . time() . '_' . random_string('alnum', 8) . '.' . $image->getExtension();
                
                if ($image->move($uploadPath, $newImageName)) {
                    $imageName = 'uploads/team/' . $newImageName;
                    
                    // Resize image jika perlu
                    $this->resizeImage($uploadPath . '/' . $newImageName, 500, 500);
                } else {
                    throw new \Exception('Failed to move uploaded file');
                }
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->with('error', 'Failed to upload image: ' . $e->getMessage());
            }
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'position' => $this->request->getPost('position'),
            'image' => $imageName,
            'bio' => $this->request->getPost('bio'),
            'social_facebook' => $this->request->getPost('social_facebook'),
            'social_twitter' => $this->request->getPost('social_twitter'),
            'social_instagram' => $this->request->getPost('social_instagram'),
            'social_linkedin' => $this->request->getPost('social_linkedin'),
            'display_order' => $this->request->getPost('display_order') ?? 0,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->teamModel->update($id, $data)) {
            return redirect()->to('/admin/teams')->with('success', 'Team member updated successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update team member');
    }

    public function delete($id)
    {
        $team = $this->teamModel->find($id);
        
        if (!$team) {
            return redirect()->to('/admin/teams')->with('error', 'Team member not found');
        }

        // Hapus gambar jika ada
        if ($team['image'] && file_exists(ROOTPATH . 'public/' . $team['image'])) {
            unlink(ROOTPATH . 'public/' . $team['image']);
        }

        if ($this->teamModel->delete($id)) {
            return redirect()->to('/admin/teams')->with('success', 'Team member deleted successfully');
        }

        return redirect()->to('/admin/teams')->with('error', 'Failed to delete team member');
    }

    public function updateStatus($id)
    {
        $team = $this->teamModel->find($id);
        
        if (!$team) {
            return $this->response->setJSON(['success' => false, 'message' => 'Team member not found']);
        }

        $newStatus = $team['is_active'] ? 0 : 1;
        
        if ($this->teamModel->update($id, ['is_active' => $newStatus])) {
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Status updated successfully',
                'newStatus' => $newStatus
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update status']);
    }

    public function updateOrder()
    {
        $order = $this->request->getPost('order');
        
        if (!is_array($order)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid order data']);
        }

        foreach ($order as $position => $id) {
            $this->teamModel->update($id, ['display_order' => $position]);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Order updated successfully']);
    }

    /**
     * Helper function to resize images
     */
    private function resizeImage($imagePath, $width, $height)
    {
        try {
            $image = \Config\Services::image()
                ->withFile($imagePath)
                ->fit($width, $height, 'center')
                ->save($imagePath);
        } catch (\Exception $e) {
            // Log error but don't stop the process
            log_message('error', 'Failed to resize image: ' . $e->getMessage());
        }
    }

    /**
     * Bulk actions
     */
    public function bulkAction()
    {
        $action = $this->request->getPost('action');
        $ids = $this->request->getPost('ids');

        if (empty($ids)) {
            return redirect()->back()->with('error', 'No team members selected');
        }

        switch ($action) {
            case 'activate':
                $this->teamModel->whereIn('team_id', $ids)->set(['is_active' => 1])->update();
                $message = 'Selected team members activated';
                break;
                
            case 'deactivate':
                $this->teamModel->whereIn('team_id', $ids)->set(['is_active' => 0])->update();
                $message = 'Selected team members deactivated';
                break;
                
            case 'delete':
                // Delete images first
                $teams = $this->teamModel->whereIn('team_id', $ids)->findAll();
                foreach ($teams as $team) {
                    if ($team['image'] && file_exists(ROOTPATH . 'public/' . $team['image'])) {
                        unlink(ROOTPATH . 'public/' . $team['image']);
                    }
                }
                $this->teamModel->whereIn('team_id', $ids)->delete();
                $message = 'Selected team members deleted';
                break;
                
            default:
                return redirect()->back()->with('error', 'Invalid action');
        }

        return redirect()->to('/admin/teams')->with('success', $message);
    }
}