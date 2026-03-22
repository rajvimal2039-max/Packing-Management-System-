<?php
require_once 'db_connect.php';

$id = isset($_GET['id']) ? $_GET['id'] : '';
$firstname = $lastname = $email = $phone = $address = $city = $state = $zip_code = $country = '';
$customer_type = 'individual';
$company_name = $tax_id = $notes = '';
$status = 1;

if(!empty($id)) {
    $qry = $conn->query("SELECT * FROM customers WHERE id = $id");
    if($qry->num_rows > 0) {
        $row = $qry->fetch_assoc();
        $firstname = $row['firstname'];
        $lastname = $row['lastname'];
        $email = $row['email'];
        $phone = $row['phone'];
        $address = $row['address'];
        $city = $row['city'];
        $state = $row['state'];
        $zip_code = $row['zip_code'];
        $country = $row['country'];
        $customer_type = $row['customer_type'];
        $company_name = $row['company_name'];
        $tax_id = $row['tax_id'];
        $notes = $row['notes'];
        $status = $row['status'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo empty($id) ? 'Add' : 'Edit'; ?> Customer</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5><?php echo empty($id) ? 'Add New Customer' : 'Edit Customer'; ?></h5>
        </div>
        <div class="card-body">
            <div id="message" style="display:none;" class="alert"></div>
            
            <form id="customerForm">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="firstname" class="form-control" 
                                   value="<?php echo $firstname; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="lastname" class="form-control" 
                                   value="<?php echo $lastname; ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" 
                                   value="<?php echo $email; ?>">
                            <small class="text-muted">Optional</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control" 
                                   value="<?php echo $phone; ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Address</label>
                            <textarea name="address" class="form-control" rows="2" required><?php echo $address; ?></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="city" class="form-control" value="<?php echo $city; ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>State</label>
                            <input type="text" name="state" class="form-control" value="<?php echo $state; ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>ZIP Code</label>
                            <input type="text" name="zip_code" class="form-control" value="<?php echo $zip_code; ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Country</label>
                            <input type="text" name="country" class="form-control" value="<?php echo $country ?: 'USA'; ?>">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Customer Type</label>
                            <select name="customer_type" class="form-control" id="custType">
                                <option value="individual" <?php echo $customer_type == 'individual' ? 'selected' : ''; ?>>Individual</option>
                                <option value="business" <?php echo $customer_type == 'business' ? 'selected' : ''; ?>>Business</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4" id="companyField" style="<?php echo $customer_type == 'business' ? '' : 'display:none;'; ?>">
                        <div class="form-group">
                            <label>Company Name</label>
                            <input type="text" name="company_name" class="form-control" value="<?php echo $company_name; ?>">
                        </div>
                    </div>
                    <div class="col-md-4" id="taxField" style="<?php echo $customer_type == 'business' ? '' : 'display:none;'; ?>">
                        <div class="form-group">
                            <label>Tax ID</label>
                            <input type="text" name="tax_id" class="form-control" value="<?php echo $tax_id; ?>">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="1" <?php echo $status == 1 ? 'selected' : ''; ?>>Active</option>
                                <option value="0" <?php echo $status == 0 ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control" rows="1"><?php echo $notes; ?></textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-footer">
            <button class="btn btn-success" id="saveBtn">Save Customer</button>
            <a href="?page=customer_list" class="btn btn-secondary">Cancel</a>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Show/hide business fields
    $('#custType').change(function() {
        if($(this).val() == 'business') {
            $('#companyField').show();
            $('#taxField').show();
        } else {
            $('#companyField').hide();
            $('#taxField').hide();
        }
    });
    
    $('#saveBtn').click(function() {
        // Show loading
        $(this).html('<span class="spinner-border spinner-border-sm"></span> Saving...').prop('disabled', true);
        
        // Get form data
        var formData = {
            action: 'save_customer',
            id: $('input[name="id"]').val(),
            firstname: $('input[name="firstname"]').val(),
            lastname: $('input[name="lastname"]').val(),
            email: $('input[name="email"]').val(),
            phone: $('input[name="phone"]').val(),
            address: $('textarea[name="address"]').val(),
            city: $('input[name="city"]').val(),
            state: $('input[name="state"]').val(),
            zip_code: $('input[name="zip_code"]').val(),
            country: $('input[name="country"]').val(),
            customer_type: $('select[name="customer_type"]').val(),
            company_name: $('input[name="company_name"]').val(),
            tax_id: $('input[name="tax_id"]').val(),
            notes: $('textarea[name="notes"]').val(),
            status: $('select[name="status"]').val()
        };
        
        console.log('Sending:', formData);
        
        $.ajax({
            url: 'ajax.php?action=save_customer',
            method: 'POST',
            data: formData,
            success: function(resp) {
                $('#saveBtn').html('Save Customer').prop('disabled', false);
                
                if(resp == 1) {
                    $('#message').removeClass().addClass('alert alert-success').html('Customer saved!').show();
                    setTimeout(() => window.location.href = '?page=customer_list', 1500);
                } else {
                    $('#message').removeClass().addClass('alert alert-danger').html('Error: ' + resp).show();
                }
            },
            error: function(xhr, status, error) {
                $('#saveBtn').html('Save Customer').prop('disabled', false);
                $('#message').removeClass().addClass('alert alert-danger').html('AJAX Error: ' + error).show();
            }
        });
    });
});
</script>
</body>
</html>