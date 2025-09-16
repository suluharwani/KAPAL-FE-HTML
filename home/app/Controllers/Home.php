<?php namespace App\Controllers;
use App\Models\FeatureModel;
use App\Models\IslandModel;
use App\Models\PopularRouteModel;
use App\Models\SliderModel;
use App\Models\TestimonialModel;
use App\Models\ScheduleModel; 
use App\Models\RouteModel; 
class Home extends BaseController
{
public function index()
{
    // Load models
    $sliderModel = new SliderModel();
    $islandModel = new IslandModel();
    $featureModel = new FeatureModel();
    $testimonialModel = new TestimonialModel();
    $scheduleModel = new ScheduleModel(); 
    $routeModel = new RouteModel();

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
        'openTripSchedules' => $scheduleModel->getOpenTripSchedulesWithDetails(),
        'adminUrl' => $_ENV['adminUrl'] // Tambahkan ini untuk mengirim adminUrl ke view
    ];
    
    $this->render('home', $data);
}
    public function searchSchedules()
    {
        $scheduleModel = new ScheduleModel();
        
        $routeId = $this->request->getGet('route');
        $date = $this->request->getGet('date');
        $tripType = $this->request->getGet('trip_type'); // 'regular' or 'open_trip'
        
        if ($tripType === 'open_trip') {
            $schedules = $scheduleModel->getOpenTripSchedulesWithDetails($routeId, $date);
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
    if (!isset($_SESSION['user_id'])) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Anda harus login terlebih dahulu'
        ]);
    }
    
    $scheduleId = $this->request->getPost('schedule_id');
    $passengerData = [
        'name' => $this->request->getPost('name'),
        'identity' => $this->request->getPost('identity'),
        'phone' => $this->request->getPost('phone'),
        'age' => $this->request->getPost('age')
    ];
    
    // Cek apakah jadwal masih tersedia
    $scheduleModel = new \App\Models\ScheduleModel();
    $schedule = $scheduleModel->find($scheduleId);
    
    if (!$schedule || $schedule['available_seats'] <= 0) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Maaf, kuota untuk open trip ini sudah penuh'
        ]);
    }
    
    // Buat booking untuk open trip
    $bookingModel = new \App\Models\BookingModel();
    $bookingData = [
        'user_id' => $_SESSION['user_id'],
        'schedule_id' => $scheduleId,
        'passenger_count' => 1,
        'total_price' => 0, // Akan dihitung kemudian
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
        $passengerAdded = $passengerModel->addPassengers($bookingId, [$passengerData]);
        
        if ($passengerAdded) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Permintaan kursi berhasil dikirim. Menunggu konfirmasi.'
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

// Method untuk konfirmasi penumpang (admin only)
public function confirmPassenger()
{
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