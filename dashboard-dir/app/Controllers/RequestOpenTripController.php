<?php namespace App\Controllers;

use App\Models\RequestOpenTripModel;
use App\Models\BoatModel;
use App\Models\RouteModel;

class RequestOpenTripController extends BaseController
{
    protected $requestOpenTripModel;
    protected $boatModel;
    protected $routeModel;

    public function __construct()
    {
        $this->requestOpenTripModel = new RequestOpenTripModel();
        $this->boatModel = new BoatModel();
        $this->routeModel = new RouteModel();
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        $status = $this->request->getGet('status');
        
        $data = [
            'title' => 'Open Trip Requests',
            'requests' => $this->requestOpenTripModel->getRequestsWithDetails($status),
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/request_open_trips/index', $data);
    }

    public function show($id)
    {
        $request = $this->requestOpenTripModel->getRequestDetails($id);
        if (!$request) {
            return redirect()->to('/admin/request-open-trips')->with('error', 'Request not found');
        }

        $data = [
            'title' => 'Open Trip Request Details',
            'request' => $request,
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/request_open_trips/show', $data);
    }

    public function updateStatus($id, $status)
    {
        $validStatuses = ['pending', 'approved', 'rejected'];
        if (!in_array($status, $validStatuses)) {
            return redirect()->back()->with('error', 'Invalid status');
        }

        if ($this->requestOpenTripModel->update($id, ['status' => $status])) {
            return redirect()->back()->with('success', 'Status updated successfully');
        } else {
            return redirect()->back()->with('error', 'Failed to update status');
        }
    }
}