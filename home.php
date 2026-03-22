<?php
require_once 'db_connect.php';

// Get statistics for charts
$status_counts = [];
$status_labels = ['Accepted','Collected','Shipped','In-Transit','Arrived','Out for Delivery','Ready','Delivered','Picked-up','Failed'];
for($i=0; $i<=9; $i++) {
    $result = $conn->query("SELECT COUNT(*) as c FROM parcels WHERE status = $i");
    $status_counts[$i] = $result->fetch_assoc()['c'];
}

// Monthly data for last 6 months
$monthly_data = [];
$monthly_labels = [];
for($i=5; $i>=0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $monthly_labels[] = date('M Y', strtotime("-$i months"));
    $result = $conn->query("SELECT COUNT(*) as c FROM parcels WHERE DATE_FORMAT(date_created, '%Y-%m') = '$month'");
    $monthly_data[] = $result->fetch_assoc()['c'];
}
?>
<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="row">
    <?php if($_SESSION['login_type'] == 1): ?>
    <div class="col-md-3">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?php echo $conn->query("SELECT COUNT(*) as c FROM branches")->fetch_assoc()['c']; ?></h3>
                <p>Branches</p>
            </div>
            <div class="icon"><i class="fa fa-building"></i></div>
            <a href="?page=branches" class="small-box-footer">More info</a>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="col-md-3">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?php echo $conn->query("SELECT COUNT(*) as c FROM parcels")->fetch_assoc()['c']; ?></h3>
                <p>Parcels</p>
            </div>
            <div class="icon"><i class="fa fa-boxes"></i></div>
            <a href="?page=parcel_list" class="small-box-footer">More info</a>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3><?php echo $conn->query("SELECT COUNT(*) as c FROM customers")->fetch_assoc()['c']; ?></h3>
                <p>Customers</p>
            </div>
            <div class="icon"><i class="fa fa-users"></i></div>
            <a href="?page=customer_list" class="small-box-footer">More info</a>
        </div>
    </div>
    
    <?php if($_SESSION['login_type'] == 1): ?>
    <div class="col-md-3">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?php echo $conn->query("SELECT COUNT(*) as c FROM users WHERE type=2")->fetch_assoc()['c']; ?></h3>
                <p>Staff</p>
            </div>
            <div class="icon"><i class="fa fa-user-tie"></i></div>
            <a href="?page=staff_list" class="small-box-footer">More info</a>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Charts Row -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fa fa-chart-pie mr-2"></i>Parcel Status Distribution</h5>
            </div>
            <div class="card-body">
                <canvas id="statusChart" style="height:300px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fa fa-chart-line mr-2"></i>Monthly Parcel Trends</h5>
            </div>
            <div class="card-body">
                <canvas id="trendChart" style="height:300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Parcels -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Recent Parcels</h5>
                <a href="?page=parcel_list" class="btn btn-sm btn-primary float-right">View All</a>
            </div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Ref #</th>
                            <th>Sender</th>
                            <th>Recipient</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $qry = $conn->query("SELECT * FROM parcels ORDER BY id DESC LIMIT 10");
                        while($row = $qry->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $row['reference_number']; ?></td>
                            <td><?php echo $row['sender_name']; ?></td>
                            <td><?php echo $row['recipient_name']; ?></td>
                            <td><span class="badge badge-<?php echo getStatusBadge($row['status']); ?>"><?php echo getStatusLabel($row['status']); ?></span></td>
                            <td><?php echo date('Y-m-d', strtotime($row['date_created'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Status Chart
var ctx1 = document.getElementById('statusChart').getContext('2d');
var statusChart = new Chart(ctx1, {
    type: 'doughnut',
    data: {
        labels: ['Accepted', 'Collected', 'Shipped', 'In-Transit', 'Arrived', 'Out for Delivery', 'Ready', 'Delivered', 'Picked-up', 'Failed'],
        datasets: [{
            data: [<?php 
                echo $status_counts[0] . ',' . 
                     $status_counts[1] . ',' . 
                     $status_counts[2] . ',' . 
                     $status_counts[3] . ',' . 
                     $status_counts[4] . ',' . 
                     $status_counts[5] . ',' . 
                     $status_counts[6] . ',' . 
                     $status_counts[7] . ',' . 
                     $status_counts[8] . ',' . 
                     $status_counts[9]; 
            ?>],
            backgroundColor: [
                '#6c757d', '#17a2b8', '#007bff', '#007bff', '#17a2b8',
                '#ffc107', '#007bff', '#28a745', '#28a745', '#dc3545'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'right',
                labels: { color: '#333', font: { size: 11 } }
            }
        }
    }
});

// Trend Chart
var ctx2 = document.getElementById('trendChart').getContext('2d');
var trendChart = new Chart(ctx2, {
    type: 'line',
    data: {
        labels: [<?php echo "'" . implode("','", $monthly_labels) . "'"; ?>],
        datasets: [{
            label: 'Number of Parcels',
            data: [<?php echo implode(',', $monthly_data); ?>],
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
            x: { grid: { display: false } }
        }
    }
});
</script>

<style>
    .small-box { border-radius: 10px; position: relative; display: block; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    .small-box>.inner { padding: 20px; color: white; }
    .small-box h3 { font-size: 38px; font-weight: bold; margin: 0; }
    .small-box p { font-size: 15px; }
    .small-box .icon { position: absolute; top: 15px; right: 15px; font-size: 70px; color: rgba(0,0,0,0.15); }
    .small-box-footer { background: rgba(0,0,0,0.1); color: white; display: block; padding: 10px; text-align: center; border-radius: 0 0 10px 10px; }
    .bg-info { background: #17a2b8; }
    .bg-success { background: #28a745; }
    .bg-primary { background: #007bff; }
    .bg-warning { background: #ffc107; }
</style>