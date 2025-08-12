<?php namespace App\Controllers;

use App\Models\BoatModel;
use App\Models\IslandModel;
use App\Models\RouteModel;
use App\Models\ScheduleModel;

class Boats extends BaseController
{
    public function index()
    {
        $model = new BoatModel();
        $islandModel = new IslandModel();
        
        $data = [
            'title' => 'Pesan Kapal - Raja Ampat Boat Services',
            'boats' => $model->findAll(),
            'islands' => $islandModel->findAll()
        ];
        
        $this->render('boats/index', $data);
    }

    public function schedule()
    {
        $routeModel = new RouteModel();
        $scheduleModel = new ScheduleModel();
        $boatModel = new BoatModel();
        $islandModel = new IslandModel();
        
        $data = [
            'title' => 'Jadwal Kapal - Raja Ampat Boat Services',
            'routes' => $routeModel->findAll(),
            'schedules' => $scheduleModel->getSchedulesWithDetails(),
            'boats' => $boatModel->findAll(),
            'islands' => $islandModel->findAll()
        ];
        
        $this->render('boats/schedule', $data);
    }

    public function checkAvailability()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'from_island' => 'required',
            'to_island' => 'required',
            'departure_date' => 'required|valid_date',
            'passengers' => 'required|numeric'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setStatusCode(400)->setJSON(['errors' => $validation->getErrors()]);
        }

        $scheduleModel = new ScheduleModel();
        $fromIsland = $this->request->getPost('from_island');
        $toIsland = $this->request->getPost('to_island');
        $departureDate = $this->request->getPost('departure_date');
        $passengers = $this->request->getPost('passengers');

        $schedules = $scheduleModel->getAvailableSchedules($fromIsland, $toIsland, $departureDate, $passengers);

        return $this->response->setJSON([
            'success' => true,
            'data' => $schedules
        ]);
    }

    public function book()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        if (!$this->session->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'schedule_id' => 'required|numeric',
            'passengers' => 'required|numeric|greater_than[0]',
            'passenger_names' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setStatusCode(400)->setJSON(['errors' => $validation->getErrors()]);
        }

        // Here you would typically save the booking to database
        // For demo purposes, we'll just return a success message
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Pemesanan berhasil. Silakan lakukan pembayaran.',
            'data' => [
                'booking_code' => 'BOOK-' . strtoupper(uniqid()),
                'schedule_id' => $this->request->getPost('schedule_id'),
                'passengers' => $this->request->getPost('passengers'),
                'total_price' => 3000000 // Example price
            ]
        ]);
    }
}