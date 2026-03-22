<?php
require_once 'db_connect.php';
if(!isset($_GET['id'])) die("Invalid request");
$id = $_GET['id'];

$qry = $conn->query("SELECT p.*, fb.branch_code as fcode, fb.city as fcity, 
                     tb.branch_code as tcode, tb.city as tcity 
                     FROM parcels p 
                     LEFT JOIN branches fb ON p.from_branch_id = fb.id 
                     LEFT JOIN branches tb ON p.to_branch_id = tb.id 
                     WHERE p.id = $id");
if($qry->num_rows == 0) die("Parcel not found");
$row = $qry->fetch_assoc();

$items = $conn->query("SELECT * FROM parcel_items WHERE parcel_id = $id");
$tracking = $conn->query("SELECT * FROM parcel_tracking WHERE parcel_id = $id ORDER BY date_created DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Parcel Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
    <style>
        .detail-card { margin-bottom: 20px; }
        .detail-header { background: #007bff; color: white; padding: 10px; border-radius: 5px 5px 0 0; }
        .detail-body { background: #f8f9fa; padding: 15px; border: 1px solid #dee2e6; border-radius: 0 0 5px 5px; }
        .tracking-item { padding: 10px; border-left: 3px solid #007bff; margin-bottom: 10px; background: #f8f9fa; }
    </style>
</head>
<body>
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4>Parcel Details: <?php echo $row['reference_number']; ?></h4>
        </div>
        <div class="card-body">
            
            <div class="row">
                <!-- Sender Information -->
                <div class="col-md-6 detail-card">
                    <div class="detail-header">
                        <h5 class="mb-0"><i class="fa fa-user"></i> Sender Information</h5>
                    </div>
                    <div class="detail-body">
                        <table class="table table-sm table-borderless">
                            <tr><th style="width:100px;">Name:</th><td><?php echo $row['sender_name']; ?></td></tr>
                            <tr><th>Address:</th><td><?php echo $row['sender_address']; ?></td></tr>
                            <tr><th>Contact:</th><td><?php echo $row['sender_contact']; ?></td></tr>
                        </table>
                    </div>
                </div>
                
                <!-- Recipient Information -->
                <div class="col-md-6 detail-card">
                    <div class="detail-header">
                        <h5 class="mb-0"><i class="fa fa-user"></i> Recipient Information</h5>
                    </div>
                    <div class="detail-body">
                        <table class="table table-sm table-borderless">
                            <tr><th style="width:100px;">Name:</th><td><?php echo $row['recipient_name']; ?></td></tr>
                            <tr><th>Address:</th><td><?php echo $row['recipient_address']; ?></td></tr>
                            <tr><th>Contact:</th><td><?php echo $row['recipient_contact']; ?></td></tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="row mt-3">
                <!-- Delivery Information -->
                <div class="col-md-6 detail-card">
                    <div class="detail-header bg-success">
                        <h5 class="mb-0"><i class="fa fa-truck"></i> Delivery Information</h5>
                    </div>
                    <div class="detail-body">
                        <table class="table table-sm table-borderless">
                            <tr><th style="width:120px;">Type:</th><td><?php echo $row['type'] == 1 ? 'Deliver' : 'Pickup'; ?></td></tr>
                            <tr><th>From Branch:</th><td><?php echo $row['fcode'] ? $row['fcode'].' - '.$row['fcity'] : 'N/A'; ?></td></tr>
                            <tr><th>To Branch:</th><td><?php echo $row['tcode'] ? $row['tcode'].' - '.$row['tcity'] : 'N/A'; ?></td></tr>
                        </table>
                    </div>
                </div>
                
                <!-- Parcel Items -->
                <div class="col-md-6 detail-card">
                    <div class="detail-header bg-info">
                        <h5 class="mb-0"><i class="fa fa-box"></i> Parcel Items</h5>
                    </div>
                    <div class="detail-body">
                        <table class="table table-sm table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>Weight</th>
                                    <th>Dimensions</th>
                                    <th class="text-right">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total = 0;
                                while($item = $items->fetch_assoc()): 
                                    $total += $item['price'];
                                ?>
                                <tr>
                                    <td><?php echo $item['weight']; ?> kg</td>
                                    <td><?php echo $item['height'].'x'.$item['length'].'x'.$item['width']; ?> cm</td>
                                    <td class="text-right">$<?php echo number_format($item['price'],2); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot class="bg-light font-weight-bold">
                                <tr>
                                    <td colspan="2" class="text-right">Total:</td>
                                    <td class="text-right">$<?php echo number_format($total,2); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Tracking History -->
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="detail-header bg-warning">
                        <h5 class="mb-0"><i class="fa fa-history"></i> Tracking History</h5>
                    </div>
                    <div class="detail-body">
                        <?php if($tracking->num_rows > 0): ?>
                            <?php while($track = $tracking->fetch_assoc()): ?>
                                <div class="tracking-item">
                                    <div class="d-flex justify-content-between">
                                        <strong>
                                            <?php 
                                            $labels = ['Accepted', 'Collected', 'Shipped', 'In-Transit', 'Arrived', 
                                                      'Out for Delivery', 'Ready', 'Delivered', 'Picked-up', 'Failed'];
                                            echo $labels[$track['status']] ?? 'Unknown';
                                            ?>
                                        </strong>
                                        <small class="text-muted"><?php echo date('Y-m-d H:i:s', strtotime($track['date_created'])); ?></small>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-muted">No tracking history available</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="card-footer">
            <a href="javascript:window.close()" class="btn btn-secondary">Close</a>
            <a href="?page=edit_parcel&id=<?php echo $id; ?>" class="btn btn-primary">Edit Parcel</a>
        </div>
    </div>
</div>
</body>
</html>