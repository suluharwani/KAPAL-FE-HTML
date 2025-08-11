<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ReportModel;

class Reports extends BaseController
{
    protected $reportModel;

    public function __construct()
    {
        $this->reportModel = new ReportModel();
    }

    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/admin/login');
        }

        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');

        $data = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'bookingReport' => $this->reportModel->getBookingReport($startDate, $endDate),
            'paymentReport' => $this->reportModel->getPaymentReport($startDate, $endDate),
            'revenueReport' => $this->reportModel->getRevenueReport($startDate, $endDate)
        ];

        return view('admin/reports/index', $data);
    }

    public function export($type)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/admin/login');
        }

        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');

        switch ($type) {
            case 'bookings':
                $data = $this->reportModel->getBookingReport($startDate, $endDate);
                $filename = 'bookings_report_' . date('Ymd') . '.csv';
                return $this->exportToCSV($data, $filename);
                break;
            case 'payments':
                $data = $this->reportModel->getPaymentReport($startDate, $endDate);
                $filename = 'payments_report_' . date('Ymd') . '.csv';
                return $this->exportToCSV($data, $filename);
                break;
            case 'revenue':
                $data = $this->reportModel->getRevenueReport($startDate, $endDate);
                $filename = 'revenue_report_' . date('Ymd') . '.csv';
                return $this->exportToCSV($data, $filename);
                break;
            default:
                return redirect()->back()->with('error', 'Invalid report type');
        }
    }

    protected function exportToCSV($data, $filename)
    {
        if (empty($data)) {
            return redirect()->back()->with('error', 'No data to export');
        }

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        
        // Write headers
        fputcsv($output, array_keys($data[0]));
        
        // Write data
        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }
    public function exportExcel()
{
    $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
    $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
    $type = $this->request->getGet('type') ?? 'bookings';

    switch ($type) {
        case 'bookings':
            $data = $this->reportModel->getBookingReport($startDate, $endDate);
            $filename = 'bookings_report_' . date('Ymd');
            $headers = ['Booking Code', 'Customer', 'Departure', 'Arrival', 'Date', 'Time', 'Passengers', 'Total Price', 'Status'];
            $fields = ['booking_code', 'customer_name', 'departure_island', 'arrival_island', 'departure_date', 'departure_time', 'passenger_count', 'total_price', 'booking_status'];
            break;
            
        case 'payments':
            $data = $this->reportModel->getPaymentReport($startDate, $endDate);
            $filename = 'payments_report_' . date('Ymd');
            $headers = ['Payment ID', 'Booking Code', 'Customer', 'Amount', 'Method', 'Status', 'Date'];
            $fields = ['payment_id', 'booking_code', 'customer_name', 'amount', 'payment_method', 'status', 'payment_date'];
            break;
            
        case 'revenue':
            $data = $this->reportModel->getRevenueReport($startDate, $endDate);
            $filename = 'revenue_report_' . date('Ymd');
            $headers = ['Date', 'Total Revenue', 'Payment Count'];
            $fields = ['payment_date', 'total_revenue', 'payment_count'];
            break;
            
        default:
            return redirect()->back()->with('error', 'Invalid report type');
    }

    // Load PHPExcel library
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set headers
    $sheet->fromArray([$headers], NULL, 'A1');

    // Add data
    $row = 2;
    foreach ($data as $item) {
        $rowData = [];
        foreach ($fields as $field) {
            if ($field === 'departure_date' || $field === 'payment_date') {
                $rowData[] = date('d M Y', strtotime($item[$field]));
            } elseif ($field === 'departure_time') {
                $rowData[] = date('H:i', strtotime($item[$field]));
            } elseif ($field === 'total_price' || $field === 'amount' || $field === 'total_revenue') {
                $rowData[] = number_format($item[$field], 0, ',', '.');
            } else {
                $rowData[] = $item[$field];
            }
        }
        $sheet->fromArray([$rowData], NULL, 'A' . $row);
        $row++;
    }

    // Auto size columns
        foreach (range('A', $sheet->getHighestDataColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}