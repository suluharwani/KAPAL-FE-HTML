<?php namespace App\Controllers\Api;

use App\Models\RequestOpenTripModel;
use App\Models\OpenTripScheduleModel;
use App\Models\ScheduleModel;
use App\Models\BookingModel;
use App\Models\BoatModel;
use App\Models\RouteModel;

class OpenTrips extends BaseApiController
{
    protected $requestModel;
    protected $openTripModel;
    protected $scheduleModel;
    protected $bookingModel;
    protected $boatModel;
    protected $routeModel;

    public function __construct()
    {
        $this->requestModel = new RequestOpenTripModel();
        $this->openTripModel = new OpenTripScheduleModel();
        $this->scheduleModel = new ScheduleModel();
        $this->bookingModel = new BookingModel();
        $this->boatModel = new BoatModel();
        $this->routeModel = new RouteModel();
    }

    // Customer membuat request open trip
    public function createRequest()
    {
        $rules = [
            'boat_id' => 'required|integer',
            'route_id' => 'required|integer',
            'proposed_date' => 'required|valid_date',
            'proposed_time' => 'required|valid_time',
            'min_passengers' => 'required|integer|greater_than[5]',
            'max_passengers' => 'required|integer|greater_than[min_passengers]',
            'notes' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        // Check boat and route availability
        $boat = $this->boatModel->find($this->request->getVar('boat_id'));
        $route = $this->routeModel->find($this->request->getVar('route_id'));

        if (!$boat || !$route) {
            return $this->fail('Boat or route not found', 404);
        }

        $data = [
            'user_id' => $this->request->user->user_id,
            'boat_id' => $this->request->getVar('boat_id'),
            'route_id' => $this->request->getVar('route_id'),
            'proposed_date' => $this->request->getVar('proposed_date'),
            'proposed_time' => $this->request->getVar('proposed_time'),
            'min_passengers' => $this->request->getVar('min_passengers'),
            'max_passengers' => $this->request->getVar('max_passengers'),
            'notes' => $this->request->getVar('notes'),
            'status' => 'pending'
        ];

        $requestId = $this->requestModel->insert($data);

        return $this->respondCreated([
            'request_id' => $requestId,
            'message' => 'Open trip request submitted. Waiting for admin approval.'
        ]);
    }

    // Admin approve/reject request
    public function approveRequest($requestId)
    {
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only admin can approve requests');
        }

        $request = $this->requestModel->find($requestId);
        if (!$request) {
            return $this->respondNotFound('Request not found');
        }

        $rules = [
            'status' => 'required|in_list[approved,rejected]',
            'admin_notes' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $status = $this->request->getVar('status');
        $this->requestModel->update($requestId, [
            'status' => $status,
            'admin_notes' => $this->request->getVar('admin_notes')
        ]);

        // Jika approved, buat jadwal open trip
        if ($status === 'approved') {
            $this->createOpenTripSchedule($request);
        }

        return $this->respondUpdated([
            'request_id' => $requestId,
            'status' => $status
        ]);
    }

    protected function createOpenTripSchedule($request)
    {
        // Buat regular schedule dulu
        $scheduleData = [
            'route_id' => $request['route_id'],
            'boat_id' => $request['boat_id'],
            'departure_date' => $request['proposed_date'],
            'departure_time' => $request['proposed_time'],
            'available_seats' => $request['max_passengers'],
            'is_open_trip' => true,
            'status' => 'available'
        ];

        $scheduleId = $this->scheduleModel->insert($scheduleData);

        // Buat open trip schedule
        $openTripData = [
            'request_id' => $request['request_id'],
            'schedule_id' => $scheduleId,
            'reserved_seats' => $request['min_passengers'],
            'available_seats' => $request['max_passengers'] - $request['min_passengers'],
            'status' => 'upcoming'
        ];

        $this->openTripModel->insert($openTripData);

        return $scheduleId;
    }

    // List open trip yang available
    public function listAvailable()
    {
        $openTrips = $this->openTripModel
            ->select('open_trip_schedules.*, schedules.departure_date, schedules.departure_time, boats.boat_name, routes.estimated_duration')
            ->join('schedules', 'schedules.schedule_id = open_trip_schedules.schedule_id')
            ->join('boats', 'boats.boat_id = schedules.boat_id')
            ->join('routes', 'routes.route_id = schedules.route_id')
            ->where('open_trip_schedules.status', 'upcoming')
            ->where('open_trip_schedules.available_seats >', 0)
            ->findAll();

        return $this->respond([
            'status' => 200,
            'data' => $openTrips
        ]);
    }

    // Customer yang request bisa booking reserved seat
    public function bookReservedSeat($openTripId)
    {
        $openTrip = $this->openTripModel->find($openTripId);
        if (!$openTrip) {
            return $this->respondNotFound('Open trip not found');
        }

        // Dapatkan request asli
        $request = $this->requestModel->find($openTrip['request_id']);
        if ($request['user_id'] !== $this->request->user->user_id) {
            return $this->failForbidden('Only the requester can book reserved seats');
        }

        $rules = [
            'passenger_count' => 'required|integer|less_than_equal_to['.$openTrip['reserved_seats'].']',
            'passengers' => 'required|array|min_length[1]',
            'passengers.*.full_name' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $passengerCount = (int) $this->request->getVar('passenger_count');

        // Buat booking
        $bookingData = [
            'booking_code' => $this->bookingModel->generateBookingCode(),
            'user_id' => $this->request->user->user_id,
            'schedule_id' => $openTrip['schedule_id'],
            'passenger_count' => $passengerCount,
            'total_price' => $this->calculateOpenTripPrice($openTrip['schedule_id'], $passengerCount),
            'booking_status' => 'confirmed',
            'payment_status' => 'pending',
            'is_open_trip' => true,
            'open_trip_type' => 'reserved'
        ];

        $bookingId = $this->bookingModel->insert($bookingData);

        // Tambahkan passengers
        foreach ($this->request->getVar('passengers') as $passenger) {
            $this->passengerModel->insert([
                'booking_id' => $bookingId,
                'full_name' => $passenger['full_name'],
                'identity_number' => $passenger['identity_number'] ?? null,
                'phone' => $passenger['phone'] ?? null,
                'age' => $passenger['age'] ?? null
            ]);
        }

        // Update reserved seats
        $this->openTripModel->update($openTripId, [
            'reserved_seats' => $openTrip['reserved_seats'] - $passengerCount
        ]);

        return $this->respondCreated([
            'booking_id' => $bookingId,
            'reserved_seats_remaining' => $openTrip['reserved_seats'] - $passengerCount
        ]);
    }

    // Customer lain bisa join open trip
    public function joinOpenTrip($openTripId)
    {
        $openTrip = $this->openTripModel->find($openTripId);
        if (!$openTrip || $openTrip['status'] !== 'upcoming') {
            return $this->respondNotFound('Open trip not available');
        }

        $rules = [
            'passenger_count' => 'required|integer|less_than_equal_to['.$openTrip['available_seats'].']',
            'passengers' => 'required|array|min_length[1]',
            'passengers.*.full_name' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $passengerCount = (int) $this->request->getVar('passenger_count');

        // Buat booking
        $bookingData = [
            'booking_code' => $this->bookingModel->generateBookingCode(),
            'user_id' => $this->request->user->user_id,
            'schedule_id' => $openTrip['schedule_id'],
            'passenger_count' => $passengerCount,
            'total_price' => $this->calculateOpenTripPrice($openTrip['schedule_id'], $passengerCount),
            'booking_status' => 'confirmed',
            'payment_status' => 'pending',
            'is_open_trip' => true,
            'open_trip_type' => 'public'
        ];

        $bookingId = $this->bookingModel->insert($bookingData);

        // Tambahkan passengers
        foreach ($this->request->getVar('passengers') as $passenger) {
            $this->passengerModel->insert([
                'booking_id' => $bookingId,
                'full_name' => $passenger['full_name'],
                'identity_number' => $passenger['identity_number'] ?? null,
                'phone' => $passenger['phone'] ?? null,
                'age' => $passenger['age'] ?? null
            ]);
        }

        // Update available seats
        $this->openTripModel->update($openTripId, [
            'available_seats' => $openTrip['available_seats'] - $passengerCount
        ]);

        return $this->respondCreated([
            'booking_id' => $bookingId,
            'available_seats_remaining' => $openTrip['available_seats'] - $passengerCount
        ]);
    }

    protected function calculateOpenTripPrice($scheduleId, $passengerCount)
    {
        $schedule = $this->scheduleModel->find($scheduleId);
        $boat = $this->boatModel->find($schedule['boat_id']);
        
        return $boat['price_per_trip'] * $passengerCount;
    }
}