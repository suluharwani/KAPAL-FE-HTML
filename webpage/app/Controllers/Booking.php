<?php

namespace App\Controllers;

use App\Libraries\ApiService;

class Booking extends BaseController
{
    protected $apiService;

    public function __construct()
    {
        $this->apiService = new ApiService();
        $this->apiService->setToken(session()->get('token'));
    }

    public function index()
    {
        try {
            $boats = $this->apiService->getAllBoats();
            $bookings = $this->apiService->getAllBookings();

            return view('booking/index', [
                'boats' => $boats['data'] ?? [],
                'bookings' => $bookings['data'] ?? [],
            ]);
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal mengambil data: ' . $e->getMessage());
            return redirect()->to('/');
        }
    }

    public function create()
    {
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'schedule_id' => 'required|numeric',
                'passenger_count' => 'required|numeric|greater_than[0]',
                'payment_method' => 'required|in_list[transfer,cash]',
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('validation', $this->validator);
            }

            $passengers = [];
            // Proses data penumpang jika ada
            for ($i = 1; $i <= $this->request->getPost('passenger_count'); $i++) {
                $passengers[] = [
                    'full_name' => $this->request->getPost("passenger_name_$i"),
                    'identity_number' => $this->request->getPost("passenger_id_$i"),
                    'phone' => $this->request->getPost("passenger_phone_$i"),
                    'age' => $this->request->getPost("passenger_age_$i"),
                ];
            }

            $bookingData = [
                'schedule_id' => $this->request->getPost('schedule_id'),
                'passenger_count' => $this->request->getPost('passenger_count'),
                'passengers' => $passengers,
                'payment_method' => $this->request->getPost('payment_method'),
                'notes' => $this->request->getPost('notes'),
            ];

            try {
                $response = $this->apiService->createBooking($bookingData);
                
                if (isset($response['success'])) {
                    return redirect()->to('/booking/confirmation/' . $response['booking_id']);
                } else {
                    session()->setFlashdata('error', $response['message'] ?? 'Pemesanan gagal. Silakan coba lagi.');
                }
            } catch (\Exception $e) {
                session()->setFlashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }

            return redirect()->to('/booking');
        }

        return redirect()->to('/booking');
    }

    public function confirmation($bookingId)
    {
        try {
            $bookings = $this->apiService->getAllBookings();
            $booking = null;

            foreach ($bookings['data'] ?? [] as $item) {
                if ($item['id'] == $bookingId) {
                    $booking = $item;
                    break;
                }
            }

            if (!$booking) {
                throw new \Exception('Booking tidak ditemukan');
            }

            return view('booking/confirmation', ['booking' => $booking]);
        } catch (\Exception $e) {
            session()->setFlashdata('error', $e->getMessage());
            return redirect()->to('/booking');
        }
    }
}