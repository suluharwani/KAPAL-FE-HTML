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
    // Load necessary models
    $boatModel = new \App\Models\BoatModel();
    $bookingModel = new \App\Models\BookingModel();
    $contactModel = new \App\Models\ContactModel();
    $paymentModel = new \App\Models\PaymentModel();
    
    // Get today's date for filtering
    $today = date('Y-m-d');
    
    // Get counts for dashboard cards
    $data = [
        'title' => 'Dashboard',
        'user' => [
            'name' => $this->session->get('full_name'),
            'role' => $this->session->get('role')
        ],
        'total_boats' => $boatModel->countAll(),
        'today_bookings' => $bookingModel->where('DATE(created_at)', $today)->countAllResults(),
        'pending_payments' => $paymentModel->where('status', 'pending')->countAllResults(),
        'new_messages' => $contactModel->where('status', 'unread')->countAllResults(),
        'recent_bookings' => $bookingModel->select('bookings.*, users.full_name')
            ->join('users', 'users.user_id = bookings.user_id', 'left')
            ->orderBy('bookings.created_at', 'DESC')
            ->limit(5)
            ->findAll(),
        'recent_activities' => $this->getRecentActivities()
    ];
    
    return view('admin/dashboard', $data);
}

private function getRecentActivities()
{
    $activities = [];
    $bookingModel = new \App\Models\BookingModel();
    $paymentModel = new \App\Models\PaymentModel();
    
    // Get recent bookings (last 3)
    $recentBookings = $bookingModel->orderBy('created_at', 'DESC')->limit(3)->findAll();
    
    foreach ($recentBookings as $booking) {
        $activities[] = [
            'description' => 'New booking created: ' . $booking['booking_code'],
            'time_ago' => $this->timeAgo($booking['created_at']),
            'is_new' => strtotime($booking['created_at']) > strtotime('-1 hour'),
            'time_original' => $booking['created_at']
        ];
    }
    
    // Get recent payments (last 2)
    $recentPayments = $paymentModel->orderBy('created_at', 'DESC')->limit(2)->findAll();
    
    foreach ($recentPayments as $payment) {
        $activities[] = [
            'description' => 'Payment ' . $payment['status'] . ' for booking #' . $payment['booking_id'],
            'time_ago' => $this->timeAgo($payment['created_at']),
            'is_new' => strtotime($payment['created_at']) > strtotime('-1 hour'),
            'time_original' => $payment['created_at']
        ];
    }
    
    // Sort activities by time (newest first)
    usort($activities, function($a, $b) {
        return strtotime($b['time_original']) - strtotime($a['time_original']);
    });
    
    return array_slice($activities, 0, 5); // Return only top 5 activities
}

private function timeAgo($datetime)
{
    $time = strtotime($datetime);
    $timeDiff = time() - $time;
    
    if ($timeDiff < 60) {
        return $timeDiff . ' seconds ago';
    } elseif ($timeDiff < 3600) {
        $mins = round($timeDiff/60);
        return $mins . ' minute' . ($mins == 1 ? '' : 's') . ' ago';
    } elseif ($timeDiff < 86400) {
        $hours = round($timeDiff/3600);
        return $hours . ' hour' . ($hours == 1 ? '' : 's') . ' ago';
    } else {
        $days = round($timeDiff/86400);
        return $days . ' day' . ($days == 1 ? '' : 's') . ' ago';
    }
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
        'price_per_trip' => 'required|numeric',
        'image' => 'max_size[image,1024]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]'
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
        'facilities' => $this->request->getPost('facilities'),
        'is_featured' => $this->request->getPost('is_featured') ? 1 : 0
    ];

    // Handle image upload
    $imagePath = $this->handleImageUpload();
    if ($imagePath) {
        $data['image_url'] = $imagePath;
    }

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
        'price_per_trip' => 'required|numeric',
        'image' => 'max_size[image,1024]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]'
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
        'facilities' => $this->request->getPost('facilities'),
        'is_featured' => $this->request->getPost('is_featured') ? 1 : 0
    ];

    // Handle image upload
    $imagePath = $this->handleImageUpload();
    if ($imagePath) {
        // Delete old image if exists
        $oldBoat = $boatModel->find($id);
        if ($oldBoat['image_url'] && file_exists(ROOTPATH . 'public/' . $oldBoat['image_url'])) {
            unlink(ROOTPATH . 'public/' . $oldBoat['image_url']);
        }
        $data['image_url'] = $imagePath;
    }

    if ($boatModel->update($id, $data)) {
        return redirect()->to('/admin/boats')->with('success', 'Boat updated successfully');
    } else {
        return redirect()->back()->withInput()->with('error', 'Failed to update boat');
    }
}

public function deleteBoat($id)
{
    $boatModel = new \App\Models\BoatModel();
    $scheduleModel = new \App\Models\ScheduleModel();
    $bookingModel = new \App\Models\BookingModel();
    $paymentModel = new \App\Models\PaymentModel();
    $requestOpenTripModel = new \App\Models\RequestOpenTripModel();
    $openTripModel = new \App\Models\OpenTripModel();
    
    // Start transaction untuk menjaga konsistensi data
    $db = \Config\Database::connect();
    $db->transStart();
    
    try {
        // 1. Dapatkan data kapal terlebih dahulu
        $boat = $boatModel->find($id);
        if (!$boat) {
            return redirect()->to('/admin/boats')->with('error', 'Boat not found');
        }
        
        // 2. Dapatkan semua request open trip terkait kapal ini
        $openTripRequests = $requestOpenTripModel->where('boat_id', $id)->findAll();
        
        foreach ($openTripRequests as $request) {
            $requestId = $request['request_id'];
            
            // 3. Dapatkan semua open trip schedules terkait request ini
            $openTrips = $openTripModel->where('request_id', $requestId)->findAll();
            
            foreach ($openTrips as $openTrip) {
                $openTripId = $openTrip['open_trip_id'];
                
                // 4. Dapatkan semua bookings terkait open trip
                $openTripBookings = $bookingModel->where('open_trip_id', $openTripId)->findAll();
                
                foreach ($openTripBookings as $booking) {
                    $bookingId = $booking['booking_id'];
                    
                    // 5. Hapus payments terkait booking
                    $paymentModel->where('booking_id', $bookingId)->delete();
                    
                    // 6. Hapus passengers terkait booking
                    $db->table('passengers')->where('booking_id', $bookingId)->delete();
                    
                    // 7. Hapus booking
                    $bookingModel->delete($bookingId);
                }
                
                // 8. Hapus open trip
                $openTripModel->delete($openTripId);
            }
            
            // 9. Hapus request open trip
            $requestOpenTripModel->delete($requestId);
        }
        
        // 10. Dapatkan semua jadwal terkait kapal ini
        $schedules = $scheduleModel->where('boat_id', $id)->findAll();
        
        foreach ($schedules as $schedule) {
            $scheduleId = $schedule['schedule_id'];
            
            // 11. Dapatkan semua booking terkait jadwal ini
            $bookings = $bookingModel->where('schedule_id', $scheduleId)->findAll();
            
            foreach ($bookings as $booking) {
                $bookingId = $booking['booking_id'];
                
                // 12. Hapus payments terkait booking
                $paymentModel->where('booking_id', $bookingId)->delete();
                
                // 13. Hapus passengers terkait booking
                $db->table('passengers')->where('booking_id', $bookingId)->delete();
                
                // 14. Hapus booking
                $bookingModel->delete($bookingId);
            }
            
            // 15. Hapus jadwal
            $scheduleModel->delete($scheduleId);
        }
        
        // 16. Hapus gambar kapal jika ada
        if ($boat['image_url'] && file_exists(ROOTPATH . 'public/' . $boat['image_url'])) {
            unlink(ROOTPATH . 'public/' . $boat['image_url']);
        }
        
        // 17. Hapus kapal
        $boatModel->delete($id);
        
        $db->transComplete();
        
        if ($db->transStatus() === FALSE) {
            $db->transRollback();
            return redirect()->to('/admin/boats')->with('error', 'Failed to delete boat and related data');
        }
        
        return redirect()->to('/admin/boats')->with('success', 'Boat and all related data deleted successfully');
        
    } catch (\Exception $e) {
        $db->transRollback();
        log_message('error', 'Delete boat error: ' . $e->getMessage());
        return redirect()->to('/admin/boats')->with('error', 'Error: Cannot delete boat. It may have active bookings or schedules.');
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
private function handleImageUpload($fieldName = 'image')
{
    $image = $this->request->getFile($fieldName);
    
    if ($image && $image->isValid() && !$image->hasMoved()) {
        $newName = $image->getRandomName();
        $image->move(ROOTPATH . 'public/uploads/boats', $newName);
        return 'uploads/boats/' . $newName;
    }
    
    return null;
}
}