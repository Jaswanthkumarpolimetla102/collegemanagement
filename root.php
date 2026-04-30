<?php
session_start();
include "config.php";

$error = "";

if(isset($_POST['login'])){

    $role = $_POST['role'];
    $user = trim($_POST['username']);
    $pass = trim($_POST['password']);

    // ================= EMPLOYEE =================
    if($role == "employee"){

        $stmt = $conn->prepare("SELECT emp_id, username, password FROM employee WHERE username=?");
        $stmt->bind_param("s",$user);
        $stmt->execute();
        $res = $stmt->get_result();

        if($res->num_rows == 1){

            $row = $res->fetch_assoc();

            // Plain text compare
            if($pass == $row['password']){

                $_SESSION['employee_logged_in'] = true;
                $_SESSION['employee_id'] = $row['emp_id'];
                $_SESSION['employee_username'] = $row['username'];

                header("Location: employee/dashboard.php");
                exit();

            } else {
                $error = "Wrong Password";
            }

        } else {
            $error = "Username not found";
        }
    }

    // ================= STUDENT =================
    elseif($role == "student"){

        $stmt = $conn->prepare("SELECT * FROM students WHERE email=?");
        $stmt->bind_param("s",$user);
        $stmt->execute();
        $res = $stmt->get_result();

        if($res->num_rows == 1){

            $s = $res->fetch_assoc();

            if(password_verify($pass,$s['password'])){

                $_SESSION['student_logged_in'] = true;
                $_SESSION['student'] = $s['id'];

                header("Location: student/dashboard.php");
                exit();

            } else {
                $error = "Wrong Password";
            }

        } else {
            $error = "Email Not Found";
        }
    }

    // ================= ADMIN =================
    elseif($role == "admin"){

        $stmt = $conn->prepare("SELECT * FROM admin WHERE username=?");
        $stmt->bind_param("s",$user);
        $stmt->execute();
        $res = $stmt->get_result();

        if($res->num_rows == 1){

            $a = $res->fetch_assoc();

            // Plain text compare
            if($pass == $a['password']){

                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin'] = $a['id'];

                header("Location: admin/dashboard.php");
                exit();

            } else {
                $error = "Wrong Password";
            }

        } else {
            $error = "Admin Not Found";
        }
    }
    
    // ================= NEW: HOD =================
    elseif($role == "hod"){

        $stmt = $conn->prepare("SELECT * FROM hod WHERE username=? OR email=?");
        $stmt->bind_param("ss",$user, $user);
        $stmt->execute();
        $res = $stmt->get_result();

        if($res->num_rows == 1){

            $hod = $res->fetch_assoc();

            // Plain text compare (you can change to password_verify if using hashed passwords)
            if($pass == $hod['password']){

                $_SESSION['hod_logged_in'] = true;
                $_SESSION['hod_id'] = $hod['hod_id'];
                $_SESSION['hod_name'] = $hod['name'];
                $_SESSION['hod_department'] = $hod['department'];
                $_SESSION['hod_db_id'] = $hod['id'];

                header("Location: hod/dashboard.php");
                exit();

            } else {
                $error = "Wrong Password";
            }

        } else {
            $error = "HOD Not Found";
        }
    }
}
?>
<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <title>A.A.N.M & V.V.R.S.R. POLYTECHNIC | Login</title> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=yes">
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Space Grotesk', sans-serif;
        }

        body {
            min-height: 100vh;
            background: #0a0f1e;
            position: relative;
            overflow: hidden;
        }

        /* Abstract Background */
        .background {
            position: fixed;
            width: 100vw;
            height: 100vh;
            top: 0;
            left: 0;
            z-index: 0;
            overflow: hidden;
        }

        .gradient-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.4;
            animation: orbFloat 20s ease-in-out infinite;
        }

        .orb-1 {
            width: 600px;
            height: 600px;
            background: linear-gradient(135deg, #4158D0, #C850C0);
            top: -200px;
            right: -200px;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 500px;
            height: 500px;
            background: linear-gradient(135deg, #FFCC70, #FF8C42);
            bottom: -150px;
            left: -150px;
            animation-delay: -5s;
        }

        .orb-3 {
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, #12c2e9, #c471ed);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: -10s;
        }

        @keyframes orbFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(50px, -30px) scale(1.1); }
            66% { transform: translate(-30px, 50px) scale(0.9); }
        }

        /* Grid Pattern */
        .grid-pattern {
            position: fixed;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            z-index: 1;
            animation: gridMove 20s linear infinite;
        }

        @keyframes gridMove {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        /* Main Container */
        .container {
            position: relative;
            z-index: 2;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Login Card */
        .login-card {
            width: 100%;
            max-width: 450px;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 40px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: cardAppear 0.8s cubic-bezier(0.23, 1, 0.32, 1);
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #4158D0, #C850C0, #FFCC70);
        }

        @keyframes cardAppear {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Logo/Brand Section */
        .brand {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }

        .logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #4158D0, #C850C0);
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2.5rem;
            color: white;
            transform: rotate(-5deg);
            transition: all 0.5s ease;
            box-shadow: 0 20px 30px -10px rgba(193, 80, 192, 0.5);
        }

        .logo-icon:hover {
            transform: rotate(0deg) scale(1.05);
        }

        .brand h1 {
            color: white;
            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 5px;
        }

        .brand p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }

        .brand span {
            background: linear-gradient(135deg, #FFCC70, #FF8C42);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 700;
        }

        /* Role Selector - Updated for 4 roles */
        .role-selector {
            display: flex;
            gap: 5px;
            margin-bottom: 30px;
            background: rgba(255, 255, 255, 0.03);
            padding: 5px;
            border-radius: 60px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            flex-wrap: wrap;
        }

        .role-option {
            flex: 1 1 auto;
            text-align: center;
            padding: 10px 8px;
            border-radius: 50px;
            color: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.85rem;
            min-width: 90px;
        }

        .role-option i {
            margin-right: 4px;
            font-size: 0.9rem;
        }

        .role-option.active {
            background: linear-gradient(135deg, #4158D0, #C850C0);
            color: white;
            box-shadow: 0 10px 20px -10px rgba(65, 88, 208, 0.5);
        }

        .role-option:hover:not(.active) {
            color: white;
            background: rgba(255, 255, 255, 0.05);
        }

        /* Form Group */
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.4);
            font-size: 1.1rem;
            transition: all 0.3s ease;
            z-index: 1;
        }

        .input-wrapper input,
        .input-wrapper select {
            width: 100%;
            padding: 16px 16px 16px 48px;
            background: rgba(255, 255, 255, 0.03);
            border: 2px solid rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
            outline: none;
        }

        .input-wrapper select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='rgba(255,255,255,0.4)' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 20px;
        }

        .input-wrapper input:focus,
        .input-wrapper select:focus {
            border-color: #C850C0;
            background: rgba(255, 255, 255, 0.05);
            box-shadow: 0 0 30px rgba(200, 80, 192, 0.2);
        }

        .input-wrapper input:focus + i,
        .input-wrapper select:focus + i {
            color: #C850C0;
        }

        /* Password Toggle */
        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.4);
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 2;
        }

        .password-toggle:hover {
            color: #C850C0;
        }

        /* Options Row */
        .options-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
        }

        .remember-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            cursor: pointer;
        }

        .remember-checkbox input {
            display: none;
        }

        .checkbox-custom {
            width: 18px;
            height: 18px;
            background: rgba(255, 255, 255, 0.03);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 6px;
            display: inline-block;
            position: relative;
            transition: all 0.3s ease;
        }

        .remember-checkbox input:checked + .checkbox-custom {
            background: linear-gradient(135deg, #4158D0, #C850C0);
            border-color: transparent;
        }

        .remember-checkbox input:checked + .checkbox-custom::after {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 11px;
            color: white;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .forgot-link {
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .forgot-link:hover {
            color: #FFCC70;
        }

        /* Login Button */
        .login-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #4158D0, #C850C0);
            border: none;
            border-radius: 60px;
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 20px -10px rgba(65, 88, 208, 0.5);
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .login-btn:hover {
            transform: scale(1.02);
            box-shadow: 0 20px 30px -10px rgba(200, 80, 192, 0.7);
        }

        .login-btn:hover::before {
            left: 100%;
        }

        /* Back Button */
        .back-btn {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            padding: 10px;
            border-radius: 50px;
        }

        .back-btn:hover {
            color: white;
            background: rgba(255, 255, 255, 0.03);
        }

        .back-btn i {
            margin-right: 6px;
            transition: transform 0.3s ease;
        }

        .back-btn:hover i {
            transform: translateX(-3px);
        }

        /* Error Alert */
        .error-alert {
            background: rgba(255, 70, 70, 0.1);
            border: 2px solid rgba(255, 70, 70, 0.3);
            border-radius: 20px;
            padding: 15px 20px;
            margin-bottom: 25px;
            color: #ff6b6b;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: shake 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
            backdrop-filter: blur(10px);
        }

        @keyframes shake {
            10%, 90% { transform: translateX(-1px); }
            20%, 80% { transform: translateX(2px); }
            30%, 50%, 70% { transform: translateX(-4px); }
            40%, 60% { transform: translateX(4px); }
        }

        .error-alert i {
            font-size: 1.2rem;
        }

        /* Loading State */
        .login-btn.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .login-btn.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top-color: white;
            border-radius: 50%;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: translateY(-50%) rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-card {
                padding: 30px 20px;
            }
            
            .role-option {
                padding: 8px 4px;
                font-size: 0.75rem;
            }
            
            .role-option i {
                margin-right: 2px;
                display: block;
                margin-bottom: 2px;
            }
        }

        /* Stats Strip */
        .stats-strip {
            display: flex;
            justify-content: space-around;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            color: white;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>
</head> 
<body>
    <!-- Abstract Background -->
    <div class="background">
        <div class="gradient-orb orb-1"></div>
        <div class="gradient-orb orb-2"></div>
        <div class="gradient-orb orb-3"></div>
        <div class="grid-pattern"></div>
    </div>

    <!-- Main Container -->
    <div class="container">
        <div class="login-card">
            <!-- Brand Section -->
            <div class="brand">
                <div class="logo-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h1>A.A.N.M & V.V.R.S.R.</h1>
                <p>POLYTECHNIC <span>GUDLAVALLERU</span></p>
            </div>

            <!-- Error Alert -->
            <?php if($error!=""){ ?> 
                <div class="error-alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php } ?>

            <!-- Login Form -->
            <form method="POST" id="loginForm">
                <!-- Hidden Role Input -->
                <input type="hidden" name="role" id="selectedRole" value="">
                
                <!-- Role Selector (Visual) - Updated with HOD -->
                <div class="role-selector">
                    <div class="role-option" data-role="admin" onclick="selectRole('admin')">
                        <i class="fas fa-crown"></i>
                        <span>Admin</span>
                    </div>
                    <div class="role-option" data-role="hod" onclick="selectRole('hod')">
                        <i class="fas fa-user-tie"></i>
                        <span>HOD</span>
                    </div>
                    <div class="role-option" data-role="student" onclick="selectRole('student')">
                        <i class="fas fa-user-graduate"></i>
                        <span>Student</span>
                    </div>
                    <div class="role-option" data-role="employee" onclick="selectRole('employee')">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Employee</span>
                    </div>
                </div>

                <!-- Username Field -->
                <div class="form-group">
                    <label>Email / Username</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="text" name="username" placeholder="Enter your email/username" required>
                    </div>
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                                                                                                                        <i class="fas fa-eye-slash  password-toggle" id="togglePassword" onclick="togglePassword()"></i>
                    </div>
                </div>

                <!-- Options Row -->
                <div class="options-row">
                    <label class="remember-checkbox">
                        <input type="checkbox" id="remember">
                        <span class="checkbox-custom"></span>
                        <span>Remember me</span>
                    </label>
                    <a href="#" class="forgot-link">Forgot Password?</a>
                </div>

                <!-- Login Button -->
                <button type="submit" name="login" class="login-btn" id="loginBtn">
                    <span>Access Dashboard</span>
                    <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
                </button>

                <!-- Back Link -->
                <a href="index.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back to Home
                </a>
            </form>

            <!-- Stats Strip -->
            <div class="stats-strip">
                <div class="stat-item">
                    <div class="stat-number">45+</div>
                    <div class="stat-label">Years</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">3000+</div>
                    <div class="stat-label">Students</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">200+</div>
                    <div class="stat-label">Staff</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Select Role Function
        function selectRole(role) {
            // Update hidden input
            document.getElementById('selectedRole').value = role;
            
            // Update visual selector
            document.querySelectorAll('.role-option').forEach(option => {
                option.classList.remove('active');
            });
            document.querySelector(`.role-option[data-role="${role}"]`).classList.add('active');
        }

        // Select default role (Admin)
        selectRole('admin');

        // Toggle Password Visibility
        function togglePassword() {
            const password = document.getElementById('password');
            const toggle = document.getElementById('togglePassword');
            
            if (password.type === 'password') {
                password.type = 'text';
                toggle.classList.remove('fa-eye');
                toggle.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                toggle.classList.remove('fa-eye-slash');
                toggle.classList.add('fa-eye');
            }
        }

        // Form Submission with Loading State
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const role = document.getElementById('selectedRole').value;
            const username = document.querySelector('input[name="username"]').value;
            const password = document.querySelector('input[name="password"]').value;
            
            if (!role || !username || !password) {
                e.preventDefault();
                alert('Please select a role and fill in all fields');
                return;
            }
            
            // Show loading state
            const btn = document.getElementById('loginBtn');
            btn.classList.add('loading');
            btn.querySelector('span').textContent = 'Logging in...';
        });

        // Input animations
        document.querySelectorAll('.input-wrapper input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.querySelector('i:first-child').style.color = '#C850C0';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.querySelector('i:first-child').style.color = 'rgba(255, 255, 255, 0.4)';
            });
        });

        // Smooth page load
        document.addEventListener('DOMContentLoaded', function() {
            document.body.style.opacity = '1';
            
            // Add floating animation to stats
            const stats = document.querySelectorAll('.stat-item');
            stats.forEach((stat, index) => {
                stat.style.animation = `float 3s ease-in-out ${index * 0.2}s infinite`;
            });
        });

        // Additional floating animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes float {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-5px); }
            }
        `;
        document.head.appendChild(style);

        // Prevent form resubmission on page refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body> 
<!-- Footer with Developer Info -->
        <div style="margin-top: 2rem; text-align: center;">
            <div style="display: inline-block; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2); border-radius: 50px; padding: 0.5rem 1.5rem;">
                <span style="color: white; opacity: 0.7; font-size: 0.9rem;">
                    <i class="fas fa-code me-2"></i>
                    Developed with 
                    <i class="fas fa-heart" style="color: #ff6b6b; margin: 0 4px;"></i> 
                    by 
                    <button onclick="openDeveloperModal()" style="background: none; border: none; color: #ffd93d; font-weight: 600; cursor: pointer; text-decoration: underline; text-underline-offset: 3px;">
                        23Batch Aiml 3BStudents
                    </button>
                    <i class="fas fa-mug-hot ms-2" style="color: #ffd93d;"></i>
                </span>
            </div>
        </div>
    </div> <!-- Close dashboard div -->

</html>