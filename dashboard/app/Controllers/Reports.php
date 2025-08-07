<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Services;

class Reports extends BaseController
{
    protected $apiUrl;
    protected $session;

    public function __construct()
    {
        $this->apiUrl = getenv('API_BASE_URL') . '/api/reports';
        $this->session = session();
        
        // Hanya admin yang bisa mengakses laporan
        if ($this->session->get('role') !== 'admin') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    // Laporan Booking
    public function bookings()
    {
        $client = \Config\Services::curlrequest();
        
        try {
            // Default date range (current month)
            $dateFrom = $this->request->getGet('date_from') ?? date('Y-m-01');
            $dateTo = $this->request->getGet('date_to') ?? date('Y-m-t');
            
            $response = $client->get($this->apiUrl . '/bookings', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ],
                'query' => [
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                    'status' => $this->request->getGet('status'),
                    'boat_id' => $this->request->getGet('boat_id'),
                    'route_id' => $this->request->getGet('route_id')
                ]
            ]);

            $result = json_decode($response->getBody(), true);
            
            // Get additional data for filters
            $boatsResponse = $client->get(getenv('API_BASE_URL') . '/api/boats', [
                'headers' => ['Authorization' => 'Bearer ' . $this->session->get('token')]
            ]);
            $boats = json_decode($boatsResponse->getBody(), true)['data'] ?? [];
            
            $routesResponse = $client->get(getenv('API_BASE_URL') . '/api/routes', [
                'headers' => ['Authorization' => 'Bearer ' . $this->session->get('token')]
            ]);
            $routes = json_decode($routesResponse->getBody(), true)['data'] ?? [];

            return view('reports/bookings', [
                'title' => 'Laporan Pemesanan',
                'bookings' => $result['data'] ?? [],
                'total' => $result['meta']['total'] ?? 0,
                'total_revenue' => $result['meta']['total_revenue'] ?? 0,
                'boats' => $boats,
                'routes' => $routes,
                'filters' => [
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                    'status' => $this->request->getGet('status'),
                    'boat_id' => $this->request->getGet('boat_id'),
                    'route_id' => $this->request->getGet('route_id')
                ]
            ]);

        } catch (\Exception $e) {
            return $this->handleError('Gagal memuat laporan: ' . $e->getMessage());
        }
    }

    // Laporan Pendapatan
    public function revenue()
    {
        $client = \Config\Services::curlrequest();
        
        try {
            // Default date range (last 30 days)
            $dateFrom = $this->request->getGet('date_from') ?? date('Y-m-d', strtotime('-30 days'));
            $dateTo = $this->request->getGet('date_to') ?? date('Y-m-d');
            $groupBy = $this->request->getGet('group_by') ?? 'day';
            
            $response = $client->get($this->apiUrl . '/revenue', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ],
                'query' => [
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                    'group_by' => $groupBy
                ]
            ]);

            $result = json_decode($response->getBody(), true);

            return view('reports/revenue', [
                'title' => 'Laporan Pendapatan',
                'data' => $result['data'] ?? [],
                'total' => $result['meta']['total'] ?? 0,
                'filters' => [
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                    'group_by' => $groupBy
                ]
            ]);

        } catch (\Exception $e) {
            return $this->handleError('Gagal memuat laporan pendapatan: ' . $e->getMessage());
        }
    }

    // Laporan Kapal
    public function boats()
    {
        $client = \Config\Services::curlrequest();
        
        try {
            $dateFrom = $this->request->getGet('date_from') ?? date('Y-m-01');
            $dateTo = $this->request->getGet('date_to') ?? date('Y-m-t');
            
            $response = $client->get($this->apiUrl . '/boats', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ],
                'query' => [
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo
                ]
            ]);

            $result = json_decode($response->getBody(), true);

            return view('reports/boats', [
                'title' => 'Laporan Utilisasi Kapal',
                'boats' => $result['data'] ?? [],
                'filters' => [
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo
                ]
            ]);

        } catch (\Exception $e) {
            return $this->handleError('Gagal memuat laporan kapal: ' . $e->getMessage());
        }
    }

    // Export to Excel
    public function export($type)
    {
        $client = \Config\Services::curlrequest();
        
        try {
            $queryParams = $this->request->getGet();
            
            $response = $client->get($this->apiUrl . '/' . $type, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ],
                'query' => $queryParams
            ]);

            $result = json_decode($response->getBody(), true);
            
            // Load PhpSpreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set headers based on report type
            switch ($type) {
                case 'bookings':
                    $sheet->setCellValue('A1', 'Laporan Pemesanan');
                    $sheet->setCellValue('A2', 'Periode: ' . ($queryParams['date_from'] ?? '-') . ' s/d ' . ($queryParams['date_to'] ?? '-'));
                    
                    $headers = ['ID', 'Kode Booking', 'Tanggal', 'Kapal', 'Rute', 'Penumpang', 'Total', 'Status'];
                    $dataKey = 'bookings';
                    break;
                    
                case 'revenue':
                    $sheet->setCellValue('A1', 'Laporan Pendapatan');
                    $sheet->setCellValue('A2', 'Periode: ' . ($queryParams['date_from'] ?? '-') . ' s/d ' . ($queryParams['date_to'] ?? '-'));
                    
                    $headers = ['Tanggal', 'Jumlah Booking', 'Total Pendapatan'];
                    $dataKey = 'data';
                    break;
                    
                case 'boats':
                    $sheet->setCellValue('A1', 'Laporan Utilisasi Kapal');
                    $sheet->setCellValue('A2', 'Periode: ' . ($queryParams['date_from'] ?? '-') . ' s/d ' . ($queryParams['date_to'] ?? '-'));
                    
                    $headers = ['Nama Kapal', 'Jumlah Trip', 'Penumpang', 'Pendapatan', 'Utilisasi'];
                    $dataKey = 'boats';
                    break;
                    
                default:
                    throw new \Exception('Jenis laporan tidak valid');
            }
            
            // Write headers
            $sheet->fromArray($headers, null, 'A4');
            
            // Format and write data
            $data = [];
            foreach ($result[$dataKey] as $item) {
                switch ($type) {
                    case 'bookings':
                        $data[] = [
                            $item['id'],
                            $item['booking_code'],
                            date('d M Y', strtotime($item['created_at'])),
                            $item['boat_name'],
                            $item['route_name'],
                            $item['passenger_count'],
                            'Rp ' . number_format($item['total_amount'], 0, ',', '.'),
                            ucfirst($item['status'])
                        ];
                        break;
                        
                    case 'revenue':
                        $data[] = [
                            $item['date'],
                            $item['booking_count'],
                            'Rp ' . number_format($item['total_revenue'], 0, ',', '.')
                        ];
                        break;
                        
                    case 'boats':
                        $data[] = [
                            $item['boat_name'],
                            $item['trip_count'],
                            $item['passenger_count'],
                            'Rp ' . number_format($item['total_revenue'], 0, ',', '.'),
                            $item['utilization'] . '%'
                        ];
                        break;
                }
            }
            
            $sheet->fromArray($data, null, 'A5');
            
            // Auto size columns
            foreach (range('A', $sheet->getHighestColumn()) as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Style header
            $headerStyle = [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D9E1F2']
                ]
            ];
            $sheet->getStyle('A4:' . $sheet->getHighestColumn() . '4')->applyFromArray($headerStyle);
            
            // Set title style
            $sheet->getStyle('A1:A2')->getFont()->setBold(true);
            
            // Save file
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="laporan_' . $type . '_' . date('YmdHis') . '.xlsx"');
            header('Cache-Control: max-age=0');
            
            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor laporan: ' . $e->getMessage());
        }
    }

    // Helper untuk handle error
    private function handleError($message)
    {
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $message
            ]);
        }
        
        return redirect()->to('/reports/bookings')->with('error', $message);
    }
}