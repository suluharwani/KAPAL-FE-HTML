<?php namespace App\Controllers;
use App\Models\FeatureModel;
use App\Models\IslandModel;
use App\Models\PopularRouteModel;
use App\Models\SliderModel;
use App\Models\TestimonialModel;
use App\Models\ScheduleModel; 
use App\Models\RouteModel; 
use AllowDynamicProperties;
use App\Controllers\WarehouseController;
class Home extends BaseController
{
    protected $userValidation;
    protected $session;
    protected $db;
    protected $uri;
    protected $form_validation;
    
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->db = \Config\Database::connect();
        $this->session = session();
        $this->uri = service('uri');
        helper('form');
        $this->form_validation = \Config\Services::validation();
    }
public function index()
{
    // Load models
    $sliderModel = new SliderModel();
    $islandModel = new IslandModel();
    $featureModel = new FeatureModel();
    $testimonialModel = new TestimonialModel();
    $scheduleModel = new ScheduleModel(); 
    $routeModel = new RouteModel();
    $openTripModel = new \App\Models\OpenTripSchedulesModel(); // Tambahkan model OpenTrip

    // Get data from database
    $data = [
        'title' => 'Pemesanan Kapal Raja Ampat',
        'active' => 'home',
        'sliders' => $sliderModel->where('is_active', 1)->findAll(),
        'islands' => $islandModel->findAll(),
        'features' => $featureModel->where('is_active', 1)->findAll(),
        'popularRoutes' => $routeModel->getPopularRoutes(6),
        'testimonials' => $testimonialModel->where('status', 'approved')->orderBy('created_at', 'DESC')->findAll(3),
        'regularRoutes' => $scheduleModel->getAvailableRegularRoutes(),
        'openTripRoutes' => $scheduleModel->getAvailableOpenTripRoutes(),
        'regularSchedules' => $scheduleModel->getRegularSchedulesWithDetails(),
        'openTripSchedules' => $openTripModel->getUpcomingOpenTrips(), // Gunakan method yang sama seperti di boats/open-trip
        'adminUrl' => $_ENV['adminUrl']
    ];
    
    $this->render('home', $data);
}
 // Home.php - Ubah method searchSchedules()
public function searchSchedules()
{
    $openTripModel = new \App\Models\OpenTripSchedulesModel(); // Gunakan model OpenTrip
    $scheduleModel = new ScheduleModel();
    
    $routeId = $this->request->getGet('route');
    $date = $this->request->getGet('date');
    $tripType = $this->request->getGet('trip_type'); // 'regular' or 'open_trip'
    
    if ($tripType === 'open_trip') {
        // Gunakan method yang sama dengan boats/open-trip
        $schedules = $openTripModel->getUpcomingOpenTrips();
        
        // Filter berdasarkan route dan date jika ada
        if (!empty($routeId)) {
            $schedules = array_filter($schedules, function($schedule) use ($routeId) {
                return $schedule['route_id'] == $routeId;
            });
        }
        
        if (!empty($date)) {
            $schedules = array_filter($schedules, function($schedule) use ($date) {
                return $schedule['departure_date'] == $date;
            });
        }
        
        // Re-index array
        $schedules = array_values($schedules);
    } else {
        $schedules = $scheduleModel->getRegularSchedulesWithDetails($routeId, $date);
    }
    
    return $this->response->setJSON($schedules);
}


    public function about()
    {
        $data = [
            'title' => 'Tentang Kami - Raja Ampat Boat Services',
            'active' => 'about'
        ];
        
        $this->render('about', $data);
    }

    public function blog()
    {
        $data = [
            'title' => 'Blog - Raja Ampat Boat Services',
            'active' => 'blog'
        ];
        
        $this->render('blog', $data);
    }

    public function blogSingle($slug)
    {
        $data = [
            'title' => 'Blog Post - Raja Ampat Boat Services',
            'active' => 'blog',
            'post' => [
                'title' => '5 Spot Snorkeling Terbaik di Raja Ampat',
                'content' => '...' // Your blog content here
            ]
        ];
        
        $this->render('blog_single', $data);
    }

    public function gallery()
    {
        $data = [
            'title' => 'Galeri - Raja Ampat Boat Services',
            'active' => 'gallery'
        ];
        
        $this->render('gallery', $data);
    }

    public function contact()
    {
        $data = [
            'title' => 'Kontak - Raja Ampat Boat Services',
            'active' => 'contact'
        ];
        
        $this->render('contact', $data);
    }

    public function faq()
    {
        $data = [
            'title' => 'FAQ - Raja Ampat Boat Services',
            'active' => 'faq'
        ];
        
        $this->render('faq', $data);
    }
public function requestOpenTripSeat()
{
    // Only allow AJAX requests
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(405)->setJSON([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
    }

    if (!isset($_SESSION['userData'])) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Anda harus login terlebih dahulu'
        ]);
    }
    
    $scheduleId = $this->request->getPost('schedule_id');
    $passengerData = [
        'full_name' => $this->request->getPost('name'),
        'identity_number' => $this->request->getPost('identity'),
        'phone' => $this->request->getPost('phone'),
        'age' => $this->request->getPost('age')
    ];
    
    // Validasi input
    if (empty($passengerData['full_name']) || empty($passengerData['phone'])) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Nama dan nomor telepon wajib diisi'
        ]);
    }
    
    // Cek apakah jadwal masih tersedia
    $scheduleModel = new \App\Models\ScheduleModel();
    $schedule = $scheduleModel->find($scheduleId);
    
    if (!$schedule) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Jadwal tidak ditemukan'
        ]);
    }
    
    if ($schedule['available_seats'] <= 0) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Maaf, kuota untuk open trip ini sudah penuh'
        ]);
    }
    
    // Hitung harga per orang untuk open trip
    $pricePerPerson = 0;
    if (isset($schedule['price_per_person']) && $schedule['price_per_person'] > 0) {
        $pricePerPerson = $schedule['price_per_person'];
    } elseif (isset($schedule['agreed_price']) && $schedule['agreed_price'] > 0 && isset($schedule['capacity']) && $schedule['capacity'] > 0) {
        // Hitung harga per orang berdasarkan harga trip dan kapasitas
        $pricePerPerson = ceil($schedule['agreed_price'] / $schedule['capacity']);
    } elseif (isset($schedule['price']) && $schedule['price'] > 0 && isset($schedule['capacity']) && $schedule['capacity'] > 0) {
        // Fallback: hitung berdasarkan harga regular dan kapasitas
        $pricePerPerson = ceil($schedule['price'] / $schedule['capacity']);
    }
    
    // Generate booking code
    $bookingCode = 'BOOK-' . strtoupper(uniqid());
    
    // Buat booking untuk open trip
    $bookingModel = new \App\Models\BookingModel();
    $bookingData = [
        'user_id' => $_SESSION['userData']['user_id'],
        'schedule_id' => $scheduleId,
        'passenger_count' => 1,
        'total_price' => $pricePerPerson, // Harga untuk 1 penumpang
        'booking_code' => $bookingCode, // Tambahkan booking code
        'booking_status' => 'pending',
        'is_open_trip' => 1,
        'open_trip_type' => 'public',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    $bookingId = $bookingModel->insert($bookingData);
    
    if ($bookingId) {
        // Tambahkan penumpang
        $passengerModel = new \App\Models\PassengerModel();
        $passengerData['booking_id'] = $bookingId;
        $passengerAdded = $passengerModel->insert($passengerData);
        
        if ($passengerAdded) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Permintaan kursi berhasil dikirim. Menunggu konfirmasi.',
                'booking_code' => $bookingCode,
                'price' => $pricePerPerson
            ]);
        } else {
            // Hapus booking jika gagal menambah penumpang
            $bookingModel->delete($bookingId);
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menambahkan data penumpang'
            ]);
        }
    }
    
    return $this->response->setJSON([
        'success' => false,
        'message' => 'Gagal membuat permintaan booking'
    ]);
}

 public function confirmPassenger()
    {
        // Only allow AJAX requests
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
        }

        $this->access('admin'); // Hanya admin yang bisa mengonfirmasi
        
        $passengerId = $this->request->getPost('passenger_id');
        $scheduleId = $this->request->getPost('schedule_id');
        
        $passengerModel = new \App\Models\PassengerModel();
        $confirmed = $passengerModel->confirmPassenger($passengerId, $scheduleId);
        
        if ($confirmed) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Penumpang berhasil dikonfirmasi dan kuota berkurang'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal mengonfirmasi penumpang'
        ]);
    }
  public function cancelPassengerConfirmation()
    {
        // Only allow AJAX requests
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
        }

        $this->access('admin'); // Hanya admin yang bisa membatalkan konfirmasi
        
        $passengerId = $this->request->getPost('passenger_id');
        $scheduleId = $this->request->getPost('schedule_id');
        
        $passengerModel = new \App\Models\PassengerModel();
        $canceled = $passengerModel->cancelPassengerConfirmation($passengerId, $scheduleId);
        
        if ($canceled) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Konfirmasi penumpang dibatalkan dan kuota dikembalikan'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal membatalkan konfirmasi penumpang'
        ]);
    }
}