<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OpenTripModel;
use App\Models\ScheduleModel;
use App\Models\OpenTripScheduleModel;

class OpenTrip extends BaseController
{
    protected $openTripModel;
    protected $scheduleModel;
    protected $openTripScheduleModel;

    public function __construct()
    {
        $this->openTripModel = new OpenTripModel();
        $this->scheduleModel = new ScheduleModel();
        $this->openTripScheduleModel = new OpenTripScheduleModel();
    }

    public function index()
    {
        $data = [
            'pendingRequests' => $this->openTripModel->getOpenTripsWithDetails('pending'),
            'approvedRequests' => $this->openTripModel->getOpenTripsWithDetails('approved'),
            'rejectedRequests' => $this->openTripModel->getOpenTripsWithDetails('rejected')
        ];

        return view('admin/open_trip/index', $data);
    }

    public function approve($requestId)
    {
        $request = $this->openTripModel->find($requestId);
        if (!$request) {
            return redirect()->back()->with('error', 'Request not found');
        }

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'admin_notes' => 'permit_empty|max_length[500]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            // Create schedule
            $scheduleData = [
                'route_id' => $request['route_id'],
                'boat_id' => $request['boat_id'],
                'departure_date' => $request['proposed_date'],
                'departure_time' => $request['proposed_time'],
                'available_seats' => $request['max_passengers'],
                'status' => 'available',
                'is_open_trip' => 1
            ];

            if ($scheduleId = $this->scheduleModel->insert($scheduleData)) {
                // Create open trip schedule
                $openTripData = [
                    'request_id' => $requestId,
                    'schedule_id' => $scheduleId,
                    'reserved_seats' => $request['min_passengers'],
                    'available_seats' => $request['max_passengers'] - $request['min_passengers'],
                    'status' => 'upcoming'
                ];

                if ($this->openTripScheduleModel->insert($openTripData)) {
                    // Approve the request
                    $this->openTripModel->approveRequest($requestId, $this->request->getPost('admin_notes'));
                    
                    return redirect()->to('/admin/open-trip')->with('success', 'Request approved and schedule created');
                } else {
                    // Rollback schedule creation if open trip schedule fails
                    $this->scheduleModel->delete($scheduleId);
                    return redirect()->back()->with('error', 'Failed to create open trip schedule');
                }
            } else {
                return redirect()->back()->with('error', 'Failed to create schedule');
            }
        }

        return view('admin/open_trip/approve', ['request' => $request]);
    }

    public function reject($requestId)
    {
        $request = $this->openTripModel->find($requestId);
        if (!$request) {
            return redirect()->back()->with('error', 'Request not found');
        }

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'admin_notes' => 'required|max_length[500]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            if ($this->openTripModel->rejectRequest($requestId, $this->request->getPost('admin_notes'))) {
                return redirect()->to('/admin/open-trip')->with('success', 'Request rejected');
            } else {
                return redirect()->back()->with('error', 'Failed to reject request');
            }
        }

        return view('admin/open_trip/reject', ['request' => $request]);
    }

    public function viewSchedule($openTripId)
    {
        $openTrip = $this->openTripScheduleModel->getOpenTripWithDetails($openTripId);
        if (!$openTrip) {
            return redirect()->back()->with('error', 'Open trip not found');
        }

        $data = [
            'openTrip' => $openTrip,
            'bookings' => $this->openTripScheduleModel->getOpenTripBookings($openTripId)
        ];

        return view('admin/open_trip/view_schedule', $data);
    }
}