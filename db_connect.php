<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "packing_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// ============================================
// FUNCTIONS - Check if already declared
// ============================================

if (!function_exists('getStatusLabel')) {
    function getStatusLabel($status) {
        $labels = [
            0 => 'Accepted',
            1 => 'Collected', 
            2 => 'Shipped',
            3 => 'In-Transit',
            4 => 'Arrived',
            5 => 'Out for Delivery',
            6 => 'Ready to Pickup',
            7 => 'Delivered',
            8 => 'Picked-up',
            9 => 'Failed'
        ];
        return $labels[$status] ?? 'Unknown';
    }
}

if (!function_exists('getStatusBadge')) {
    function getStatusBadge($status) {
        $badges = [
            0 => 'secondary',
            1 => 'info',
            2 => 'primary',
            3 => 'primary',
            4 => 'info',
            5 => 'warning',
            6 => 'primary',
            7 => 'success',
            8 => 'success',
            9 => 'danger'
        ];
        return $badges[$status] ?? 'secondary';
    }
}

if (!function_exists('generateReferenceNumber')) {
    function generateReferenceNumber($conn) {
        $prefix = 'CMP' . date('Ymd');
        $result = $conn->query("SELECT reference_number FROM parcels WHERE reference_number LIKE '$prefix%' ORDER BY id DESC LIMIT 1");
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $last = substr($row['reference_number'], -4);
            $num = intval($last) + 1;
            $seq = str_pad($num, 4, '0', STR_PAD_LEFT);
        } else {
            $seq = '0001';
        }
        
        return $prefix . $seq;
    }
}
?>