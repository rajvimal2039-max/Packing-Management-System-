<?php
require_once 'db_connect.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Track Parcel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <style>
        .track-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .search-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .timeline {
            position: relative;
            padding-left: 30px;
            margin-top: 30px;
        }
        .timeline-item {
            position: relative;
            padding-bottom: 25px;
        }
        .timeline-badge {
            position: absolute;
            left: -30px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #007bff;
            border: 3px solid #fff;
            box-shadow: 0 0 0 2px #007bff;
            z-index: 2;
        }
        .timeline-item:before {
            content: '';
            position: absolute;
            left: -22px;
            top: 18px;
            bottom: -5px;
            width: 2px;
            background: #dee2e6;
            z-index: 1;
        }
        .timeline-item:last-child:before {
            display: none;
        }
        .timeline-date {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 3px;
        }
        .timeline-status {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }
        .badge-accepted { background: #6c757d; color: white; padding: 5px 10px; border-radius: 20px; }
        .badge-collected { background: #17a2b8; color: white; padding: 5px 10px; border-radius: 20px; }
        .badge-shipped { background: #007bff; color: white; padding: 5px 10px; border-radius: 20px; }
        .badge-transit { background: #007bff; color: white; padding: 5px 10px; border-radius: 20px; }
        .badge-arrived { background: #17a2b8; color: white; padding: 5px 10px; border-radius: 20px; }
        .badge-outfordelivery { background: #ffc107; color: #333; padding: 5px 10px; border-radius: 20px; }
        .badge-ready { background: #007bff; color: white; padding: 5px 10px; border-radius: 20px; }
        .badge-delivered { background: #28a745; color: white; padding: 5px 10px; border-radius: 20px; }
        .badge-picked { background: #28a745; color: white; padding: 5px 10px; border-radius: 20px; }
        .badge-failed { background: #dc3545; color: white; padding: 5px 10px; border-radius: 20px; }
    </style>
</head>
<body>
    <div class="track-container">
        <h3 class="text-center mb-4"><i class="fa fa-search"></i> Track Your Parcel</h3>
        
        <div class="search-box">
            <div class="input-group">
                <input type="text" id="ref_no" class="form-control form-control-lg" 
                       placeholder="Enter tracking number (e.g., CMP2025001)">
                <div class="input-group-append">
                    <button class="btn btn-primary btn-lg" id="trackBtn">
                        <i class="fa fa-search"></i> Track
                    </button>
                </div>
            </div>
            <div class="text-center mt-2">
                <small class="text-muted">Try: CMP2025001 or CMP2025002</small>
            </div>
        </div>
        
        <div id="result" style="display: none;">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Tracking History</h5>
                </div>
                <div class="card-body">
                    <div id="parcelInfo" class="alert alert-info" style="display: none;"></div>
                    <div class="timeline" id="timeline"></div>
                    <div id="noData" class="text-center text-muted" style="display: none;">
                        <i class="fa fa-box-open fa-3x"></i>
                        <p class="mt-2">No tracking history available</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        $('#trackBtn').click(function() {
            trackParcel();
        });
        
        $('#ref_no').keypress(function(e) {
            if(e.which == 13) {
                trackParcel();
            }
        });
        
        function trackParcel() {
            var ref = $('#ref_no').val().trim();
            
            if(ref == '') {
                alert('Please enter a tracking number');
                return;
            }
            
            // Show loading
            $('#trackBtn').html('<i class="fa fa-spinner fa-spin"></i> Tracking...').prop('disabled', true);
            $('#result').hide();
            
            $.ajax({
                url: 'ajax.php?action=get_parcel_history',
                method: 'POST',
                data: { ref_no: ref },
                dataType: 'json',
                success: function(response) {
                    $('#trackBtn').html('<i class="fa fa-search"></i> Track').prop('disabled', false);
                    
                    console.log('Response:', response);
                    
                    if(response == 2 || response === 2) {
                        alert('Tracking number not found');
                        return;
                    }
                    
                    if(response && response.length > 0) {
                        displayTracking(response);
                    } else {
                        $('#result').show();
                        $('#timeline').empty();
                        $('#parcelInfo').hide();
                        $('#noData').show();
                    }
                },
                error: function(xhr, status, error) {
                    $('#trackBtn').html('<i class="fa fa-search"></i> Track').prop('disabled', false);
                    console.log('Error:', error);
                    console.log('Response:', xhr.responseText);
                    alert('Error tracking parcel. Please try again.');
                }
            });
        }
        
        function displayTracking(history) {
            $('#result').show();
            $('#noData').hide();
            
            // Show current status
            var current = history[0];
            $('#parcelInfo').html('<strong>Current Status:</strong> ' + current.status + 
                                 ' <span class="float-right">' + current.date_created + '</span>')
                          .show();
            
            // Build timeline
            var html = '';
            for(var i = 0; i < history.length; i++) {
                var item = history[i];
                var statusClass = getStatusClass(item.status);
                
                html += '<div class="timeline-item">' +
                        '<div class="timeline-badge" style="background: ' + getStatusColor(item.status) + ';"></div>' +
                        '<div class="timeline-date">' + item.date_created + '</div>' +
                        '<div class="timeline-status"><span class="' + statusClass + '">' + item.status + '</span></div>' +
                        '</div>';
            }
            
            $('#timeline').html(html);
        }
        
        function getStatusClass(status) {
            var classes = {
                'Accepted': 'badge-accepted',
                'Collected': 'badge-collected',
                'Shipped': 'badge-shipped',
                'In-Transit': 'badge-transit',
                'Arrived': 'badge-arrived',
                'Out for Delivery': 'badge-outfordelivery',
                'Ready to Pickup': 'badge-ready',
                'Delivered': 'badge-delivered',
                'Picked-up': 'badge-picked',
                'Failed': 'badge-failed'
            };
            return classes[status] || 'badge-secondary';
        }
        
        function getStatusColor(status) {
            var colors = {
                'Accepted': '#6c757d',
                'Collected': '#17a2b8',
                'Shipped': '#007bff',
                'In-Transit': '#007bff',
                'Arrived': '#17a2b8',
                'Out for Delivery': '#ffc107',
                'Ready to Pickup': '#007bff',
                'Delivered': '#28a745',
                'Picked-up': '#28a745',
                'Failed': '#dc3545'
            };
            return colors[status] || '#6c757d';
        }
    });
    </script>
</body>
</html>