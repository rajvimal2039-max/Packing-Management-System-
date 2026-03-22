<?php
require_once 'db_connect.php';
if($_SESSION['login_type'] != 1) {
    header("Location: index.php");
    exit();
}

$id = isset($_GET['id']) ? $_GET['id'] : '';
$branch_code = $street = $city = $state = $zip_code = $country = $contact = '';

if(!empty($id)) {
    $qry = $conn->query("SELECT * FROM branches WHERE id = $id");
    if($qry->num_rows > 0) {
        $row = $qry->fetch_assoc();
        $branch_code = $row['branch_code'];
        $street = $row['street'];
        $city = $row['city'];
        $state = $row['state'];
        $zip_code = $row['zip_code'];
        $country = $row['country'];
        $contact = $row['contact'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo empty($id) ? 'Add' : 'Edit'; ?> Branch</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5><?php echo empty($id) ? 'Add New Branch' : 'Edit Branch'; ?></h5>
        </div>
        <div class="card-body">
            <div id="message" style="display:none;" class="alert"></div>
            
            <form id="branchForm">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Branch Code</label>
                            <input type="text" name="branch_code" class="form-control" 
                                   value="<?php echo $branch_code; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Street Address</label>
                            <input type="text" name="street" class="form-control" 
                                   value="<?php echo $street; ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="city" class="form-control" 
                                   value="<?php echo $city; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>State</label>
                            <input type="text" name="state" class="form-control" 
                                   value="<?php echo $state; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>ZIP Code</label>
                            <input type="text" name="zip_code" class="form-control" 
                                   value="<?php echo $zip_code; ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Country</label>
                            <input type="text" name="country" class="form-control" 
                                   value="<?php echo $country ?: 'USA'; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Contact Number</label>
                            <input type="text" name="contact" class="form-control" 
                                   value="<?php echo $contact; ?>" required>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-footer">
            <button class="btn btn-success" id="saveBtn">Save Branch</button>
            <a href="?page=branches" class="btn btn-secondary">Cancel</a>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#saveBtn').click(function() {
        // Show loading
        $(this).html('<span class="spinner-border spinner-border-sm"></span> Saving...').prop('disabled', true);
        
        // Get form data
        var formData = {
            action: 'save_branch',
            id: $('input[name="id"]').val(),
            branch_code: $('input[name="branch_code"]').val(),
            street: $('input[name="street"]').val(),
            city: $('input[name="city"]').val(),
            state: $('input[name="state"]').val(),
            zip_code: $('input[name="zip_code"]').val(),
            country: $('input[name="country"]').val(),
            contact: $('input[name="contact"]').val()
        };
        
        console.log('Sending:', formData);
        
        $.ajax({
            url: 'ajax.php?action=save_branch',
            method: 'POST',
            data: formData,
            success: function(resp) {
                $('#saveBtn').html('Save Branch').prop('disabled', false);
                
                if(resp == 1) {
                    $('#message').removeClass().addClass('alert alert-success').html('Branch saved!').show();
                    setTimeout(() => window.location.href = '?page=branches', 1500);
                } else {
                    $('#message').removeClass().addClass('alert alert-danger').html('Error: ' + resp).show();
                }
            },
            error: function(xhr, status, error) {
                $('#saveBtn').html('Save Branch').prop('disabled', false);
                $('#message').removeClass().addClass('alert alert-danger').html('AJAX Error: ' + error).show();
            }
        });
    });
});
</script>
</body>
</html>