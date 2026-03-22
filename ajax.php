<?php
session_start();
require_once 'db_connect.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

// TEST ACTION - Add this for debugging
if($action == 'test') {
    echo "AJAX is working!";
    exit;
}

// ============================================
// LOGIN
// ============================================
if($action == 'login') {
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $qry = $conn->query("SELECT * FROM users WHERE email='$email' AND password='$pass'");
    if($qry->num_rows > 0) {
        $row = $qry->fetch_assoc();
        $_SESSION['login_id'] = $row['id'];
        $_SESSION['login_name'] = $row['firstname'].' '.$row['lastname'];
        $_SESSION['login_type'] = $row['type'];
        $_SESSION['login_branch_id'] = $row['branch_id'];
        echo "1";
    } else echo "0";
    exit;
}

// ============================================
// CUSTOMER MANAGEMENT
// ============================================

// Save customer
if($action == 'save_customer') {
    $id = $_POST['id'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip = $_POST['zip_code'];
    $country = $_POST['country'];
    $type = $_POST['customer_type'];
    $company = $_POST['company_name'];
    $tax = $_POST['tax_id'];
    $notes = $_POST['notes'];
    $status = $_POST['status'];
    
    // Generate customer code
    $code = 'CUST'.date('Y').rand(1000,9999);
    
    if(empty($id)) {
        $sql = "INSERT INTO customers (customer_code, firstname, lastname, email, phone, address, city, state, zip_code, country, customer_type, company_name, tax_id, notes, status, created_by, date_created) 
                VALUES ('$code', '$firstname', '$lastname', '$email', '$phone', '$address', '$city', '$state', '$zip', '$country', '$type', ".($company?"'$company'":"NULL").", ".($tax?"'$tax'":"NULL").", ".($notes?"'$notes'":"NULL").", '$status', '{$_SESSION['login_id']}', NOW())";
    } else {
        $sql = "UPDATE customers SET firstname='$firstname', lastname='$lastname', email='$email', phone='$phone', address='$address', city='$city', state='$state', zip_code='$zip', country='$country', customer_type='$type', company_name=".($company?"'$company'":"NULL").", tax_id=".($tax?"'$tax'":"NULL").", notes=".($notes?"'$notes'":"NULL").", status='$status' WHERE id=$id";
    }
    
    echo $conn->query($sql) ? "1" : "0";
    exit;
}

// Delete customer
if($action == 'delete_customer') {
    $id = $_POST['id'];
    $conn->query("UPDATE parcels SET customer_id=NULL WHERE customer_id=$id");
    echo $conn->query("DELETE FROM customers WHERE id=$id") ? "1" : "0";
    exit;
}

// Get customers for dropdown
if($action == 'get_customers') {
    $search = $_POST['search'] ?? '';
    $where = "WHERE status=1";
    if($search) $where .= " AND (firstname LIKE '%$search%' OR lastname LIKE '%$search%' OR email LIKE '%$search%')";
    
    $qry = $conn->query("SELECT id, customer_code, firstname, lastname, email, phone FROM customers $where ORDER BY firstname LIMIT 20");
    $data = [];
    while($row = $qry->fetch_assoc()) {
        $data[] = [
            'id' => $row['id'],
            'text' => $row['firstname'].' '.$row['lastname'].' ('.$row['customer_code'].')',
            'email' => $row['email'],
            'phone' => $row['phone']
        ];
    }
    echo json_encode($data);
    exit;
}

// ============================================
// BRANCH MANAGEMENT
// ============================================

if($action == 'save_branch') {
    $id = $_POST['id'];
    $code = $_POST['branch_code'];
    $street = $_POST['street'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip = $_POST['zip_code'];
    $country = $_POST['country'];
    $contact = $_POST['contact'];
    
    if(empty($id)) {
        $sql = "INSERT INTO branches (branch_code,street,city,state,zip_code,country,contact,date_created) 
                VALUES ('$code','$street','$city','$state','$zip','$country','$contact',NOW())";
    } else {
        $sql = "UPDATE branches SET branch_code='$code',street='$street',city='$city',
                state='$state',zip_code='$zip',country='$country',contact='$contact' WHERE id=$id";
    }
    echo $conn->query($sql) ? "1" : "0";
    exit;
}

if($action == 'delete_branch') {
    $id = $_POST['id'];
    echo $conn->query("DELETE FROM branches WHERE id=$id") ? "1" : "0";
    exit;
}

// ============================================
// STAFF MANAGEMENT
// ============================================

if($action == 'save_user') {
    $id = $_POST['id'];
    $fname = $_POST['firstname'];
    $lname = $_POST['lastname'];
    $branch = $_POST['branch_id'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    
    $check = $conn->query("SELECT * FROM users WHERE email='$email'".($id?" AND id!=$id":""));
    if($check->num_rows > 0) { echo "2"; exit; }
    
    if(empty($id)) {
        $pass = $_POST['password'];
        $sql = "INSERT INTO users (firstname,lastname,branch_id,email,password,contact,address,type,date_created) 
                VALUES ('$fname','$lname','$branch','$email','$pass','$contact','$address',2,NOW())";
    } else {
        if(!empty($_POST['password'])) {
            $pass = $_POST['password'];
            $sql = "UPDATE users SET firstname='$fname',lastname='$lname',branch_id='$branch',
                    email='$email',password='$pass',contact='$contact',address='$address' WHERE id=$id";
        } else {
            $sql = "UPDATE users SET firstname='$fname',lastname='$lname',branch_id='$branch',
                    email='$email',contact='$contact',address='$address' WHERE id=$id";
        }
    }
    echo $conn->query($sql) ? "1" : "0";
    exit;
}

if($action == 'delete_user') {
    $id = $_POST['id'];
    echo $conn->query("DELETE FROM users WHERE id=$id") ? "1" : "0";
    exit;
}

// ============================================
// PARCEL MANAGEMENT
// ============================================

if($action == 'save_parcel') {
    // Log received data for debugging
    error_log("save_parcel called with POST: " . print_r($_POST, true));
    
    $id = $_POST['id'] ?? '';
    $customer = isset($_POST['customer_id']) && $_POST['customer_id'] != '' ? $_POST['customer_id'] : 'NULL';
    $sname = $_POST['sender_name'] ?? '';
    $saddr = $_POST['sender_address'] ?? '';
    $scont = $_POST['sender_contact'] ?? '';
    $rname = $_POST['recipient_name'] ?? '';
    $raddr = $_POST['recipient_address'] ?? '';
    $rcont = $_POST['recipient_contact'] ?? '';
    $type = $_POST['type'] ?? 0;
    $from = $_POST['from_branch_id'] ?? '';
    $to = $_POST['to_branch_id'] ?? '';
    
    // Calculate total
    $total = 0;
    if(isset($_POST['price']) && is_array($_POST['price'])) {
        foreach($_POST['price'] as $p) {
            $total += floatval($p);
        }
    }
    
    // Generate reference number
    $ref = 'CMP' . date('Ymd') . rand(1000, 9999);
    
    if(empty($id)) {
        // Insert new parcel
        $sql = "INSERT INTO parcels (reference_number, customer_id, sender_name, sender_address, sender_contact,
                recipient_name, recipient_address, recipient_contact, type, from_branch_id, to_branch_id,
                total, status, date_created) 
                VALUES ('$ref', $customer, '$sname', '$saddr', '$scont', '$rname', 
                '$raddr', '$rcont', '$type', '$from', '$to', '$total', 0, NOW())";
        
        error_log("Insert SQL: " . $sql);
        
        if($conn->query($sql)) {
            $pid = $conn->insert_id;
            
            // Insert parcel items
            if(isset($_POST['weight']) && is_array($_POST['weight'])) {
                for($i = 0; $i < count($_POST['weight']); $i++) {
                    if(!empty($_POST['weight'][$i])) {
                        $w = $_POST['weight'][$i];
                        $h = $_POST['height'][$i];
                        $l = $_POST['length'][$i];
                        $wd = $_POST['width'][$i];
                        $p = $_POST['price'][$i];
                        
                        $item_sql = "INSERT INTO parcel_items (parcel_id, weight, height, length, width, price) 
                                     VALUES ('$pid', '$w', '$h', '$l', '$wd', '$p')";
                        $conn->query($item_sql);
                    }
                }
            }
            
            // Insert initial tracking
            $conn->query("INSERT INTO parcel_tracking (parcel_id, status, date_created) VALUES ('$pid', 0, NOW())");
            
            echo "1";
        } else {
            error_log("Insert Error: " . $conn->error);
            echo "0 - " . $conn->error;
        }
    } else {
        // Update existing parcel
        $sql = "UPDATE parcels SET 
                customer_id = $customer,
                sender_name = '$sname',
                sender_address = '$saddr',
                sender_contact = '$scont',
                recipient_name = '$rname',
                recipient_address = '$raddr',
                recipient_contact = '$rcont',
                type = '$type',
                from_branch_id = '$from',
                to_branch_id = '$to',
                total = '$total'
                WHERE id = $id";
        
        error_log("Update SQL: " . $sql);
        
        if($conn->query($sql)) {
            // Delete old items
            $conn->query("DELETE FROM parcel_items WHERE parcel_id = $id");
            
            // Insert new items
            if(isset($_POST['weight']) && is_array($_POST['weight'])) {
                for($i = 0; $i < count($_POST['weight']); $i++) {
                    if(!empty($_POST['weight'][$i])) {
                        $w = $_POST['weight'][$i];
                        $h = $_POST['height'][$i];
                        $l = $_POST['length'][$i];
                        $wd = $_POST['width'][$i];
                        $p = $_POST['price'][$i];
                        
                        $item_sql = "INSERT INTO parcel_items (parcel_id, weight, height, length, width, price) 
                                     VALUES ('$id', '$w', '$h', '$l', '$wd', '$p')";
                        $conn->query($item_sql);
                    }
                }
            }
            echo "1";
        } else {
            error_log("Update Error: " . $conn->error);
            echo "0 - " . $conn->error;
        }
    }
    exit;
}

// UPDATE PARCEL STATUS
if($action == 'update_status') {
    $id = $_POST['id'];
    $status = $_POST['status'];
    
    // Update parcel status
    $conn->query("UPDATE parcels SET status = '$status' WHERE id = $id");
    
    // Add to tracking history
    $conn->query("INSERT INTO parcel_tracking (parcel_id, status, date_created) VALUES ('$id', '$status', NOW())");
    
    echo "1";
    exit;
}

// ============================================
// TRACKING & REPORTS
// ============================================

if($action == 'get_parcel_history') {
    $ref = $_POST['ref_no'];
    $qry = $conn->query("SELECT * FROM parcels WHERE reference_number='$ref'");
    if($qry->num_rows == 0) { echo "2"; exit; }
    
    $parcel = $qry->fetch_assoc();
    $track = $conn->query("SELECT * FROM parcel_tracking WHERE parcel_id=".$parcel['id']." ORDER BY date_created DESC");
    
    $labels = ['Accepted','Collected','Shipped','In-Transit','Arrived','Out for Delivery','Ready','Delivered','Picked','Failed'];
    $data = [];
    while($row = $track->fetch_assoc()) {
        $data[] = ['status'=>$labels[$row['status']], 'date_created'=>$row['date_created']];
    }
    echo json_encode($data);
    exit;
}

if($action == 'get_report') {
    $from = $_POST['date_from'].' 00:00:00';
    $to = $_POST['date_to'].' 23:59:59';
    $status = $_POST['status'];
    
    $where = "WHERE date_created BETWEEN '$from' AND '$to'";
    if($status != 'all') $where .= " AND status='$status'";
    
    $qry = $conn->query("SELECT * FROM parcels $where ORDER BY date_created DESC");
    
    $labels = ['Accepted','Collected','Shipped','In-Transit','Arrived','Out for Delivery','Ready','Delivered','Picked','Failed'];
    $data = [];
    while($row = $qry->fetch_assoc()) {
        $data[] = [
            'date_created'=>date('Y-m-d',strtotime($row['date_created'])),
            'reference_number'=>$row['reference_number'],
            'sender_name'=>$row['sender_name'],
            'recipient_name'=>$row['recipient_name'],
            'type'=>$row['type'],
            'total'=>$row['total'],
            'status_label'=>$labels[$row['status']]
        ];
    }
    echo json_encode($data);
    exit;
}
?>