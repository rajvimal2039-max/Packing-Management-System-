<?php
require_once 'db_connect.php';
if($_SESSION['login_type'] != 1) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Branch Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>
<div class="container-fluid mt-3">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5>Branch Management</h5>
            <a href="?page=new_branch" class="btn btn-light btn-sm float-right">+ Add Branch</a>
        </div>
        <div class="card-body">
            <div id="message" style="display:none;" class="alert"></div>
            
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Branch Code</th>
                        <th>Address</th>
                        <th>City</th>
                        <th>Contact</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $qry = $conn->query("SELECT * FROM branches ORDER BY id DESC");
                    while($row = $qry->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><strong><?php echo $row['branch_code']; ?></strong></td>
                        <td><?php echo $row['street']; ?></td>
                        <td><?php echo $row['city']; ?></td>
                        <td><?php echo $row['contact']; ?></td>
                        <td>
                            <a href="?page=edit_branch&id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">
                                <i class="fa fa-edit"></i>
                            </a>
                            <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $row['id']; ?>">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.delete-btn').click(function() {
        var id = $(this).data('id');
        if(confirm('Delete this branch?')) {
            $.ajax({
                url: 'ajax.php?action=delete_branch',
                method: 'POST',
                data: { id: id },
                success: function(resp) {
                    if(resp == 1) {
                        $('#message').removeClass().addClass('alert alert-success').html('Branch deleted!').show();
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        $('#message').removeClass().addClass('alert alert-danger').html('Error deleting!').show();
                    }
                }
            });
        }
    });
});
</script>
</body>
</html>