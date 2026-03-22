<?php
require_once 'db_connect.php';

// Get branches for dropdown
$branches = $conn->query("SELECT * FROM branches");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Parcel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5>Add New Parcel</h5>
        </div>
        <div class="card-body">
            <div id="message" style="display:none;" class="alert"></div>
            
            <form id="simpleParcelForm">
                <!-- Sender Information -->
                <h6 class="mt-3">Sender Information</h6>
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" id="sender_name" class="form-control" placeholder="Name" value="vimal" required>
                    </div>
                    <div class="col-md-5">
                        <input type="text" id="sender_address" class="form-control" placeholder="Address" value="thiruvallur" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" id="sender_contact" class="form-control" placeholder="Contact" value="7305237272" required>
                    </div>
                </div>
                
                <!-- Recipient Information -->
                <h6 class="mt-3">Recipient Information</h6>
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" id="recipient_name" class="form-control" placeholder="Name" value="raj" required>
                    </div>
                    <div class="col-md-5">
                        <input type="text" id="recipient_address" class="form-control" placeholder="Address" value="karanodai" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" id="recipient_contact" class="form-control" placeholder="Contact" value="9962367887" required>
                    </div>
                </div>
                
                <!-- Delivery Information -->
                <h6 class="mt-3">Delivery Information</h6>
                <div class="row">
                    <div class="col-md-4">
                        <select id="type" class="form-control">
                            <option value="0">Pickup</option>
                            <option value="1">Deliver</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select id="from_branch" class="form-control" required>
                            <option value="">From Branch</option>
                            <?php while($b = $branches->fetch_assoc()): ?>
                            <option value="<?php echo $b['id']; ?>" <?php echo ($b['id']==2)?'selected':''; ?>>
                                <?php echo $b['branch_code']; ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select id="to_branch" class="form-control" required>
                            <option value="">To Branch</option>
                            <?php 
                            $branches->data_seek(0);
                            while($b = $branches->fetch_assoc()): 
                            ?>
                            <option value="<?php echo $b['id']; ?>" <?php echo ($b['id']==3)?'selected':''; ?>>
                                <?php echo $b['branch_code']; ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                
                <!-- Item -->
                <h6 class="mt-3">Item</h6>
                <div class="row">
                    <div class="col-md-2">
                        <input type="number" step="0.01" id="weight" class="form-control" placeholder="Weight" value="80.00" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" id="height" class="form-control" placeholder="Height" value="5.2" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" id="length" class="form-control" placeholder="Length" value="7.00" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" id="width" class="form-control" placeholder="Width" value="4.00" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" id="price" class="form-control" placeholder="Price" value="1000" required>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-footer">
            <button class="btn btn-success" id="saveBtn">SAVE PARCEL</button>
            <a href="?page=parcel_list" class="btn btn-secondary">Cancel</a>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#saveBtn').click(function() {
        // Show loading
        $(this).html('<span class="spinner-border spinner-border-sm"></span> Saving...').prop('disabled', true);
        
        // Get form values
        var formData = {
            action: 'save_parcel',
            sender_name: $('#sender_name').val(),
            sender_address: $('#sender_address').val(),
            sender_contact: $('#sender_contact').val(),
            recipient_name: $('#recipient_name').val(),
            recipient_address: $('#recipient_address').val(),
            recipient_contact: $('#recipient_contact').val(),
            type: $('#type').val(),
            from_branch_id: $('#from_branch').val(),
            to_branch_id: $('#to_branch').val(),
            weight: [$('#weight').val()],
            height: [$('#height').val()],
            length: [$('#length').val()],
            width: [$('#width').val()],
            price: [$('#price').val()],
            customer_id: ''
        };
        
        console.log('Sending:', formData);
        
        // Send AJAX
        $.ajax({
            url: 'ajax.php?action=save_parcel',
            method: 'POST',
            data: formData,
            success: function(response) {
                $('#saveBtn').html('SAVE PARCEL').prop('disabled', false);
                
                $('#message').removeClass('alert-danger alert-success').show();
                
                if(response == 1 || response == "1") {
                    $('#message').addClass('alert-success').html('✅ Parcel saved successfully!');
                    setTimeout(function() {
                        window.location.href = '?page=parcel_list';
                    }, 2000);
                } else {
                    $('#message').addClass('alert-danger').html('❌ Error: ' + response);
                    console.log('Error response:', response);
                }
            },
            error: function(xhr, status, error) {
                $('#saveBtn').html('SAVE PARCEL').prop('disabled', false);
                $('#message').removeClass('alert-danger alert-success').addClass('alert-danger').show()
                    .html('❌ AJAX Error: ' + error);
                console.log('AJAX Error:', error);
                console.log('Response:', xhr.responseText);
            }
        });
    });
});
</script>
</body>
</html>