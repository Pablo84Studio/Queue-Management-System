<?php
// PDF Report Generator Class
// This uses TCPDF library - install with: composer require tecnickcom/tcpdf

require_once(__DIR__ . '/../vendor/autoload.php');

class ReportGenerator {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    // Generate PDF report for queue data
    public function generateQueueReport($start_date, $end_date, $period_type = 'day', $output_format = 'I') {
        try {
            // Fetch queue data
            $queue_data = $this->getQueueData($start_date, $end_date, $period_type);
            $statistics = $this->getReportStatistics($start_date, $end_date);
            
            // Create PDF object
            $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_PAGE_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
            $pdf->SetMargins(15, 15, 15);
            $pdf->SetAutoPageBreak(TRUE, 15);
            
            // Add page
            $pdf->AddPage();
            
            // Set font
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->Cell(0, 10, 'Queue Management System - Report', 0, 1, 'C');
            
            // Report period
            $pdf->SetFont('helvetica', '', 12);
            $pdf->Cell(0, 8, 'Period: ' . date('Y-m-d', strtotime($start_date)) . ' to ' . date('Y-m-d', strtotime($end_date)), 0, 1, 'C');
            $pdf->Cell(0, 8, 'Report Type: ' . ucfirst($period_type), 0, 1, 'C');
            $pdf->Ln(5);
            
            // Summary Statistics
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Cell(50, 8, 'Total Customers:', 1, 0, 'L', TRUE);
            $pdf->SetFont('helvetica', '', 12);
            $pdf->Cell(30, 8, $statistics['total_customers'] ?? 0, 1, 1);
            
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(50, 8, 'Completed:', 1, 0, 'L', TRUE);
            $pdf->SetFont('helvetica', '', 12);
            $pdf->Cell(30, 8, $statistics['completed_customers'] ?? 0, 1, 1);
            
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(50, 8, 'Cancelled:', 1, 0, 'L', TRUE);
            $pdf->SetFont('helvetica', '', 12);
            $pdf->Cell(30, 8, $statistics['cancelled_customers'] ?? 0, 1, 1);
            
            $pdf->Ln(5);
            
            // Data Table
            $pdf->SetFont('helvetica', 'B', 11);
            $pdf->SetFillColor(100, 100, 100);
            $pdf->SetTextColor(255, 255, 255);
            
            $pdf->Cell(15, 7, 'Ticket', 1, 0, 'C', TRUE);
            $pdf->Cell(40, 7, 'Customer', 1, 0, 'L', TRUE);
            $pdf->Cell(35, 7, 'Service', 1, 0, 'L', TRUE);
            $pdf->Cell(25, 7, 'Status', 1, 0, 'C', TRUE);
            $pdf->Cell(40, 7, 'Date/Time', 1, 1, 'C', TRUE);
            
            // Data rows
            $pdf->SetFont('helvetica', '', 10);
            $pdf->SetTextColor(0, 0, 0);
            $row_count = 0;
            
            foreach ($queue_data as $row) {
                $bg_color = ($row_count % 2 == 0) ? [240, 240, 240] : [255, 255, 255];
                $pdf->SetFillColor($bg_color[0], $bg_color[1], $bg_color[2]);
                
                $pdf->Cell(15, 7, $row['ticket_number'] ?? '', 1, 0, 'C', TRUE);
                $pdf->Cell(40, 7, substr($row['customer_name'] ?? '', 0, 15), 1, 0, 'L', TRUE);
                $pdf->Cell(35, 7, substr($row['service_name'] ?? '', 0, 12), 1, 0, 'L', TRUE);
                $pdf->Cell(25, 7, $row['status'] ?? '', 1, 0, 'C', TRUE);
                $pdf->Cell(40, 7, date('m-d H:i', strtotime($row['created_at'] ?? now())), 1, 1, 'C', TRUE);
                
                $row_count++;
            }
            
            // Output PDF
            $filename = 'queue_report_' . $start_date . '_to_' . $end_date . '.pdf';
            $pdf->Output($filename, $output_format);
            
            return [
                'success' => true,
                'message' => 'Report generated successfully',
                'filename' => $filename
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Generate staff performance report
    public function generateStaffReport($start_date, $end_date) {
        try {
            $staff_stats = $this->getStaffStats($start_date, $end_date);
            
            $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_PAGE_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
            $pdf->SetMargins(15, 15, 15);
            $pdf->SetAutoPageBreak(TRUE, 15);
            
            $pdf->AddPage();
            
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->Cell(0, 10, 'Staff Performance Report', 0, 1, 'C');
            
            $pdf->SetFont('helvetica', '', 12);
            $pdf->Cell(0, 8, 'Period: ' . date('Y-m-d', strtotime($start_date)) . ' to ' . date('Y-m-d', strtotime($end_date)), 0, 1, 'C');
            $pdf->Ln(5);
            
            $pdf->SetFont('helvetica', 'B', 11);
            $pdf->SetFillColor(100, 100, 100);
            $pdf->SetTextColor(255, 255, 255);
            
            $pdf->Cell(40, 7, 'Staff Name', 1, 0, 'L', TRUE);
            $pdf->Cell(30, 7, 'Customers Called', 1, 0, 'C', TRUE);
            $pdf->Cell(35, 7, 'Services Completed', 1, 0, 'C', TRUE);
            $pdf->Cell(35, 7, 'Avg Service Time', 1, 1, 'C', TRUE);
            
            $pdf->SetFont('helvetica', '', 10);
            $pdf->SetTextColor(0, 0, 0);
            $row_count = 0;
            
            foreach ($staff_stats as $staff) {
                $bg_color = ($row_count % 2 == 0) ? [240, 240, 240] : [255, 255, 255];
                $pdf->SetFillColor($bg_color[0], $bg_color[1], $bg_color[2]);
                
                $pdf->Cell(40, 7, $staff['name'] ?? '', 1, 0, 'L', TRUE);
                $pdf->Cell(30, 7, $staff['customers_called'] ?? 0, 1, 0, 'C', TRUE);
                $pdf->Cell(35, 7, $staff['services_completed'] ?? 0, 1, 0, 'C', TRUE);
                $pdf->Cell(35, 7, ($staff['avg_service_time'] ?? 0) . ' min', 1, 1, 'C', TRUE);
                
                $row_count++;
            }
            
            $filename = 'staff_report_' . $start_date . '_to_' . $end_date . '.pdf';
            $pdf->Output($filename, 'I');
            
            return ['success' => true];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Get queue data for reporting
    private function getQueueData($start_date, $end_date, $period_type) {
        $query = "SELECT q.*, s.name as service_name 
                  FROM queue q
                  JOIN services s ON q.service_id = s.id
                  WHERE DATE(q.created_at) BETWEEN ? AND ?
                  ORDER BY q.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $start_date, $end_date);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    // Get report statistics
    private function getReportStatistics($start_date, $end_date) {
        $query = "SELECT 
                    COUNT(*) as total_customers,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_customers,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_customers
                  FROM queue
                  WHERE DATE(created_at) BETWEEN ? AND ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $start_date, $end_date);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_assoc();
    }
    
    // Get staff statistics
    private function getStaffStats($start_date, $end_date) {
        $query = "SELECT 
                    u.name,
                    COUNT(DISTINCT CASE WHEN sa.activity_type = 'called_customer' THEN sa.id END) as customers_called,
                    COUNT(DISTINCT CASE WHEN sa.activity_type = 'completed_service' THEN sa.id END) as services_completed,
                    AVG(TIMESTAMPDIFF(MINUTE, q.called_at, q.completed_at)) as avg_service_time
                  FROM users u
                  LEFT JOIN staff_activity sa ON u.id = sa.staff_id AND DATE(sa.timestamp) BETWEEN ? AND ?
                  LEFT JOIN queue q ON sa.queue_id = q.id
                  WHERE u.role = 'staff'
                  GROUP BY u.id
                  ORDER BY services_completed DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $start_date, $end_date);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
