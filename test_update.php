<?php
require_once 'db_connect.php';

echo "<h2>🔧 Update Status Test</h2>";

// First, check if tables exist and are correct
echo "<h3>📋 Database Check:</h3>";

// Check parcels table
$parcels_check = $conn->query("SHOW TABLES LIKE 'parcels'");
if($parcels_check->num_rows > 0) {
    echo "✅ parcels table exists<br>";
    
    // Check for orphaned tracking records
    $orphans = $conn->query("SELECT COUNT(*) as c FROM parcel_tracking WHERE parcel_id NOT IN (SELECT id FROM parcels)");
    $orphan_count = $orphans->fetch_assoc()['c'];
    if($orphan_count > 0) {
        echo "⚠️ Found $orphan_count orphaned tracking records - fixing...<br>";
        $conn->query("DELETE FROM parcel_tracking WHERE parcel_id NOT IN (SELECT id FROM parcels)");
        echo "✅ Orphaned records deleted<br>";
    }
} else {
    echo "❌ parcels table missing!<br>";
}

// Get all parcels to show IDs
$parcels = $conn->query("SELECT p.*, COUNT(pt.id) as tracking_count 
                         FROM parcels p 
                         LEFT JOIN parcel_tracking pt ON p.id = pt.parcel_id 
                         GROUP BY p.id 
                         ORDER BY p.id DESC");

if($parcels->num_rows > 0) {
    echo "<h3>📦 Available Parcels:</h3>";
    echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
    echo "<tr style='background:#f0f0f0;'><th>ID</th><th>Reference</th><th>Current Status</th><th>Tracking Records</th><th>Action</th></tr>";
    
    while($row = $parcels->fetch_assoc()) {
        $status_names = ['Accepted','Collected','Shipped','In-Transit','Arrived','Out for Delivery','Ready','Delivered','Picked-up','Failed'];
        $current_status = $status_names[$row['status']] ?? 'Unknown';
        
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td><strong>" . $row['reference_number'] . "</strong></td>";
        echo "<td>" . $current_status . " (" . $row['status'] . ")</td>";
        echo "<td>" . $row['tracking_count'] . " records</td>";
        echo "<td>";
        echo "<a href='?action=update&id=" . $row['id'] . "&status=7' style='margin-right:10px;' onclick='return confirm(\"Mark as Delivered?\")'>✅ Delivered</a>";
        echo "<a href='?action=update&id=" . $row['id'] . "&status=3' style='margin-right:10px;' onclick='return confirm(\"Mark as In-Transit?\")'>🚚 In-Transit</a>";
        echo "<a href='?action=delete_tracking&id=" . $row['id'] . "' style='color:red;' onclick='return confirm(\"Delete ALL tracking for this parcel?\")'>🗑️ Clear Tracking</a>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:red'>❌ No parcels found in database!</p>";
}

// Handle update action
if(isset($_GET['action'])) {
    if($_GET['action'] == 'update') {
        $id = $_GET['id'];
        $status = $_GET['status'];
        
        echo "<hr><h3>🔄 Updating Parcel ID: $id to Status: $status</h3>";
        
        // First check if parcel exists
        $check = $conn->query("SELECT * FROM parcels WHERE id = $id");
        if($check->num_rows == 0) {
            echo "<p style='color:red'>❌ Parcel ID $id does not exist!</p>";
        } else {
            // Update parcel
            $update = $conn->query("UPDATE parcels SET status = $status WHERE id = $id");
            
            if($update) {
                echo "<p style='color:green'>✅ Parcel status updated successfully!</p>";
                
                // Add to tracking
                $track = $conn->query("INSERT INTO parcel_tracking (parcel_id, status, date_created) VALUES ($id, $status, NOW())");
                if($track) {
                    echo "<p style='color:green'>✅ Tracking record added!</p>";
                } else {
                    echo "<p style='color:red'>❌ Error adding tracking: " . $conn->error . "</p>";
                }
            } else {
                echo "<p style='color:red'>❌ Error updating parcel: " . $conn->error . "</p>";
            }
        }
    }
    
    if($_GET['action'] == 'delete_tracking') {
        $id = $_GET['id'];
        $conn->query("DELETE FROM parcel_tracking WHERE parcel_id = $id");
        echo "<p style='color:green'>✅ All tracking records deleted for parcel $id</p>";
        echo "<script>setTimeout(() => window.location.href = 'test_update.php', 2000);</script>";
    }
}

// Show tracking records
echo "<hr><h3>📊 Recent Tracking Records:</h3>";
$tracking = $conn->query("SELECT pt.*, p.reference_number 
                          FROM parcel_tracking pt 
                          JOIN parcels p ON pt.parcel_id = p.id 
                          ORDER BY pt.date_created DESC LIMIT 20");
if($tracking->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Parcel</th><th>Status</th><th>Date</th></tr>";
    while($row = $tracking->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['reference_number'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "<td>" . $row['date_created'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No tracking records found</p>";
}

// Test ajax connection
echo "<hr><h3>🔄 Test AJAX Connection</h3>";
echo "<button onclick='testAjax()'>Test AJAX</button>";
echo "<div id='ajaxResult' style='margin-top:10px; padding:10px; background:#f0f0f0;'></div>";

$ajax_exists = file_exists('ajax.php') ? '✅ ajax.php exists' : '❌ ajax.php NOT found';
echo "<p>$ajax_exists</p>";
?>

<script>
function testAjax() {
    document.getElementById('ajaxResult').innerHTML = 'Testing...';
    
    fetch('ajax.php?action=test')
        .then(response => response.text())
        .then(data => {
            document.getElementById('ajaxResult').innerHTML = '✅ Response: ' + data;
        })
        .catch(error => {
            document.getElementById('ajaxResult').innerHTML = '❌ Error: ' + error;
        });
}
</script>