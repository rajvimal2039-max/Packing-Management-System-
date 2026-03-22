<?php
require_once 'db_connect.php';
if(!isset($_GET['id'])) die("Invalid request");
$id = $_GET['id'];

$qry = $conn->query("SELECT c.*, u.firstname as created_by_name FROM customers c LEFT JOIN users u ON c.created_by = u.id WHERE c.id = $id");
if($qry->num_rows == 0) die("Customer not found");
$row = $qry->fetch_assoc();

$parcels = $conn->query("SELECT * FROM parcels WHERE customer_id = $id ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Customer Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container-fluid mt-3">
    <div class="card">
        <div class="card-header bg-info text-white">
            <h5>Customer Details</h5>
        </div>
        <div class="card-body">
            <h3 class="text-center text-primary"><?php echo $row['firstname'].' '.$row['lastname']; ?></h3>
            <p class="text-center"><strong>Code:</strong> <?php echo $row['customer_code']; ?></p>
            
            <div class="row mt-4">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr><th colspan="2" class="bg-light">Personal Information</th></tr>
                        <tr><th>Name:</th><td><?php echo $row['firstname'].' '.$row['lastname']; ?></td></tr>
                        <tr><th>Type:</th><td><?php echo ucfirst($row['customer_type']); ?></td></tr>
                        <tr><th>Email:</th><td><?php echo $row['email'] ?: 'N/A'; ?></td></tr>
                        <tr><th>Phone:</th><td><?php echo $row['phone']; ?></td></tr>
                        <tr><th>Status:</th><td><?php echo $row['status'] ? 'Active' : 'Inactive'; ?></td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr><th colspan="2" class="bg-light">Address Information</th></tr>
                        <tr><th>Address:</th><td><?php echo $row['address']; ?></td></tr>
                        <tr><th>City:</th><td><?php echo $row['city'] ?: 'N/A'; ?></td></tr>
                        <tr><th>State:</th><td><?php echo $row['state'] ?: 'N/A'; ?></td></tr>
                        <tr><th>ZIP:</th><td><?php echo $row['zip_code'] ?: 'N/A'; ?></td></tr>
                        <tr><th>Country:</th><td><?php echo $row['country']; ?></td></tr>
                    </table>
                </div>
            </div>
            
            <?php if($row['customer_type'] == 'business' && $row['company_name']): ?>
            <div class="row mt-3">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <tr><th colspan="2" class="bg-light">Business Information</th></tr>
                        <tr><th>Company:</th><td><?php echo $row['company_name']; ?></td></tr>
                        <tr><th>Tax ID:</th><td><?php echo $row['tax_id'] ?: 'N/A'; ?></td></tr>
                    </table>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="row mt-3">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <tr><th colspan="3" class="bg-light">Statistics</th></tr>
                        <tr class="text-center">
                            <th>Total Parcels</th>
                            <th>Total Spent</th>
                            <th>Average per Parcel</th>
                        </tr>
                        <tr class="text-center">
                            <td><h3><?php echo $row['total_parcels']; ?></h3></td>
                            <td><h3>$<?php echo number_format($row['total_spent'],2); ?></h3></td>
                            <td><h3>$<?php echo $row['total_parcels'] > 0 ? number_format($row['total_spent']/$row['total_parcels'],2) : '0.00'; ?></h3></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <?php if($row['notes']): ?>
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">Notes</div>
                        <div class="card-body"><?php echo $row['notes']; ?></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-dark text-white">Recent Parcels</div>
                        <div class="card-body">
                            <?php if($parcels->num_rows > 0): ?>
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr><th>Ref #</th><th>Recipient</th><th>Status</th><th>Date</th><th>Amount</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php while($p = $parcels->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $p['reference_number']; ?></td>
                                            <td><?php echo $p['recipient_name']; ?></td>
                                            <td><?php echo getStatusLabel($p['status']); ?></td>
                                            <td><?php echo date('Y-m-d',strtotime($p['date_created'])); ?></td>
                                            <td>$<?php echo number_format($p['total'],2); ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="text-muted">No parcels found</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button class="btn btn-secondary" onclick="window.close()">Close</button>
            <a href="?page=edit_customer&id=<?php echo $id; ?>" class="btn btn-primary">Edit</a>
        </div>
    </div>
</div>
</body>
</html>