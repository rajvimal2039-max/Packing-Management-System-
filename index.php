<?php
session_start();
if(!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'db_connect.php';
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packing Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
    <style>
        body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .main-header { background: #343a40; color: white; padding: 15px 0; margin-bottom: 30px; }
        .sidebar { background: white; min-height: calc(100vh - 120px); padding: 20px; border-right: 1px solid #dee2e6; }
        .sidebar .nav-link { color: #495057; padding: 12px 15px; border-radius: 5px; margin-bottom: 5px; }
        .sidebar .nav-link:hover { background: #e9ecef; color: #007bff; }
        .sidebar .nav-link.active { background: #007bff; color: white; }
        .content-wrapper { background: white; padding: 20px; border-radius: 10px; min-height: calc(100vh - 120px); }
        .loader { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999; 
                  background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.2); }
    </style>
</head>
<body>
    <div class="main-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <h4><i class="fa fa-box mr-2"></i> Packing Management System</h4>
                </div>
                <div class="col-md-6 text-right">
                    <span class="mr-3"><i class="fa fa-user-circle"></i> <?php echo $_SESSION['login_name']; ?></span>
                    <a href="logout.php" class="btn btn-sm btn-outline-light">Logout</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 p-0">
                <div class="sidebar">
                    <nav class="nav flex-column">
                        <!-- Main Menu -->
                        <a class="nav-link <?php echo $page == 'home' ? 'active' : ''; ?>" href="?page=home">
                            <i class="fa fa-home"></i> Dashboard
                        </a>
                        
                        <a class="nav-link <?php echo $page == 'parcel_list' ? 'active' : ''; ?>" href="?page=parcel_list">
                            <i class="fa fa-boxes"></i> Parcels
                        </a>
                        
                        <a class="nav-link <?php echo $page == 'new_parcel' ? 'active' : ''; ?>" href="?page=new_parcel">
                            <i class="fa fa-plus-circle"></i> Add Parcel
                        </a>
                        
                        <a class="nav-link <?php echo $page == 'track_parcel' ? 'active' : ''; ?>" href="?page=track_parcel">
                            <i class="fa fa-search"></i> Track Parcel
                        </a>
                        
                        <!-- CUSTOMER MANAGEMENT - ADDED HERE -->
                        <a class="nav-link <?php echo $page == 'customer_list' ? 'active' : ''; ?>" href="?page=customer_list">
                            <i class="fa fa-users"></i> Customers
                        </a>
                        
                        <?php if($_SESSION['login_type'] == 1): // Admin only ?>
                            <hr>
                            <h6 class="text-muted px-3">Administration</h6>
                            
                            <a class="nav-link <?php echo $page == 'staff_list' ? 'active' : ''; ?>" href="?page=staff_list">
                                <i class="fa fa-user-tie"></i> Staff
                            </a>
                            
                            <a class="nav-link <?php echo $page == 'branches' ? 'active' : ''; ?>" href="?page=branches">
                                <i class="fa fa-building"></i> Branches
                            </a>
                            
                            <a class="nav-link <?php echo $page == 'reports' ? 'active' : ''; ?>" href="?page=reports">
                                <i class="fa fa-chart-bar"></i> Reports
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
            </div>
            
            <div class="col-md-10">
                <div class="content-wrapper">
                    <?php
                    // Map page names to actual file names
                    $files = [
                        'home' => 'home.php',
                        'parcel_list' => 'parcel_list.php',
                        'new_parcel' => 'manage_parcel.php',
                        'edit_parcel' => 'manage_parcel.php',
                        'customer_list' => 'customer_list.php',
                        'new_customer' => 'manage_customer.php',
                        'edit_customer' => 'manage_customer.php',
                        'view_customer' => 'view_customer.php',
                        'staff_list' => 'staff_list.php',
                        'new_staff' => 'manage_staff.php',
                        'edit_staff' => 'manage_staff.php',
                        'branches' => 'branches_list.php',
                        'new_branch' => 'manage_branches.php',
                        'edit_branch' => 'manage_branches.php',
                        'track_parcel' => 'track_parcel.php',
                        'reports' => 'report.php'
                    ];
                    
                    $file = isset($files[$page]) ? $files[$page] : 'home.php';
                    
                    if(file_exists($file)) {
                        include $file;
                    } else {
                        echo "<div class='alert alert-danger'>";
                        echo "<h5>Page Not Found</h5>";
                        echo "<p>The file <strong>" . $file . "</strong> does not exist.</p>";
                        echo "<p>Requested page: <strong>" . $page . "</strong></p>";
                        echo "</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    
    <script>
        function start_load() {
            $('body').append('<div class="loader"><i class="fa fa-spinner fa-spin fa-3x"></i><p>Loading...</p></div>');
        }
        
        function end_load() {
            $('.loader').remove();
        }
        
        function alert_toast(msg, type) {
            alert(msg);
        }
        
        function _conf(msg, func, id) {
            if(confirm(msg)) {
                window[func](id);
            }
        }
    </script>
</body>
</html>