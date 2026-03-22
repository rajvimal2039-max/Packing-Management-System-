<?php
session_start();
require_once 'db_connect.php';

// Force login for testing (remove after fixing)
$_SESSION['login_id'] = 1;
$_SESSION['login_type'] = 1;

// Simple test query
$test = $conn->query("SELECT * FROM parcels LIMIT 5");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Report Test</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3>🔍 Report Debug Page</h3>
            </div>
            <div class="card-body">
                
                <!-- Test 1: Database Connection -->
                <div class="alert alert-info">
                    <strong>Test 1: Database Connection</strong><br>
                    <?php
                    if($conn) {
                        echo "✅ Database connected: " . $conn->query("SELECT DATABASE()")->fetch_row()[0];
                    } else {
                        echo "❌ Database connection failed";
                    }
                    ?>
                </div>
                
                <!-- Test 2: Check if user is admin -->
                <div class="alert alert-info">
                    <strong>Test 2: User Status</strong><br>
                    <?php
                    if($_SESSION['login_type'] == 1) {
                        echo "✅ You are logged in as ADMIN";
                    } else {
                        echo "❌ You are not admin. Current type: " . $_SESSION['login_type'];
                    }
                    ?>
                </div>
                
                <!-- Test 3: Check parcels table -->
                <div class="alert alert-info">
                    <strong>Test 3: Parcels Table</strong><br>
                    <?php
                    $check = $conn->query("SHOW TABLES LIKE 'parcels'");
                    if($check->num_rows > 0) {
                        $count = $conn->query("SELECT COUNT(*) as c FROM parcels")->fetch_assoc()['c'];
                        echo "✅ parcels table exists with $count records";
                    } else {
                        echo "❌ parcels table does NOT exist";
                    }
                    ?>
                </div>
                
                <!-- Test 4: Show sample data -->
                <div class="alert alert-info">
                    <strong>Test 4: Sample Parcel Data</strong><br>
                    <?php if($test->num_rows > 0): ?>
                        <table class="table table-bordered mt-2">
                            <tr>
                                <th>ID</th>
                                <th>Reference</th>
                                <th>Sender</th>
                                <th>Date</th>
                            </tr>
                            <?php while($row = $test->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['reference_number']; ?></td>
                                <td><?php echo $row['sender_name']; ?></td>
                                <td><?php echo $row['date_created']; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </table>
                    <?php else: ?>
                        <p class="text-danger">No parcels found in database!</p>
                    <?php endif; ?>
                </div>
                
                <!-- Simple Report Form -->
                <hr>
                <h4>Simple Report Test</h4>
                <form method="GET" action="" class="form-inline mb-3">
                    <div class="form-group mx-2">
                        <label>From:</label>
                        <input type="date" name="from" class="form-control ml-2" value="<?php echo date('Y-m-d', strtotime('-30 days')); ?>">
                    </div>
                    <div class="form-group mx-2">
                        <label>To:</label>
                        <input type="date" name="to" class="form-control ml-2" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Generate</button>
                </form>
                
                <?php
                if(isset($_GET['from']) && isset($_GET['to'])) {
                    $from = $_GET['from'] . ' 00:00:00';
                    $to = $_GET['to'] . ' 23:59:59';
                    
                    $qry = $conn->query("SELECT * FROM parcels WHERE date_created BETWEEN '$from' AND '$to'");
                    
                    echo "<div class='alert alert-success'>Found " . $qry->num_rows . " parcels between " . $_GET['from'] . " and " . $_GET['to'] . "</div>";
                    
                    if($qry->num_rows > 0) {
                        echo "<table class='table table-bordered'>";
                        echo "<tr><th>Ref</th><th>Sender</th><th>Recipient</th><th>Date</th></tr>";
                        while($row = $qry->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['reference_number'] . "</td>";
                            echo "<td>" . $row['sender_name'] . "</td>";
                            echo "<td>" . $row['recipient_name'] . "</td>";
                            echo "<td>" . date('Y-m-d', strtotime($row['date_created'])) . "</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                    }
                }
                ?>
                
            </div>
        </div>
    </div>
</body>
</html>