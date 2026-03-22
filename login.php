<?php
session_start();
if(isset($_SESSION['login_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packing Management System - Login</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            min-height: 100vh;
            background: #f4f6f9;
            display: flex;
        }

        /* Split Screen Container */
        .split-container {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* Left Side - Image/Branding */
        .left-side {
            flex: 1;
            background: linear-gradient(135deg, #e8f0fe 0%, #d9e6f2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Pattern Overlay */
        .left-side::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect width="100" height="100" fill="none"/><circle cx="50" cy="50" r="40" stroke="%23007bff" stroke-width="0.5" fill="none" opacity="0.1"/><circle cx="50" cy="50" r="30" stroke="%23007bff" stroke-width="0.5" fill="none" opacity="0.1"/><circle cx="50" cy="50" r="20" stroke="%23007bff" stroke-width="0.5" fill="none" opacity="0.1"/></svg>');
            background-repeat: repeat;
            opacity: 0.5;
            animation: move 60s linear infinite;
        }

        @keyframes move {
            from { background-position: 0 0; }
            to { background-position: 100% 100%; }
        }

        .brand-content {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 40px;
            max-width: 500px;
        }

        .brand-logo {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #007bff, #00bcd4);
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            box-shadow: 0 20px 40px -10px rgba(0, 123, 255, 0.3);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .brand-logo i {
            font-size: 60px;
            color: white;
        }

        .brand-content h1 {
            font-size: 48px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 15px;
            line-height: 1.2;
        }

        .brand-content h1 .line1 {
            display: block;
            font-size: 48px;
            font-weight: 700;
        }

        .brand-content h1 .line2 {
            display: block;
            font-size: 48px;
            font-weight: 700;
            margin-top: -5px;
        }

        .brand-content p {
            color: #475569;
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 40px;
        }

        .feature-list {
            text-align: left;
            margin-top: 50px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            color: #334155;
            font-size: 16px;
        }

        .feature-item i {
            width: 30px;
            height: 30px;
            background: rgba(0, 123, 255, 0.1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #007bff;
            font-size: 16px;
        }

        /* Right Side - Login Form */
        .right-side {
            flex: 1;
            background: #f4f6f9;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
        }

        .login-card {
            background: white;
            border-radius: 30px;
            padding: 50px 40px;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-header h2 {
            font-size: 32px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 10px;
        }

        .login-header p {
            color: #64748b;
            font-size: 14px;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            color: #334155;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 18px;
            transition: color 0.3s;
        }

        .input-wrapper input {
            width: 100%;
            padding: 16px 16px 16px 50px;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            font-size: 15px;
            color: #1e293b;
            transition: all 0.3s;
        }

        .input-wrapper input:focus {
            outline: none;
            border-color: #007bff;
            background: white;
        }

        .input-wrapper input:focus + i {
            color: #007bff;
        }

        .input-wrapper input::placeholder {
            color: #94a3b8;
        }

        /* Options */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0 30px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            font-size: 14px;
            cursor: pointer;
        }

        .remember input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #007bff;
        }

        .forgot {
            color: #64748b;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
        }

        .forgot:hover {
            color: #007bff;
        }

        /* Login Button */
        .login-btn {
            width: 100%;
            padding: 16px;
            background: #007bff;
            border: none;
            border-radius: 16px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 10px 20px -5px rgba(0, 123, 255, 0.3);
        }

        .login-btn:hover {
            background: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 20px 30px -5px rgba(0, 123, 255, 0.4);
        }

        .login-btn i {
            font-size: 16px;
            transition: transform 0.3s;
        }

        .login-btn:hover i {
            transform: translateX(5px);
        }

        /* Loading State */
        .login-btn.loading {
            opacity: 0.8;
            cursor: not-allowed;
        }

        .spinner {
            display: none;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .login-btn.loading .spinner {
            display: inline-block;
        }

        .login-btn.loading .btn-text {
            display: none;
        }

        /* Error Message */
        .error-message {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 12px;
            border-radius: 12px;
            margin-top: 20px;
            display: none;
            align-items: center;
            gap: 10px;
            font-size: 14px;
        }

        .error-message.show {
            display: flex;
        }

        /* Responsive */
        @media (max-width: 968px) {
            .split-container {
                flex-direction: column;
            }
            
            .left-side {
                padding: 60px 20px;
            }
            
            .brand-content h1 .line1,
            .brand-content h1 .line2 {
                font-size: 36px;
            }
        }
    </style>
</head>
<body>
    <div class="split-container">
        <!-- Left Side - Branding with Image -->
        <div class="left-side">
            <div class="brand-content">
                <div class="brand-logo">
                    <i class="fas fa-box"></i>
                </div>
                <h1>
                    <span class="line1">PACKING MANAGEMENT</span>
                    <span class="line2">SYSTEM</span>
                </h1>
                <p>Streamline your parcel operations with intelligent tracking and real-time updates</p>
                
                <div class="feature-list">
                    <div class="feature-item">
                        <i class="fas fa-check"></i>
                        <span>Real-time parcel tracking</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check"></i>
                        <span>Multi-branch management</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check"></i>
                        <span>Automated notifications</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check"></i>
                        <span>Comprehensive reporting</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Login Form -->
        <div class="right-side">
            <div class="login-container">
                <div class="login-card">
                    <div class="login-header">
                        <h2>Welcome Back</h2>
                        <p>Sign in to access your dashboard</p>
                    </div>
                    
                    <form id="login-form" autocomplete="off">
                        <div class="form-group">
                            <label>Email Address</label>
                            <div class="input-wrapper">
                                <i class="fas fa-envelope"></i>
                                <input type="email" name="email" placeholder="Enter your email" required autofocus>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Password</label>
                            <div class="input-wrapper">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="password" placeholder="Enter your password" required>
                            </div>
                        </div>
                        
                        <div class="form-options">
                            <label class="remember">
                                <input type="checkbox" id="remember">
                                <span>Remember me</span>
                            </label>
                            <a href="#" class="forgot">Forgot password?</a>
                        </div>
                        
                        <button type="submit" class="login-btn" id="loginBtn">
                            <span class="btn-text">Sign In</span>
                            <span class="spinner"></span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                        
                        <div class="error-message" id="errorMessage">
                            <i class="fas fa-exclamation-circle"></i>
                            <span id="errorText"></span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Form submission
            $('#login-form').submit(function(e) {
                e.preventDefault();
                
                var btn = $('#loginBtn');
                var errorMsg = $('#errorMessage');
                var errorText = $('#errorText');
                
                btn.addClass('loading').prop('disabled', true);
                errorMsg.removeClass('show');
                
                $.ajax({
                    url: 'ajax.php?action=login',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(resp) {
                        if(resp == 1) {
                            // Success - redirect to dashboard
                            window.location.href = 'index.php?page=home';
                        } else {
                            // Error
                            btn.removeClass('loading').prop('disabled', false);
                            errorText.text('Invalid email or password');
                            errorMsg.addClass('show');
                        }
                    },
                    error: function() {
                        btn.removeClass('loading').prop('disabled', false);
                        errorText.text('Connection error. Please try again.');
                        errorMsg.addClass('show');
                    }
                });
            });
            
            // Remember me functionality
            if(localStorage.getItem('rememberedEmail')) {
                $('input[name="email"]').val(localStorage.getItem('rememberedEmail'));
                $('#remember').prop('checked', true);
            }
            
            $('#remember').change(function() {
                if($(this).is(':checked')) {
                    localStorage.setItem('rememberedEmail', $('input[name="email"]').val());
                } else {
                    localStorage.removeItem('rememberedEmail');
                }
            });
        });
    </script>
</body>
</html>