<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in
if(!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit();
}

// Get parcel ID from URL
$id = isset($_GET['id']) ? $_GET['id'] : 0;

if($id > 0) {
    // Delete parcel items first
    $conn->query("DELETE FROM parcel_items WHERE parcel_id = $id");
    
    // Delete tracking records
    $conn->query("DELETE FROM parcel_tracking WHERE parcel_id = $id");
    
    // Delete the parcel
    if($conn->query("DELETE FROM parcels WHERE id = $id")) {
        $_SESSION['message'] = "Parcel deleted successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error deleting parcel: " . $conn->error;
        $_SESSION['message_type'] = "error";
    }
} else {
    $_SESSION['message'] = "Invalid parcel ID!";
    $_SESSION['message_type'] = "error";
}

// Redirect back to parcel list
header("Location: index.php?page=parcel_list");
exit();
?>