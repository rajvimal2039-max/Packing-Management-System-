<?php
// Check if session is already started before starting
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'db_connect.php';

// Check if user is logged in and is admin
if(!isset($_SESSION['login_id']) || $_SESSION['login_type'] != 1) {
    header("Location: index.php");
    exit();
}

// Get date range from URL or set defaults
$from_date = isset($_GET['from']) ? $_GET['from'] : date('Y-m-d', strtotime('-30 days'));
$to_date = isset($_GET['to']) ? $_GET['to'] : date('Y-m-d');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reports - Packing System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .card-header { background: #007bff; color: white; }
        .summary-card { border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .rupee-symbol { font-size: 1.2em; font-weight: bold; }
        @media print {
            .no-print { display: none; }
            .card { border: 1px solid #ddd; }
        }
    </style>
</head>
<body>
<div class="container-fluid mt-3">
    <div class="card">
        <div class="card-header">
            <h4><i class="fa fa-chart-bar mr-2"></i>Parcel Reports (₹ Indian Rupees)</h4>
        </div>
        <div class="card-body">
            
            <!-- Filter Form -->
            <form method="GET" action="" class="form-inline justify-content-center mb-4 no-print">
                <input type="hidden" name="page" value="reports">
                
                <div class="form-group mx-2">
                    <label class="mr-2 font-weight-bold">From:</label>
                    <input type="date" name="from" class="form-control" value="<?php echo $from_date; ?>" required>
                </div>
                
                <div class="form-group mx-2">
                    <label class="mr-2 font-weight-bold">To:</label>
                    <input type="date" name="to" class="form-control" value="<?php echo $to_date; ?>" required>
                </div>
                
                <button type="submit" class="btn btn-primary mx-2">
                    <i class="fa fa-search"></i> Generate Report
                </button>
                
                <button type="button" class="btn btn-success mx-2" onclick="window.print()">
                    <i class="fa fa-print"></i> Print
                </button>
            </form>
            
            <?php
            // Get report data
            $where = "WHERE date_created BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59'";
            $qry = $conn->query("SELECT * FROM parcels $where ORDER BY date_created DESC");
            
            $total_parcels = $qry->num_rows;
            $total_amount = 0;
            $parcels = [];
            
            while($row = $qry->fetch_assoc()) {
                $total_amount += $row['total'];
                $parcels[] = $row;
            }
            ?>
            
            <!-- Summary Cards with ₹ symbol -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card summary-card bg-primary text-white">
                        <div class="card-body text-center">
                            <h1 class="display-4"><?php echo $total_parcels; ?></h1>
                            <p class="lead">Total Parcels</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card summary-card bg-success text-white">
                        <div class="card-body text-center">
                            <h1 class="display-4"><span class="rupee-symbol">₹</span> <?php echo number_format($total_amount, 2); ?></h1>
                            <p class="lead">Total Amount</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card summary-card bg-info text-white">
                        <div class="card-body text-center">
                            <h1 class="display-4"><?php echo $from_date; ?></h1>
                            <p class="lead">to <?php echo $to_date; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Average Card -->
            <div class="row mb-4">
                <div class="col-md-6 offset-md-3">
                    <div class="card summary-card bg-warning">
                        <div class="card-body text-center">
                            <h3>Average per Parcel</h3>
                            <h2 class="display-4"><span class="rupee-symbol">₹</span> <?php echo $total_parcels > 0 ? number_format($total_amount / $total_parcels, 2) : '0.00'; ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Report Table with ₹ symbol -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Reference</th>
                            <th>Sender</th>
                            <th>Recipient</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th class="text-right">Amount (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($parcels)): 
                            $i = 1;
                            foreach($parcels as $row):
                        ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo date('Y-m-d', strtotime($row['date_created'])); ?></td>
                            <td><strong><?php echo $row['reference_number']; ?></strong></td>
                            <td><?php echo $row['sender_name']; ?></td>
                            <td><?php echo $row['recipient_name']; ?></td>
                            <td>
                                <?php if($row['type'] == 1): ?>
                                    <span class="badge badge-primary">Deliver</span>
                                <?php else: ?>
                                    <span class="badge badge-info">Pickup</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $status_labels = ['Accepted', 'Collected', 'Shipped', 'In-Transit', 'Arrived', 
                                                  'Out for Delivery', 'Ready', 'Delivered', 'Picked-up', 'Failed'];
                                $status_colors = ['secondary', 'info', 'primary', 'primary', 'info', 
                                                  'warning', 'primary', 'success', 'success', 'danger'];
                                $status = $row['status'];
                                ?>
                                <span class="badge badge-<?php echo $status_colors[$status] ?? 'secondary'; ?>">
                                    <?php echo $status_labels[$status] ?? 'Unknown'; ?>
                                </span>
                            </td>
                            <td class="text-right font-weight-bold text-primary">
                                <span class="rupee-symbol">₹</span> <?php echo number_format($row['total'], 2); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="fa fa-box-open fa-3x mb-3"></i>
                                <h5>No parcels found in this date range</h5>
                                <p>Try selecting a different date range</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="bg-light font-weight-bold">
                        <tr>
                            <td colspan="7" class="text-right">TOTAL:</td>
                            <td class="text-right text-primary">
                                <span class="rupee-symbol">₹</span> <?php echo number_format($total_amount, 2); ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <!-- Print Info -->
            <div class="row mt-3 no-print">
                <div class="col-12 text-center text-muted">
                    <small>Report generated on <?php echo date('Y-m-d H:i:s'); ?></small>
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- Simple print styles -->
<style>
@media print {
    .no-print { display: none !important; }
    body { background: white; }
    .card { border: none !important; box-shadow: none !important; }
    .badge { border: 1px solid #000; color: #000 !important; background: transparent !important; }
    .table { border: 1px solid #000; }
    th { background: #f0f0f0 !important; }
    .rupee-symbol { font-family: Arial, sans-serif; }
}
</style>

</body>
</html>