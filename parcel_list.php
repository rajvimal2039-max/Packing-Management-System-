<?php
require_once 'db_connect.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Parcel List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container-fluid mt-3">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5>Parcel List</h5>
            <a href="?page=new_parcel" class="btn btn-light btn-sm float-right">+ Add New</a>
        </div>
        <div class="card-body">
            
            <!-- Success Message -->
            <?php if(isset($_GET['updated'])): ?>
            <div class="alert alert-success">
                ✅ Status updated successfully!
            </div>
            <?php endif; ?>
            
            <?php if(isset($_GET['deleted'])): ?>
            <div class="alert alert-success">
                ✅ Parcel deleted successfully!
            </div>
            <?php endif; ?>
            
            <table class="table table-bordered table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>Reference</th>
                        <th>Sender</th>
                        <th>Recipient</th>
                        <th>Status</th>
                        <th colspan="4" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $qry = $conn->query("SELECT * FROM parcels ORDER BY id DESC");
                    while($row = $qry->fetch_assoc()):
                        $status_names = ['Accepted','Collected','Shipped','In-Transit','Arrived','Out for Delivery','Ready','Delivered','Picked-up','Failed'];
                        $status_colors = ['secondary','info','primary','primary','info','warning','primary','success','success','danger'];
                    ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><strong><?php echo $row['reference_number']; ?></strong></td>
                        <td><?php echo $row['sender_name']; ?></td>
                        <td><?php echo $row['recipient_name']; ?></td>
                        <td>
                            <span class="badge badge-<?php echo $status_colors[$row['status']]; ?>" style="font-size:14px; padding:8px;">
                                <?php echo $status_names[$row['status']]; ?>
                            </span>
                        </td>
                        
                        <!-- VIEW BUTTON -->
                        <td>
                            <a href="view_parcel.php?id=<?php echo $row['id']; ?>" 
                               class="btn btn-info btn-sm" 
                               target="_blank">
                                <i class="fa fa-eye"></i> View
                            </a>
                        </td>
                        
                        <!-- UPDATE BUTTON (opens modal) -->
                        <td>
                            <button class="btn btn-warning btn-sm" 
                                    onclick="openUpdateModal(<?php echo $row['id']; ?>, <?php echo $row['status']; ?>)">
                                <i class="fa fa-edit"></i> Update
                            </button>
                        </td>
                        
                        <!-- EDIT BUTTON -->
                        <td>
                            <a href="?page=edit_parcel&id=<?php echo $row['id']; ?>" 
                               class="btn btn-primary btn-sm">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                        </td>
                        
                        <!-- DELETE BUTTON -->
                        <td>
                            <a href="delete_parcel.php?id=<?php echo $row['id']; ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Delete this parcel?')">
                                <i class="fa fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="updateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Update Parcel Status</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modal_parcel_id">
                <div class="form-group">
                    <label>Select New Status:</label>
                    <select class="form-control" id="modal_status">
                        <option value="0">0 - Accepted</option>
                        <option value="1">1 - Collected</option>
                        <option value="2">2 - Shipped</option>
                        <option value="3">3 - In-Transit</option>
                        <option value="4">4 - Arrived</option>
                        <option value="5">5 - Out for Delivery</option>
                        <option value="6">6 - Ready to Pickup</option>
                        <option value="7">7 - Delivered</option>
                        <option value="8">8 - Picked-up</option>
                        <option value="9">9 - Failed</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveStatusUpdate()">Update Status</button>
            </div>
        </div>
    </div>
</div>

<script>
function openUpdateModal(id, currentStatus) {
    document.getElementById('modal_parcel_id').value = id;
    document.getElementById('modal_status').value = currentStatus;
    $('#updateModal').modal('show');
}

function saveStatusUpdate() {
    var id = document.getElementById('modal_parcel_id').value;
    var status = document.getElementById('modal_status').value;
    
    // Show loading
    var btn = document.querySelector('#updateModal .btn-primary');
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Updating...';
    btn.disabled = true;
    
    // Use AJAX to update
    $.ajax({
        url: 'ajax.php?action=update_status',
        method: 'POST',
        data: { id: id, status: status },
        success: function(resp) {
            $('#updateModal').modal('hide');
            if(resp == 1) {
                alert('Status updated successfully!');
                location.reload();
            } else {
                alert('Error: ' + resp);
            }
            btn.innerHTML = 'Update Status';
            btn.disabled = false;
        },
        error: function(xhr, status, error) {
            $('#updateModal').modal('hide');
            alert('Error: ' + error);
            btn.innerHTML = 'Update Status';
            btn.disabled = false;
        }
    });
}
</script>

<style>
    .btn-group { display: flex; gap: 5px; }
    .table td { vertical-align: middle; }
    .btn-sm { padding: 5px 10px; margin: 2px; }
</style>
</body>
</html>