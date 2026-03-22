<?php
require_once 'db_connect.php';
if($_SESSION['login_type'] != 1) {
    header("Location: index.php");
    exit();
}

$id = isset($_GET['id']) ? $_GET['id'] : '';
$firstname = $lastname = $email = $contact = $address = $branch_id = '';

if(!empty($id)) {
    $qry = $conn->query("SELECT * FROM users WHERE id = $id AND type = 2");
    if($qry->num_rows > 0) {
        $row = $qry->fetch_assoc();
        $firstname = $row['firstname'];
        $lastname = $row['lastname'];
        $email = $row['email'];
        $contact = $row['contact'];
        $address = $row['address'];
        $branch_id = $row['branch_id'];
    }
}

// Get branches
$branches = $conn->query("SELECT * FROM branches");
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo empty($id) ? 'Add' : 'Edit'; ?> Staff</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5><?php echo empty($id) ? 'Add New Staff' : 'Edit Staff'; ?></h5>
        </div>
        <div class="card-body">
            <div id="message" style="display:none;" class="alert"></div>
            
            <form id="staffForm">
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
                                   value="<?php echo $email; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Branch</label>
                            <select name="branch_id" class="form-control" required>
                                <option value="">Select</option>
                                <?php while($b = $branches->fetch_assoc()): ?>
                                <option value="<?php echo $b['id']; ?>" 
                                    <?php echo ($branch_id == $b['id']) ? 'selected' : ''; ?>>
                                    <?php echo $b['branch_code']; ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Contact</label>
                            <input type="text" name="contact" class="form-control" 
                                   value="<?php echo $contact; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Address</label>
                            <textarea name="address" class="form-control" required><?php echo $address; ?></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Password <?php echo empty($id) ? '' : '(Leave blank to keep)'; ?></label>
                            <input type="password" name="password" class="form-control" 
                                   <?php echo empty($id) ? 'required' : ''; ?>>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="cpassword" class="form-control" 
                                   <?php echo empty($id) ? 'required' : ''; ?>>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-footer">
            <button class="btn btn-success" id="saveBtn">Save Staff</button>
            <a href="?page=staff_list" class="btn btn-secondary">Cancel</a>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#saveBtn').click(function() {
        // Get passwords
        var pass = $('input[name="password"]').val();
        var cpass = $('input[name="cpassword"]').val();
        
        // Check if editing and passwords match if provided
        <?php if(empty($id)): ?>
        if(pass != cpass) {
            $('#message').removeClass().addClass('alert alert-danger').html('Passwords do not match!').show();
            return;
        }
        <?php else: ?>
        if(pass != '' && pass != cpass) {
            $('#message').removeClass().addClass('alert alert-danger').html('Passwords do not match!').show();
            return;
        }
        <?php endif; ?>
        
        // Show loading
        $(this).html('<span class="spinner-border spinner-border-sm"></span> Saving...').prop('disabled', true);
        
        // Get form data
        var formData = {
            action: 'save_user',
            id: $('input[name="id"]').val(),
            firstname: $('input[name="firstname"]').val(),
            lastname: $('input[name="lastname"]').val(),
            branch_id: $('select[name="branch_id"]').val(),
            email: $('input[name="email"]').val(),
            contact: $('input[name="contact"]').val(),
            address: $('textarea[name="address"]').val(),
            password: pass
        };
        
        console.log('Sending:', formData);
        
        $.ajax({
            url: 'ajax.php?action=save_user',
            method: 'POST',
            data: formData,
            success: function(resp) {
                $('#saveBtn').html('Save Staff').prop('disabled', false);
                
                if(resp == 1) {
                    $('#message').removeClass().addClass('alert alert-success').html('Staff saved!').show();
                    setTimeout(() => window.location.href = '?page=staff_list', 1500);
                } else if(resp == 2) {
                    $('#message').removeClass().addClass('alert alert-danger').html('Email already exists!').show();
                } else {
                    $('#message').removeClass().addClass('alert alert-danger').html('Error: ' + resp).show();
                }
            },
            error: function(xhr, status, error) {
                $('#saveBtn').html('Save Staff').prop('disabled', false);
                $('#message').removeClass().addClass('alert alert-danger').html('AJAX Error: ' + error).show();
            }
        });
    });
});
</script>
</body>
</html>