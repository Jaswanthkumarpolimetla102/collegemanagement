<?php
session_start();
include "../config.php";

// ✅ Check employee login session
if (!isset($_SESSION['employee_logged_in'])) {
    header("Location: login.php");
    exit();
}

// ✅ Get employee id
$emp_id = $_SESSION['employee_id'];

// ✅ Fetch employee details
$emp_query = $conn->query("SELECT * FROM employee WHERE emp_id = '$emp_id'");
$emp = $emp_query->fetch_assoc();
$employee_department = $emp['department'];

// ✅ Fetch HOD assigned subjects for this employee
$assigned_subjects = $conn->query("
    SELECT es.*, s.subject_name, s.semester 
    FROM employee_subjects es
    JOIN subjects s ON es.subject_code = s.subject_code
    WHERE es.employee_id = '$emp_id' AND es.department = '$employee_department'
    ORDER BY s.semester, es.section
");

// ✅ Fetch HOD assigned timetable for this employee
$employee_timetable = $conn->query("
    SELECT t.*, s.subject_name, 
           CASE 
               WHEN t.day_of_week = 'Monday' THEN 1
               WHEN t.day_of_week = 'Tuesday' THEN 2
               WHEN t.day_of_week = 'Wednesday' THEN 3
               WHEN t.day_of_week = 'Thursday' THEN 4
               WHEN t.day_of_week = 'Friday' THEN 5
               WHEN t.day_of_week = 'Saturday' THEN 6
               ELSE 7
           END as day_order
    FROM timetable t
    JOIN subjects s ON t.subject_code = s.subject_code
    WHERE t.employee_id = '$emp_id' AND t.department = '$employee_department'
    ORDER BY day_order, t.period
");

// ✅ Get unique sections for this employee
$sections = $conn->query("
    SELECT DISTINCT section FROM employee_subjects 
    WHERE employee_id = '$emp_id' AND department = '$employee_department'
    ORDER BY section
");

// ✅ Dashboard statistics
$students_query = $conn->query("SELECT COUNT(*) AS total_students FROM students WHERE department = '$employee_department'");
$students = $students_query->fetch_assoc()['total_students'];

// ✅ ==================== ATTENDANCE MARKING ====================
$show_students = false;
$attendance_saved = false;
$already_marked = false;
$present = 0;
$absent = 0;
$attendance_error = "";
$students_list = null;

// Check session for attendance data
if(isset($_SESSION['attendance_subject']) && isset($_SESSION['attendance_section']) && isset($_SESSION['attendance_date'])) {
    $show_students = true;
    $students_list = $conn->query("SELECT * FROM students WHERE department='$employee_department' AND section='{$_SESSION['attendance_section']}' ORDER BY roll_no");
}

// Load students for attendance
if(isset($_POST['load_attendance_students'])){
    $subject_code = $_POST['subject_code'];
    $section = $_POST['section'];
    $date = $_POST['attendance_date'];
    
    // Verify if employee is assigned to this subject
    $check_assigned = $conn->query("SELECT * FROM employee_subjects 
                                    WHERE employee_id = '$emp_id' 
                                    AND subject_code = '$subject_code' 
                                    AND section = '$section'");
    
    if($check_assigned->num_rows > 0) {
        // Check if there are students in this section
        $student_check = $conn->query("SELECT COUNT(*) as count FROM students WHERE department='$employee_department' AND section='$section'");
        $student_count = $student_check->fetch_assoc()['count'];
        
        if($student_count > 0) {
            $_SESSION['attendance_subject'] = $subject_code;
            $_SESSION['attendance_section'] = $section;
            $_SESSION['attendance_date'] = $date;
            
            // Refresh the page to show the attendance form
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        } else {
            $attendance_error = "No students found in this section!";
        }
    } else {
        $attendance_error = "You are not assigned to this subject/section!";
    }
}

// Submit attendance
if(isset($_POST['submit_attendance'])){
    $subject_code = $_POST['subject_code'];
    $section = $_POST['section'];
    $date = $_POST['attendance_date'];
    
    // Check if attendance already marked for this date
    $check = $conn->prepare("SELECT id FROM attendance 
        WHERE student_id=? AND subject_code=? AND attendance_date=?");
    
    $attendance_marked = false;
    $marked_count = 0;
    
    if(isset($_POST['attendance']) && is_array($_POST['attendance'])){
        foreach($_POST['attendance'] as $student_id => $status){
            $check->bind_param("iss", $student_id, $subject_code, $date);
            $check->execute();
            $check->store_result();
            
            if($check->num_rows > 0){
                $attendance_marked = true;
                $marked_count++;
            }
        }
        
        if($attendance_marked){
            $already_marked = true;
            $attendance_error = "$marked_count students already have attendance marked for this date!";
        } else {
            foreach($_POST['attendance'] as $student_id => $status){
                if($status=="Present") $present++;
                else $absent++;

                $stmt = $conn->prepare("INSERT INTO attendance
                (student_id, subject_code, section, attendance_date, status)
                VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("issss", $student_id, $subject_code, $section, $date, $status);
                $stmt->execute();
            }
            $attendance_saved = true;
            
            // Clear session
            unset($_SESSION['attendance_subject']);
            unset($_SESSION['attendance_section']);
            unset($_SESSION['attendance_date']);
            
            // Redirect with success message
            header("Location: ".$_SERVER['PHP_SELF']."?attendance_success=1&present=$present&absent=$absent");
            exit();
        }
    }
}

// ✅ ==================== INTERNAL MARKS WITH COMPONENTS ====================
$marks_success = "";
$marks_error = "";
$show_marks_students = false;
$marks_students_list = null;

// Check session for marks data
if(isset($_SESSION['marks_subject']) && isset($_SESSION['marks_section']) && isset($_SESSION['marks_exam_type'])) {
    $show_marks_students = true;
    $marks_students_list = $conn->query("SELECT * FROM students WHERE department='$employee_department' AND section='{$_SESSION['marks_section']}' ORDER BY roll_no");
}

// Load students for marks
if(isset($_POST['load_marks_students'])){
    $subject_code = $_POST['subject_code'];
    $section = $_POST['section'];
    $exam_type = $_POST['exam_type'];
    
    // Verify if employee is assigned to this subject
    $check_assigned = $conn->query("SELECT * FROM employee_subjects 
                                    WHERE employee_id = '$emp_id' 
                                    AND subject_code = '$subject_code' 
                                    AND section = '$section'");
    
    if($check_assigned->num_rows > 0) {
        // Check if there are students in this section
        $student_check = $conn->query("SELECT COUNT(*) as count FROM students WHERE department='$employee_department' AND section='$section'");
        $student_count = $student_check->fetch_assoc()['count'];
        
        if($student_count > 0) {
            $_SESSION['marks_subject'] = $subject_code;
            $_SESSION['marks_section'] = $section;
            $_SESSION['marks_exam_type'] = $exam_type;
            
            // Refresh the page to show the marks entry form
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        } else {
            $marks_error = "No students found in this section!";
        }
    } else {
        $marks_error = "You are not assigned to this subject/section!";
    }
}

// Save marks with components
if(isset($_POST['save_marks_components'])){
    $subject_code = $_POST['subject_code'];
    $section = $_POST['section'];
    $exam_type = $_POST['exam_type'];

    if(isset($_POST['written']) && is_array($_POST['written'])){
        $success_count = 0;
        foreach($_POST['written'] as $student_id => $written){
            $assignment = isset($_POST['assignment'][$student_id]) ? $_POST['assignment'][$student_id] : 0;
            $dinamic = isset($_POST['dinamic'][$student_id]) ? $_POST['dinamic'][$student_id] : 0;
            $total = $written + $assignment + $dinamic;
            
            // Ensure totals don't exceed limits
            if($written > 40) $written = 40;
            if($assignment > 5) $assignment = 5;
            if($dinamic > 5) $dinamic = 5;
            if($total > 50) $total = 50;
            
            $stmt = $conn->prepare("INSERT INTO internal_marks 
                (student_id, subject_code, section, exam_type, marks, assignment_marks, dinamic_marks)
                VALUES (?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                marks=?, assignment_marks=?, dinamic_marks=?");
            
            $stmt->bind_param("isssiiiiii", 
                $student_id, $subject_code, $section, $exam_type, $total, 
                $assignment, $dinamic,
                $total, $assignment, $dinamic);
                
            if($stmt->execute()){
                $success_count++;
            } else {
                $marks_error = "Error: " . $stmt->error;
            }
        }
        if($success_count > 0){
            // Clear session
            unset($_SESSION['marks_subject']);
            unset($_SESSION['marks_section']);
            unset($_SESSION['marks_exam_type']);
            
            // Redirect with success message
            header("Location: ".$_SERVER['PHP_SELF']."?success=marks");
            exit();
        }
    }
}

// ✅ ==================== FEE COLLECTION ====================
$fee_error = "";
$fee_success = false;
$receipt_data = null;
$selected_student = null;
$show_fee_form = false;

// Check session for fee collection
if(isset($_SESSION['fee_student_id'])) {
    $show_fee_form = true;
    $student_id = $_SESSION['fee_student_id'];
    $student_query = $conn->query("SELECT * FROM students WHERE id='$student_id' AND department='$employee_department'");
    if($student_query->num_rows > 0) {
        $selected_student = $student_query->fetch_assoc();
        
        $paid_query = $conn->query("SELECT SUM(amount) as total_paid FROM payments WHERE student_id='$student_id'");
        $paid_data = $paid_query->fetch_assoc();
        $total_paid = $paid_data['total_paid'] ?? 0;
        $old_balance = $selected_student['total_fee'] - $total_paid;
    }
}

// Load student for fee collection
if(isset($_POST['load_fee_student'])){
    $student_id = $_POST['student_id'];
    
    // Verify student belongs to this department
    $check_student = $conn->query("SELECT * FROM students WHERE id='$student_id' AND department='$employee_department'");
    
    if($check_student->num_rows > 0) {
        $_SESSION['fee_student_id'] = $student_id;
        
        // Refresh the page to show the fee collection form
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        $fee_error = "Student not found in your department!";
    }
}

// Collect fee
if(isset($_POST['collect_fee'])){
    $student_id = $_POST['student_id'];
    $amount = intval($_POST['amount']);
    
    // Verify student belongs to this department
    $student_query = $conn->query("SELECT * FROM students WHERE id='$student_id' AND department='$employee_department'");
    
    if($student_query->num_rows > 0) {
        $student = $student_query->fetch_assoc();
        
        $paid_query = $conn->query("SELECT SUM(amount) as total_paid FROM payments WHERE student_id='$student_id'");
        $paid_data = $paid_query->fetch_assoc();
        $total_paid = $paid_data['total_paid'] ?? 0;
        $old_balance = $student['total_fee'] - $total_paid;

        if($amount > 0 && $amount <= $old_balance){
            $conn->query("INSERT INTO payments (student_id, amount, pay_date) VALUES ('$student_id', '$amount', NOW())");
            $new_balance = $old_balance - $amount;
            
            $receipt_data = [
                "receipt_no" => rand(10000,99999),
                "student_name" => $student['name'],
                "student_id" => $student_id,
                "old_balance" => $old_balance,
                "amount" => $amount,
                "new_balance" => $new_balance,
                "date" => date("Y-m-d H:i:s")
            ];
            $fee_success = true;
            
            // Clear session after successful payment
            unset($_SESSION['fee_student_id']);
        } else {
            $fee_error = "Invalid amount! Maximum allowed: ₹" . $old_balance;
        }
    } else {
        $fee_error = "Student not found in your department!";
    }
}

// Cancel fee collection
if(isset($_GET['cancel_fee'])) {
    unset($_SESSION['fee_student_id']);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// ✅ Student list for fee collection
$students_list_fee = $conn->query("SELECT * FROM students WHERE department='$employee_department' ORDER BY roll_no ASC");

// ✅ Attendance Report Data
$attendance_report = $conn->query("
    SELECT s.roll_no, s.name, s.section,
           COUNT(a.id) as total_classes,
           SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) as total_present,
           SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) as total_absent
    FROM students s
    LEFT JOIN attendance a ON s.id = a.student_id
    WHERE s.department = '$employee_department'
    GROUP BY s.id
    ORDER BY s.section, s.roll_no
");

// ✅ Monthly Report Data
$month = date("m");
$year = date("Y");
$monthly_report = $conn->query("
    SELECT s.roll_no, s.name, s.section,
           COUNT(a.id) as total_classes,
           SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) as total_present
    FROM students s
    LEFT JOIN attendance a ON s.id = a.student_id 
    AND MONTH(a.attendance_date) = $month
    AND YEAR(a.attendance_date) = $year
    WHERE s.department = '$employee_department'
    GROUP BY s.id
    ORDER BY s.section, s.roll_no
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Employee Dashboard | A.A.N.M.&.V.V.R.S.R. POLYTECHNIC</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        /* CSS Variables for Light/Dark Theme */
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #ec4899;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #0f172a;
            --darker: #020617;
            --light: #f8fafc;
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
            --shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            --shadow-hover: 0 40px 70px -15px rgba(0, 0, 0, 0.7);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--darker);
            min-height: 100vh;
            position: relative;
            color: white;
        }

        /* Ultra Modern Animated Background */
        #gradient-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            background: linear-gradient(-45deg, #4158D0, #C850C0, #FFCC70, #2193b0);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Noise Texture */
        .noise {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0.15;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E");
            pointer-events: none;
        }

        /* Floating Particles */
        .particle {
            position: fixed;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            pointer-events: none;
            z-index: -1;
            animation: particle-float 20s infinite linear;
        }

        @keyframes particle-float {
            from {
                transform: translateY(100vh) rotate(0deg);
            }
            to {
                transform: translateY(-100px) rotate(720deg);
            }
        }

        /* Main Container */
        .app {
            max-width: 1440px;
            margin: 0 auto;
            padding: 1.5rem;
            position: relative;
            z-index: 1;
        }

        /* Ultra Modern Glass Navigation */
        .glass-nav {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 30px;
            padding: 1rem 2rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            animation: slideDown 0.6s ease;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            transform: rotate(-5deg);
            transition: transform 0.3s ease;
        }

        .logo-icon:hover {
            transform: rotate(0deg) scale(1.1);
        }

        .logo-text h1 {
            font-size: 1.3rem;
            font-weight: 700;
            background: linear-gradient(135deg, #fff, #a5b4fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .logo-text p {
            font-size: 0.8rem;
            opacity: 0.8;
        }

        .nav-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .theme-toggle {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .theme-toggle:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: rotate(45deg);
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 0.7rem 1.5rem;
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logout-btn:hover {
            background: var(--danger);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(239, 68, 68, 0.4);
        }

        /* Welcome Hero Section */
        .hero-section {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.3), rgba(236, 72, 153, 0.3));
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 40px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
            animation: slideUp 0.7s ease;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .greeting {
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            opacity: 0.8;
            margin-bottom: 0.5rem;
        }

        .hero-title {
            font-size: 3rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1rem;
        }

        .hero-title span {
            background: linear-gradient(135deg, #ffd700, #ffa500);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-stats {
            display: flex;
            gap: 2rem;
            margin-top: 1.5rem;
        }

        .hero-stat-item {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .hero-stat-icon {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }

        .hero-stat-info h4 {
            font-size: 1.3rem;
            font-weight: 700;
        }

        .hero-stat-info p {
            font-size: 0.8rem;
            opacity: 0.7;
        }

        .hero-pattern {
            position: absolute;
            top: -50%;
            right: -10%;
            width: 80%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: pulse 8s ease infinite;
        }

        /* Quick Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card-modern {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 30px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            animation: fadeInScale 0.5s ease;
            animation-fill-mode: both;
        }

        .stat-card-modern:nth-child(1) { animation-delay: 0.1s; }
        .stat-card-modern:nth-child(2) { animation-delay: 0.2s; }
        .stat-card-modern:nth-child(3) { animation-delay: 0.3s; }

        .stat-card-modern:hover {
            transform: translateY(-10px) scale(1.02);
            background: rgba(255, 255, 255, 0.15);
            box-shadow: var(--shadow-hover);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .stat-icon-modern {
            width: 55px;
            height: 55px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }

        .stat-trend {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .trend-up {
            background: rgba(16, 185, 129, 0.2);
            color: var(--success);
        }

        .stat-number {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 0.3rem;
        }

        .stat-label-modern {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        /* Action Grid */
        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .action-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 25px;
            padding: 1.5rem 0.8rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            animation: fadeInScale 0.5s ease;
            animation-fill-mode: both;
        }

        .action-card:nth-child(1) { animation-delay: 0.15s; }
        .action-card:nth-child(2) { animation-delay: 0.25s; }
        .action-card:nth-child(3) { animation-delay: 0.35s; }
        .action-card:nth-child(4) { animation-delay: 0.45s; }
        .action-card:nth-child(5) { animation-delay: 0.55s; }
        .action-card:nth-child(6) { animation-delay: 0.65s; }
        .action-card:nth-child(7) { animation-delay: 0.75s; }
        .action-card:nth-child(8) { animation-delay: 0.85s; }

        .action-card:hover {
            transform: translateY(-8px) scale(1.02);
            background: rgba(255, 255, 255, 0.2);
            box-shadow: var(--shadow-hover);
        }

        .action-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.8rem;
            transition: all 0.3s ease;
        }

        .action-card:hover .action-icon {
            transform: rotate(10deg) scale(1.1);
        }

        .action-title {
            font-weight: 600;
            margin-bottom: 0.3rem;
        }

        .action-subtitle {
            font-size: 0.7rem;
            opacity: 0.7;
        }

        /* Section Styles */
        .section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 30px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .section-header h3 {
            font-size: 1.2rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-header i {
            color: var(--primary);
        }

        .badge {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            padding: 0.3rem 1rem;
            border-radius: 50px;
            font-size: 0.8rem;
        }

        /* Subject Cards */
        .subject-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
        }

        .subject-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 18px;
            padding: 1.2rem;
            transition: all 0.3s ease;
        }

        .subject-card:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-3px);
            border-color: var(--primary);
        }

        .subject-code {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.3rem;
        }

        .subject-name {
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .subject-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
        }

        .subject-meta i {
            margin-right: 0.3rem;
            color: var(--primary);
        }

        /* Timetable Grid */
        .timetable-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 0.8rem;
            margin-top: 1rem;
        }

        .timetable-day {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 18px;
            padding: 1rem;
        }

        .timetable-day h4 {
            text-align: center;
            margin-bottom: 0.8rem;
            color: var(--primary);
            font-weight: 600;
            font-size: 1rem;
        }

        .timetable-period {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 0.6rem;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
            text-align: center;
        }

        .timetable-period strong {
            color: var(--primary);
        }

        /* Table Styles */
        .modern-table {
            width: 100%;
            border-collapse: collapse;
        }

        .modern-table th {
            text-align: left;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.15);
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .modern-table td {
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .modern-table tbody tr {
            transition: all 0.3s ease;
        }

        .modern-table tbody tr:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: scale(1.01);
        }

        /* Marks Input Styles */
        .marks-input-written {
            width: 80px;
            padding: 0.5rem;
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 10px;
            color: white;
            text-align: center;
            transition: all 0.3s ease;
        }

        .marks-input-written:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        .marks-input-assignment {
            width: 80px;
            padding: 0.5rem;
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 10px;
            color: white;
            text-align: center;
            transition: all 0.3s ease;
        }

        .marks-input-assignment:focus {
            outline: none;
            border-color: var(--success);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        }

        .marks-input-dinamic {
            width: 80px;
            padding: 0.5rem;
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: 10px;
            color: white;
            text-align: center;
            transition: all 0.3s ease;
        }

        .marks-input-dinamic:focus {
            outline: none;
            border-color: var(--warning);
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
        }

        .marks-total {
            width: 80px;
            padding: 0.5rem;
            background: rgba(99, 102, 241, 0.2);
            border: 1px solid rgba(99, 102, 241, 0.4);
            border-radius: 10px;
            color: white;
            text-align: center;
            font-weight: 600;
        }

        /* Fee Collection Styles */
        .fee-student-selector {
            margin-bottom: 2rem;
        }
        
        .student-search {
            width: 100%;
            padding: 1rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            color: white;
            margin-bottom: 1rem;
        }
        
        .student-list {
            max-height: 300px;
            overflow-y: auto;
            border-radius: 12px;
            background: rgba(0,0,0,0.2);
        }
        
        .student-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .student-item:hover {
            background: rgba(99, 102, 241, 0.1);
        }
        
        .student-item.selected {
            background: rgba(16, 185, 129, 0.2);
            border-left: 4px solid var(--success);
        }
        
        .student-info h4 {
            font-size: 1.1rem;
            margin-bottom: 0.2rem;
        }
        
        .student-info p {
            font-size: 0.8rem;
            opacity: 0.7;
        }
        
        .student-fee {
            text-align: right;
        }
        
        .student-fee .total {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary);
        }
        
        .student-fee .paid {
            font-size: 0.8rem;
            color: var(--success);
        }
        
        .balance-info {
            background: rgba(255,255,255,0.05);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .balance-label {
            font-size: 0.9rem;
            opacity: 0.7;
        }
        
        .balance-amount {
            font-size: 2rem;
            font-weight: 700;
            color: var(--success);
        }
        
        .receipt-card {
            background: white;
            color: #333;
            border-radius: 20px;
            padding: 2rem;
            margin-top: 1rem;
        }
        
        .receipt-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .receipt-header h3 {
            color: var(--primary);
            font-size: 1.5rem;
        }
        
        .receipt-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin: 1.5rem 0;
        }
        
        .receipt-footer {
            margin-top: 2rem;
            text-align: right;
            border-top: 1px dashed #ddd;
            padding-top: 1rem;
        }

        /* Modal Styles */
        .modal-modern {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(15px);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal-modern.active {
            display: flex;
            opacity: 1;
        }

        .modal-content-modern {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            border-radius: 40px;
            padding: 2.5rem;
            max-width: 1100px;
            width: 95%;
            max-height: 85vh;
            overflow-y: auto;
            position: relative;
            transform: scale(0.9) translateY(20px);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: var(--shadow);
        }

        .modal-modern.active .modal-content-modern {
            transform: scale(1) translateY(0);
        }

        .modal-header-modern {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        }

        .modal-header-modern h2 {
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(135deg, #fff, #a5b4fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .modal-close-modern {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .modal-close-modern:hover {
            background: var(--danger);
            transform: rotate(90deg);
        }

        /* Form Elements */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .input-group-modern {
            margin-bottom: 1rem;
        }

        .input-group-modern label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.8);
        }

        .input-modern {
            width: 100%;
            padding: 1rem 1.2rem;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid transparent;
            border-radius: 20px;
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .input-modern:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 30px rgba(99, 102, 241, 0.3);
        }

        .select-modern {
            width: 100%;
            padding: 1rem 1.2rem;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid transparent;
            border-radius: 20px;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
        }

        .btn-modern {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            border-radius: 20px;
            padding: 1rem 2rem;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            font-size: 1rem;
        }

        .btn-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(99, 102, 241, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid rgba(255,255,255,0.1);
        }
        
        .btn-outline:hover {
            border-color: var(--primary);
        }

        /* Animations */
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.2); opacity: 0.8; }
        }

        /* Alert Messages */
        .alert-modern {
            padding: 1rem 1.5rem;
            border-radius: 16px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-modern.success {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: var(--success);
        }

        .alert-modern.error {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: var(--danger);
        }

        .alert-modern.info {
            background: rgba(99, 102, 241, 0.2);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: var(--primary);
        }

        /* Radio Group */
        .radio-group-modern {
            display: flex;
            gap: 1rem;
        }

        .radio-option {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            cursor: pointer;
        }

        .radio-option input[type="radio"] {
            accent-color: var(--primary);
            width: 16px;
            height: 16px;
        }

        /* Progress Bar */
        .progress-modern {
            width: 100%;
            height: 6px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        /* Badge Styles */
        .badge-modern {
            padding: 0.3rem 0.8rem;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }

        .badge-success {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: var(--success);
        }

        .badge-danger {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: var(--danger);
        }

        .badge-warning {
            background: rgba(245, 158, 11, 0.2);
            border: 1px solid rgba(245, 158, 11, 0.3);
            color: var(--warning);
        }

        /* Profile Modal */
        .profile-header-modern {
            text-align: center;
            margin-bottom: 2rem;
        }

        .profile-avatar-modern {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid var(--primary);
            object-fit: cover;
            margin: 0 auto 1rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .profile-avatar-placeholder {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: 700;
            color: white;
            margin: 0 auto 1rem;
        }

        .profile-name-modern {
            font-size: 2rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.3rem;
        }

        .profile-title-modern {
            color: var(--primary);
            font-weight: 500;
            font-size: 1rem;
        }

        .profile-grid-modern {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .profile-item-modern {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 18px;
            padding: 1.2rem;
            transition: all 0.3s ease;
        }

        .profile-item-modern:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        .profile-item-label {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.5);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.3rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .profile-item-value {
            font-size: 1rem;
            font-weight: 600;
            color: white;
        }

        /* Marks Distribution Badges */
        .marks-distribution {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .marks-badge {
            padding: 0.5rem 1.2rem;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .marks-badge.primary {
            background: rgba(99, 102, 241, 0.15);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: var(--primary);
        }

        .marks-badge.success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: var(--success);
        }

        .marks-badge.warning {
            background: rgba(245, 158, 11, 0.15);
            border: 1px solid rgba(245, 158, 11, 0.3);
            color: var(--warning);
        }

        .marks-badge.total {
            background: rgba(99, 102, 241, 0.25);
            border: 1px solid rgba(99, 102, 241, 0.4);
            color: white;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .hero-title {
                font-size: 2.5rem;
            }
        }

        @media (max-width: 768px) {
            .app {
                padding: 1rem;
            }

            .glass-nav {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }

            .nav-actions {
                width: 100%;
                justify-content: center;
            }

            .hero-title {
                font-size: 2rem;
            }

            .hero-stats {
                flex-direction: column;
                gap: 1rem;
            }

            .action-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .modal-content-modern {
                padding: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .action-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .timetable-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .hero-title {
                font-size: 1.5rem;
            }

            .marks-distribution {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .receipt-details {
                grid-template-columns: 1fr;
            }
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div id="gradient-bg"></div>
    <div class="noise"></div>

    <!-- Floating Particles -->
    <div id="particles-container"></div>

    <!-- Main App Container -->
    <div class="app">
        <!-- Glass Navigation -->
        <nav class="glass-nav">
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-gem"></i>
                </div>
                <div class="logo-text">
                    <h1>A.A.N.M & V.V.R.S.R POLYTECHNIC</h1>
                    <p><?php echo htmlspecialchars($employee_department); ?> Department</p>
                </div>
            </div>
            
            <div class="nav-actions">
                <div class="theme-toggle" onclick="toggleTheme()">
                    <i class="fas fa-moon"></i>
                </div>
                <a href="../logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="hero-section">
            <div class="hero-content">
                <div class="greeting">Welcome back,</div>
                <h1 class="hero-title">
                    <?php echo htmlspecialchars(explode(' ', $emp['name'])[0]); ?> <span><?php echo htmlspecialchars(explode(' ', $emp['name'])[1] ?? ''); ?></span>
                </h1>
                <p style="opacity: 0.8; max-width: 600px;">Manage your classes, track attendance, evaluate performance, and collect fees from one central hub.</p>
                
                <div class="hero-stats">
                    <div class="hero-stat-item">
                        <div class="hero-stat-icon">
                            <i class="fas fa-briefcase" style="color: #ffd700;"></i>
                        </div>
                        <div class="hero-stat-info">
                            <h4><?php echo htmlspecialchars($emp['profession']); ?></h4>
                            <p>Designation</p>
                        </div>
                    </div>
                    <div class="hero-stat-item">
                        <div class="hero-stat-icon">
                            <i class="fas fa-rupee-sign" style="color: #10b981;"></i>
                        </div>
                        <div class="hero-stat-info">
                            <h4>₹<?php echo number_format($emp['salary']); ?></h4>
                            <p>Monthly Salary</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hero-pattern"></div>
        </div>

        <!-- Quick Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card-modern">
                <div class="stat-header">
                    <div class="stat-icon-modern">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <span class="stat-trend trend-up">Active</span>
                </div>
                <div class="stat-number"><?php echo $assigned_subjects->num_rows; ?></div>
                <div class="stat-label-modern">Assigned Subjects</div>
            </div>
            
            <div class="stat-card-modern">
                <div class="stat-header">
                    <div class="stat-icon-modern">
                        <i class="fas fa-users"></i>
                    </div>
                    <span class="stat-trend trend-up">Total</span>
                </div>
                <div class="stat-number"><?php echo $students; ?></div>
                <div class="stat-label-modern">Department Students</div>
            </div>
            
            <div class="stat-card-modern">
                <div class="stat-header">
                    <div class="stat-icon-modern">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                    <span class="stat-trend trend-up">This Week</span>
                </div>
                <div class="stat-number"><?php echo $employee_timetable->num_rows; ?></div>
                <div class="stat-label-modern">Scheduled Periods</div>
            </div>
        </div>

        <!-- Action Grid -->
        <div class="action-grid">
            <div class="action-card" onclick="openModal('profileModal')">
                <div class="action-icon">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="action-title">My Profile</div>
                <div class="action-subtitle">View personal details</div>
            </div>
            
            <div class="action-card" onclick="openModal('attendanceModal')">
                <div class="action-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="action-title">Mark Attendance</div>
                <div class="action-subtitle">Daily attendance</div>
            </div>
            
            <div class="action-card" onclick="openModal('marksModal')">
                <div class="action-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="action-title">Enter Marks</div>
                <div class="action-subtitle">Internal assessments</div>
            </div>
            
            <div class="action-card" onclick="openModal('feeModal')">
                <div class="action-icon">
                    <i class="fas fa-rupee-sign"></i>
                </div>
                <div class="action-title">Collect Fee</div>
                <div class="action-subtitle">Student fee collection</div>
            </div>
            
            <div class="action-card" onclick="openModal('viewAttendanceModal')">
                <div class="action-icon">
                    <i class="fas fa-eye"></i>
                </div>
                <div class="action-title">View Attendance</div>
                <div class="action-subtitle">Overall report</div>
            </div>
            
            <div class="action-card" onclick="openModal('monthlyReportModal')">
                <div class="action-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="action-title">Monthly Report</div>
                <div class="action-subtitle"><?php echo date('F Y'); ?></div>
            </div>
            
            <div class="action-card" onclick="openModal('timetableModal')">
                <div class="action-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="action-title">My Timetable</div>
                <div class="action-subtitle">Weekly schedule</div>
            </div>
            
            <div class="action-card" onclick="openModal('subjectsModal')">
                <div class="action-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <div class="action-title">My Subjects</div>
                <div class="action-subtitle">Assigned subjects</div>
            </div>
        </div>

        <!-- Subjects Preview -->
        <div class="section">
            <div class="section-header">
                <h3><i class="fas fa-book-open"></i> My Assigned Subjects</h3>
                <span class="badge"><?php echo $assigned_subjects->num_rows; ?> Subjects</span>
            </div>

            <?php if($assigned_subjects->num_rows > 0): ?>
            <div class="subject-grid">
                <?php 
                $assigned_subjects->data_seek(0);
                $count = 0;
                while($subject = $assigned_subjects->fetch_assoc()): 
                    if($count >= 4) break;
                    $count++;
                ?>
                <div class="subject-card">
                    <div class="subject-code"><?php echo htmlspecialchars($subject['subject_code']); ?></div>
                    <div class="subject-name"><?php echo htmlspecialchars($subject['subject_name']); ?></div>
                    <div class="subject-meta">
                        <span><i class="fas fa-layer-group"></i> Sem <?php echo $subject['semester']; ?></span>
                        <span><i class="fas fa-users"></i> Sec <?php echo $subject['section']; ?></span>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php if($assigned_subjects->num_rows > 4): ?>
            <div style="text-align: center; margin-top: 1rem;">
                <button class="btn-modern" style="width: auto; padding: 0.5rem 2rem;" onclick="openModal('subjectsModal')">
                    View All <?php echo $assigned_subjects->num_rows; ?> Subjects
                </button>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Timetable Preview -->
        <div class="section">
            <div class="section-header">
                <h3><i class="fas fa-calendar-alt"></i> Weekly Timetable</h3>
                <span class="badge">This Week</span>
            </div>

            <?php if($employee_timetable->num_rows > 0): ?>
            <div class="timetable-grid">
                <?php
                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                foreach($days as $day):
                    $day_periods = [];
                    $employee_timetable->data_seek(0);
                    while($period = $employee_timetable->fetch_assoc()) {
                        if($period['day_of_week'] == $day) {
                            $day_periods[] = $period;
                        }
                    }
                ?>
                <div class="timetable-day">
                    <h4><?php echo substr($day, 0, 3); ?></h4>
                    <?php if(!empty($day_periods)): ?>
                        <?php foreach($day_periods as $period): ?>
                        <div class="timetable-period">
                            <strong>P<?php echo $period['period']; ?></strong><br>
                            <?php echo htmlspecialchars($period['subject_code']); ?><br>
                            <small>Sec <?php echo htmlspecialchars($period['section']); ?></small>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="opacity: 0.3; text-align: center; padding: 0.5rem;">
                            <small>No classes</small>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ==================== PROFILE MODAL ==================== -->
    <div id="profileModal" class="modal-modern">
        <div class="modal-content-modern">
            <div class="modal-header-modern">
                <h2><i class="fas fa-user-circle"></i> My Profile</h2>
                <div class="modal-close-modern" onclick="closeModal('profileModal')">
                    <i class="fas fa-times"></i>
                </div>
            </div>

            <div class="profile-header-modern">
                <?php if(!empty($emp['photo']) && file_exists("../uploads/".$emp['photo'])): ?>
                    <img src="../uploads/<?php echo htmlspecialchars($emp['photo']); ?>" class="profile-avatar-modern">
                <?php else: ?>
                    <div class="profile-avatar-placeholder">
                        <?php echo strtoupper(substr($emp['name'], 0, 2)); ?>
                    </div>
                <?php endif; ?>
                <h3 class="profile-name-modern"><?php echo htmlspecialchars($emp['name']); ?></h3>
                <div class="profile-title-modern"><?php echo htmlspecialchars($emp['profession']); ?></div>
            </div>

            <div class="profile-grid-modern">
                <?php
                $profile_fields = [
                    'emp_id' => ['icon' => 'fa-id-card', 'label' => 'Employee ID'],
                    'dob' => ['icon' => 'fa-calendar', 'label' => 'Date of Birth'],
                    'department' => ['icon' => 'fa-building', 'label' => 'Department'],
                    'phone' => ['icon' => 'fa-phone', 'label' => 'Phone'],
                    'email' => ['icon' => 'fa-envelope', 'label' => 'Email'],
                    'category' => ['icon' => 'fa-tag', 'label' => 'Category'],
                    'father_name' => ['icon' => 'fa-user-tie', 'label' => 'Father\'s Name'],
                    'blood_group' => ['icon' => 'fa-droplet', 'label' => 'Blood Group'],
                    'permanent_address' => ['icon' => 'fa-map-marker-alt', 'label' => 'Address'],
                    'profession' => ['icon' => 'fa-briefcase', 'label' => 'Profession'],
                    'salary' => ['icon' => 'fa-rupee-sign', 'label' => 'Salary'],
                    'username' => ['icon' => 'fa-user', 'label' => 'Username']
                ];

                foreach($profile_fields as $field => $data):
                    if(isset($emp[$field]) && !empty($emp[$field])):
                ?>
                <div class="profile-item-modern">
                    <div class="profile-item-label">
                        <i class="fas <?php echo $data['icon']; ?>"></i> <?php echo $data['label']; ?>
                    </div>
                    <div class="profile-item-value">
                        <?php 
                        if($field == 'salary') echo '₹' . number_format($emp[$field]);
                        elseif($field == 'dob') echo date('d M Y', strtotime($emp[$field]));
                        else echo htmlspecialchars($emp[$field]); 
                        ?>
                    </div>
                </div>
                <?php endif; endforeach; ?>
            </div>
        </div>
    </div>

    <!-- ==================== MARK ATTENDANCE MODAL ==================== -->
    <div id="attendanceModal" class="modal-modern <?php echo $show_students ? 'active' : ''; ?>">
        <div class="modal-content-modern">
            <div class="modal-header-modern">
                <h2><i class="fas fa-calendar-check"></i> Mark Attendance</h2>
                <div class="modal-close-modern" onclick="closeModal('attendanceModal')">
                    <i class="fas fa-times"></i>
                </div>
            </div>

            <?php if($attendance_error): ?>
            <div class="alert-modern error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $attendance_error; ?>
            </div>
            <?php endif; ?>

            <?php if(isset($_GET['attendance_success'])): ?>
            <div class="alert-modern success">
                <i class="fas fa-check-circle"></i> Attendance saved! Present: <?php echo $_GET['present']; ?>, Absent: <?php echo $_GET['absent']; ?>
            </div>
            <?php endif; ?>

            <!-- Load Students Form - Shows when no students are loaded -->
            <?php if(!$show_students): ?>
            <form method="POST">
                <div class="form-grid">
                    <div class="input-group-modern">
                        <label>Subject</label>
                        <select name="subject_code" class="select-modern" required>
                            <option value="">Select Subject</option>
                            <?php
                            $assigned_subjects->data_seek(0);
                            while($sub = $assigned_subjects->fetch_assoc()):
                            ?>
                            <option value="<?php echo $sub['subject_code']; ?>">
                                <?php echo $sub['subject_code']; ?> - <?php echo $sub['subject_name']; ?> (Sec <?php echo $sub['section']; ?>)
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="input-group-modern">
                        <label>Section</label>
                        <select name="section" class="select-modern" required>
                            <option value="">Select Section</option>
                            <?php
                            $sections->data_seek(0);
                            while($sec = $sections->fetch_assoc()):
                            ?>
                            <option value="<?php echo $sec['section']; ?>">Section <?php echo $sec['section']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="input-group-modern">
                        <label>Date</label>
                        <input type="date" name="attendance_date" class="input-modern" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>
                
                <button type="submit" name="load_attendance_students" class="btn-modern">
                    <i class="fas fa-users"></i> Load Students
                </button>
            </form>
            <?php endif; ?>

            <!-- Student attendance form - Shows when students are loaded -->
            <?php if($show_students): 
                $students_list = $conn->query("SELECT * FROM students WHERE department='$employee_department' AND section='{$_SESSION['attendance_section']}' ORDER BY roll_no");
            ?>
            <form method="POST">
                <input type="hidden" name="subject_code" value="<?php echo $_SESSION['attendance_subject']; ?>">
                <input type="hidden" name="section" value="<?php echo $_SESSION['attendance_section']; ?>">
                <input type="hidden" name="attendance_date" value="<?php echo $_SESSION['attendance_date']; ?>">

                <div style="max-height: 400px; overflow-y: auto; margin: 1.5rem 0;">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Roll No</th>
                                <th>Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($student = $students_list->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $student['roll_no']; ?></td>
                                <td><?php echo $student['name']; ?></td>
                                <td>
                                    <div class="radio-group-modern">
                                        <label class="radio-option">
                                            <input type="radio" name="attendance[<?php echo $student['id']; ?>]" value="Present" checked> Present
                                        </label>
                                        <label class="radio-option">
                                            <input type="radio" name="attendance[<?php echo $student['id']; ?>]" value="Absent"> Absent
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <div style="display: flex; gap: 1rem;">
                    <button type="submit" name="submit_attendance" class="btn-modern">
                        <i class="fas fa-save"></i> Save Attendance
                    </button>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- ==================== ENTER MARKS MODAL ==================== -->
    <div id="marksModal" class="modal-modern <?php echo $show_marks_students ? 'active' : ''; ?>">
        <div class="modal-content-modern">
            <div class="modal-header-modern">
                <h2><i class="fas fa-star"></i> Enter Internal Marks</h2>
                <div class="modal-close-modern" onclick="closeModal('marksModal')">
                    <i class="fas fa-times"></i>
                </div>
            </div>

            <?php if($marks_error): ?>
            <div class="alert-modern error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $marks_error; ?>
            </div>
            <?php endif; ?>

            <?php if(isset($_GET['success']) && $_GET['success'] == 'marks'): ?>
            <div class="alert-modern success">
                <i class="fas fa-check-circle"></i> Marks saved successfully!
            </div>
            <?php endif; ?>

            <!-- Load Students Form - Shows when no students are loaded -->
            <?php if(!$show_marks_students): ?>
            <form method="POST">
                <div class="form-grid">
                    <div class="input-group-modern">
                        <label>Subject</label>
                        <select name="subject_code" class="select-modern" required>
                            <option value="">Select Subject</option>
                            <?php
                            $assigned_subjects->data_seek(0);
                            while($sub = $assigned_subjects->fetch_assoc()):
                            ?>
                            <option value="<?php echo $sub['subject_code']; ?>">
                                <?php echo $sub['subject_code']; ?> - <?php echo $sub['subject_name']; ?> (Sec <?php echo $sub['section']; ?>)
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="input-group-modern">
                        <label>Section</label>
                        <select name="section" class="select-modern" required>
                            <option value="">Select Section</option>
                            <?php
                            $sections->data_seek(0);
                            while($sec = $sections->fetch_assoc()):
                            ?>
                            <option value="<?php echo $sec['section']; ?>">Section <?php echo $sec['section']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="input-group-modern">
                        <label>Exam Type</label>
                        <select name="exam_type" class="select-modern" required>
                            <option value="">Select Exam</option>
                            <option value="Mid1">Mid Term 1</option>
                            <option value="Mid2">Mid Term 2</option>
                            <option value="Mid3">Mid Term 3</option>
                            <option value="Slip1">Slip Test 1</option>
                            <option value="Slip2">Slip Test 2</option>
                        </select>
                    </div>
                </div>
                
                <button type="submit" name="load_marks_students" class="btn-modern">
                    <i class="fas fa-users"></i> Load Students
                </button>
            </form>
            <?php endif; ?>

            <!-- Student marks entry form - Shows when students are loaded -->
            <?php if($show_marks_students): 
                $marks_students_list = $conn->query("SELECT * FROM students WHERE department='$employee_department' AND section='{$_SESSION['marks_section']}' ORDER BY roll_no");
            ?>
            <form method="POST">
                <input type="hidden" name="subject_code" value="<?php echo $_SESSION['marks_subject']; ?>">
                <input type="hidden" name="section" value="<?php echo $_SESSION['marks_section']; ?>">
                <input type="hidden" name="exam_type" value="<?php echo $_SESSION['marks_exam_type']; ?>">

                <!-- Marks Distribution Info -->
                <div class="marks-distribution">
                    <div class="marks-badge primary">
                        <i class="fas fa-pencil-alt"></i> Written: 40
                    </div>
                    <div class="marks-badge success">
                        <i class="fas fa-tasks"></i> Assignment: 5
                    </div>
                    <div class="marks-badge warning">
                        <i class="fas fa-user-check"></i> Dinamic: 5
                    </div>
                    <div class="marks-badge total">
                        <i class="fas fa-calculator"></i> Total: 50
                    </div>
                </div>

                <div style="max-height: 400px; overflow-y: auto; margin: 1.5rem 0;">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Roll No</th>
                                <th>Name</th>
                                <th style="background: rgba(99, 102, 241, 0.2);">Written (40)</th>
                                <th style="background: rgba(16, 185, 129, 0.2);">Assignment (5)</th>
                                <th style="background: rgba(245, 158, 11, 0.2);">Dinamic (5)</th>
                                <th style="background: rgba(99, 102, 241, 0.3);">Total (50)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($student = $marks_students_list->fetch_assoc()): 
                                // Check if marks already exist
                                $check = $conn->prepare("SELECT marks, assignment_marks, dinamic_marks FROM internal_marks 
                                    WHERE student_id=? AND subject_code=? AND exam_type=?");
                                $check->bind_param("iss", $student['id'], $_SESSION['marks_subject'], $_SESSION['marks_exam_type']);
                                $check->execute();
                                $check->bind_result($existing_marks, $existing_assignment, $existing_dinamic);
                                $check->fetch();
                                $check->close();
                                
                                // Calculate written marks from total
                                $existing_written = $existing_marks - ($existing_assignment ?? 0) - ($existing_dinamic ?? 0);
                                if($existing_written < 0) $existing_written = 0;
                                
                                $total = ($existing_marks ?? 0);
                            ?>
                            <tr>
                                <td><?php echo $student['roll_no']; ?></td>
                                <td><?php echo $student['name']; ?></td>
                                <td>
                                    <input type="number" 
                                           name="written[<?php echo $student['id']; ?>]" 
                                           class="marks-input-written" 
                                           value="<?php echo $existing_written ?: ''; ?>" 
                                           min="0" max="40" 
                                           data-student="<?php echo $student['id']; ?>"
                                           onchange="updateTotal(<?php echo $student['id']; ?>)"
                                           onkeyup="updateTotal(<?php echo $student['id']; ?>)">
                                </td>
                                <td>
                                    <input type="number" 
                                           name="assignment[<?php echo $student['id']; ?>]" 
                                           class="marks-input-assignment" 
                                           value="<?php echo $existing_assignment ?: ''; ?>" 
                                           min="0" max="5" 
                                           data-student="<?php echo $student['id']; ?>"
                                           onchange="updateTotal(<?php echo $student['id']; ?>)"
                                           onkeyup="updateTotal(<?php echo $student['id']; ?>)">
                                </td>
                                <td>
                                    <input type="number" 
                                           name="dinamic[<?php echo $student['id']; ?>]" 
                                           class="marks-input-dinamic" 
                                           value="<?php echo $existing_dinamic ?: ''; ?>" 
                                           min="0" max="5" 
                                           data-student="<?php echo $student['id']; ?>"
                                           onchange="updateTotal(<?php echo $student['id']; ?>)"
                                           onkeyup="updateTotal(<?php echo $student['id']; ?>)">
                                </td>
                                <td>
                                    <input type="number" 
                                           id="total-<?php echo $student['id']; ?>" 
                                           class="marks-total" 
                                           value="<?php echo $total ?: ''; ?>" 
                                           readonly>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <div style="display: flex; gap: 1rem; justify-content: space-between; align-items: center;">
                    <div style="color: rgba(255,255,255,0.6); font-size: 0.9rem;">
                        <i class="fas fa-info-circle" style="color: var(--primary);"></i>
                        Total is automatically calculated
                    </div>
                    <div style="display: flex; gap: 1rem;">
                        <button type="button" class="btn-modern" style="width: auto; padding: 0.8rem 2rem;" onclick="calculateAllTotals()">
                            <i class="fas fa-calculator"></i> Calculate All
                        </button>
                        <button type="submit" name="save_marks_components" class="btn-modern" style="width: auto; padding: 0.8rem 2rem;">
                            <i class="fas fa-save"></i> Save Marks
                        </button>
                    </div>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- ==================== FEE COLLECTION MODAL ==================== -->
    <div id="feeModal" class="modal-modern <?php echo isset($_SESSION['fee_student_id']) ? 'active' : ''; ?>">
        <div class="modal-content-modern">
            <div class="modal-header-modern">
                <h2><i class="fas fa-rupee-sign"></i> Collect Fee</h2>
                <div class="modal-close-modern" onclick="closeModal('feeModal')">
                    <i class="fas fa-times"></i>
                </div>
            </div>

            <?php if($fee_error): ?>
            <div class="alert-modern error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $fee_error; ?>
            </div>
            <?php endif; ?>

            <?php if($fee_success && $receipt_data): ?>
            <!-- Receipt Display -->
            <div class="receipt-card">
                <div class="receipt-header">
                    <h3>A.A.N.M.&.V.V.R.S.R. POLYTECHNIC</h3>
                    <p style="color: #666;">Gudlavalleru - Fee Receipt</p>
                    <h4 style="margin-top: 1rem; color: #333;">PAYMENT RECEIPT</h4>
                </div>
                
                <div style="border-top: 2px dashed var(--primary); border-bottom: 2px dashed var(--primary); padding: 1.5rem 0;">
                    <div class="receipt-details">
                        <div><strong>Receipt No:</strong> <?php echo $receipt_data['receipt_no']; ?></div>
                        <div><strong>Date:</strong> <?php echo $receipt_data['date']; ?></div>
                        <div><strong>Student Name:</strong> <?php echo $receipt_data['student_name']; ?></div>
                        <div><strong>Student ID:</strong> <?php echo $receipt_data['student_id']; ?></div>
                        <div><strong>Previous Balance:</strong> ₹<?php echo number_format($receipt_data['old_balance']); ?></div>
                        <div><strong>Amount Paid:</strong> ₹<?php echo number_format($receipt_data['amount']); ?></div>
                        <div style="grid-column: span 2;"><strong>Remaining Balance:</strong> ₹<?php echo number_format($receipt_data['new_balance']); ?></div>
                    </div>
                </div>
                
                <div class="receipt-footer">
                    <p>Authorized Signatory</p>
                    <p style="margin-top: 1rem;">_________________________</p>
                </div>
                
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button onclick="window.print()" class="btn-modern" style="background: linear-gradient(135deg, #64748b, #475569);">
                        <i class="fas fa-print"></i> Print Receipt
                    </button>
                    <button onclick="window.location.href='<?php echo $_SERVER['PHP_SELF']; ?>'" class="btn-modern">
                        <i class="fas fa-check"></i> Done
                    </button>
                </div>
            </div>
            
            <?php elseif(isset($_SESSION['fee_student_id']) && $selected_student): 
                // Calculate balance
                $paid_query = $conn->query("SELECT SUM(amount) as total_paid FROM payments WHERE student_id='{$_SESSION['fee_student_id']}'");
                $paid_data = $paid_query->fetch_assoc();
                $total_paid = $paid_data['total_paid'] ?? 0;
                $old_balance = $selected_student['total_fee'] - $total_paid;
            ?>
            
            <!-- Student Info and Fee Collection Form -->
            <div class="balance-info">
                <div>
                    <div class="balance-label">Student</div>
                    <div style="font-size: 1.3rem; font-weight: 600;"><?php echo $selected_student['name']; ?></div>
                    <div style="opacity: 0.7;">Roll No: <?php echo $selected_student['roll_no']; ?></div>
                </div>
                <div style="text-align: right;">
                    <div class="balance-label">Remaining Fee</div>
                    <div class="balance-amount">₹<?php echo number_format($old_balance); ?></div>
                </div>
            </div>
            
            <div style="background: rgba(255,255,255,0.03); border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>Total Fee:</span>
                    <span>₹<?php echo number_format($selected_student['total_fee']); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: var(--success);">
                    <span>Total Paid:</span>
                    <span>₹<?php echo number_format($total_paid); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 1.1rem; margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px solid rgba(255,255,255,0.1);">
                    <span>Due Amount:</span>
                    <span style="color: <?php echo $old_balance > 0 ? 'var(--warning)' : 'var(--success)'; ?>;">₹<?php echo number_format($old_balance); ?></span>
                </div>
            </div>

            <form method="POST">
                <input type="hidden" name="student_id" value="<?php echo $_SESSION['fee_student_id']; ?>">
                
                <div class="input-group-modern">
                    <label>Enter Amount to Collect (Max: ₹<?php echo number_format($old_balance); ?>)</label>
                    <input type="number" name="amount" class="input-modern" required min="1" max="<?php echo $old_balance; ?>" step="1" placeholder="Enter amount">
                </div>
                
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" name="collect_fee" class="btn-modern btn-success">
                        <i class="fas fa-rupee-sign"></i> Collect Fee
                    </button>
                    <a href="?cancel_fee=1" class="btn-modern btn-outline">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
            
            <?php else: ?>
            
            <!-- Student Selection Form -->
            <div class="fee-student-selector">
                <input type="text" id="studentSearch" class="student-search" placeholder="Search by name or roll number...">
                
                <form method="POST">
                    <div class="student-list">
                        <?php 
                        $students_list_fee->data_seek(0);
                        while($student = $students_list_fee->fetch_assoc()): 
                            $paid_query = $conn->query("SELECT SUM(amount) as total_paid FROM payments WHERE student_id='{$student['id']}'");
                            $paid_data = $paid_query->fetch_assoc();
                            $total_paid = $paid_data['total_paid'] ?? 0;
                            $due = $student['total_fee'] - $total_paid;
                        ?>
                        <div class="student-item" onclick="selectStudent(<?php echo $student['id']; ?>)">
                            <div class="student-info">
                                <h4><?php echo $student['name']; ?></h4>
                                <p>Roll No: <?php echo $student['roll_no']; ?> | <?php echo $student['course']; ?></p>
                            </div>
                            <div class="student-fee">
                                <div class="total">₹<?php echo number_format($student['total_fee']); ?></div>
                                <div class="paid">Due: ₹<?php echo number_format($due); ?></div>
                            </div>
                            <input type="radio" name="student_id" id="student_<?php echo $student['id']; ?>" value="<?php echo $student['id']; ?>" style="display: none;">
                        </div>
                        <?php endwhile; ?>
                    </div>
                    
                    <button type="submit" name="load_fee_student" class="btn-modern" style="margin-top: 1rem;" id="selectStudentBtn" disabled>
                        <i class="fas fa-arrow-right"></i> Continue to Payment
                    </button>
                </form>
            </div>
            
            <script>
                let selectedStudentId = null;
                
                function selectStudent(id) {
                    // Remove selected class from all
                    document.querySelectorAll('.student-item').forEach(item => {
                        item.classList.remove('selected');
                    });
                    
                    // Add selected class to clicked
                    event.currentTarget.classList.add('selected');
                    
                    // Check the radio
                    document.getElementById('student_' + id).checked = true;
                    selectedStudentId = id;
                    
                    // Enable the button
                    document.getElementById('selectStudentBtn').disabled = false;
                }
                
                // Search functionality
                document.getElementById('studentSearch').addEventListener('keyup', function() {
                    let filter = this.value.toLowerCase();
                    let items = document.querySelectorAll('.student-item');
                    
                    items.forEach(item => {
                        let text = item.innerText.toLowerCase();
                        if(text.includes(filter)) {
                            item.style.display = 'flex';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            </script>
            
            <?php endif; ?>
        </div>
    </div>

    <!-- ==================== VIEW ATTENDANCE MODAL ==================== -->
    <div id="viewAttendanceModal" class="modal-modern">
        <div class="modal-content-modern">
            <div class="modal-header-modern">
                <h2><i class="fas fa-eye"></i> Attendance Report</h2>
                <div class="modal-close-modern" onclick="closeModal('viewAttendanceModal')">
                    <i class="fas fa-times"></i>
                </div>
            </div>

            <div style="max-height: 500px; overflow-y: auto;">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Roll No</th>
                            <th>Name</th>
                            <th>Section</th>
                            <th>Total</th>
                            <th>Present</th>
                            <th>Absent</th>
                            <th>%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $attendance_report->data_seek(0);
                        while($row = $attendance_report->fetch_assoc()): 
                            $percentage = $row['total_classes'] > 0 ? round(($row['total_present'] / $row['total_classes']) * 100, 2) : 0;
                            $badge_class = $percentage >= 75 ? 'badge-success' : ($percentage >= 50 ? 'badge-warning' : 'badge-danger');
                        ?>
                        <tr>
                            <td><?php echo $row['roll_no']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['section']; ?></td>
                            <td><?php echo $row['total_classes'] ?: 0; ?></td>
                            <td style="color: #10b981;"><?php echo $row['total_present'] ?: 0; ?></td>
                            <td style="color: #ef4444;"><?php echo $row['total_absent'] ?: 0; ?></td>
                            <td>
                                <span class="badge-modern <?php echo $badge_class; ?>">
                                    <?php echo $percentage; ?>%
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ==================== MONTHLY REPORT MODAL ==================== -->
    <div id="monthlyReportModal" class="modal-modern">
        <div class="modal-content-modern">
            <div class="modal-header-modern">
                <h2><i class="fas fa-chart-line"></i> Monthly Report - <?php echo date('F Y'); ?></h2>
                <div class="modal-close-modern" onclick="closeModal('monthlyReportModal')">
                    <i class="fas fa-times"></i>
                </div>
            </div>

            <div style="max-height: 500px; overflow-y: auto;">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Roll No</th>
                            <th>Name</th>
                            <th>Section</th>
                            <th>Classes</th>
                            <th>Present</th>
                            <th>Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $monthly_report->data_seek(0);
                        while($row = $monthly_report->fetch_assoc()): 
                            $percentage = $row['total_classes'] > 0 ? round(($row['total_present'] / $row['total_classes']) * 100, 2) : 0;
                        ?>
                        <tr>
                            <td><?php echo $row['roll_no']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['section']; ?></td>
                            <td><?php echo $row['total_classes'] ?: 0; ?></td>
                            <td><?php echo $row['total_present'] ?: 0; ?></td>
                            <td style="width: 200px;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <div class="progress-modern">
                                        <div class="progress-fill" style="width: <?php echo $percentage; ?>%;"></div>
                                    </div>
                                    <span style="font-size: 0.85rem; color: white;"><?php echo $percentage; ?>%</span>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ==================== TIMETABLE MODAL ==================== -->
    <div id="timetableModal" class="modal-modern">
        <div class="modal-content-modern">
            <div class="modal-header-modern">
                <h2><i class="fas fa-calendar-alt"></i> My Weekly Timetable</h2>
                <div class="modal-close-modern" onclick="closeModal('timetableModal')">
                    <i class="fas fa-times"></i>
                </div>
            </div>

            <div class="timetable-grid">
                <?php
                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                foreach($days as $day):
                    $day_periods = [];
                    $employee_timetable->data_seek(0);
                    while($period = $employee_timetable->fetch_assoc()) {
                        if($period['day_of_week'] == $day) {
                            $day_periods[] = $period;
                        }
                    }
                ?>
                <div class="timetable-day">
                    <h4><?php echo $day; ?></h4>
                    <?php if(!empty($day_periods)): ?>
                        <?php foreach($day_periods as $period): ?>
                        <div class="timetable-period">
                            <strong>Period <?php echo $period['period']; ?></strong><br>
                            <?php echo htmlspecialchars($period['subject_code']); ?><br>
                            <?php echo htmlspecialchars($period['subject_name']); ?><br>
                            <small>Sec <?php echo htmlspecialchars($period['section']); ?></small>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="opacity: 0.3; text-align: center; padding: 1rem;">
                            <small>No classes</small>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- ==================== SUBJECTS MODAL ==================== -->
    <div id="subjectsModal" class="modal-modern">
        <div class="modal-content-modern">
            <div class="modal-header-modern">
                <h2><i class="fas fa-book-open"></i> My Assigned Subjects</h2>
                <div class="modal-close-modern" onclick="closeModal('subjectsModal')">
                    <i class="fas fa-times"></i>
                </div>
            </div>

            <div class="subject-grid">
                <?php
                $assigned_subjects->data_seek(0);
                while($subject = $assigned_subjects->fetch_assoc()):
                ?>
                <div class="subject-card">
                    <div class="subject-code"><?php echo htmlspecialchars($subject['subject_code']); ?></div>
                    <div class="subject-name"><?php echo htmlspecialchars($subject['subject_name']); ?></div>
                    <div class="subject-meta">
                        <span><i class="fas fa-layer-group"></i> Sem <?php echo $subject['semester']; ?></span>
                        <span><i class="fas fa-users"></i> Sec <?php echo $subject['section']; ?></span>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <script>
        // Create floating particles
        function createParticles() {
            const container = document.getElementById('particles-container');
            for (let i = 0; i < 20; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.width = Math.random() * 10 + 5 + 'px';
                particle.style.height = particle.style.width;
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 10 + 's';
                particle.style.animationDuration = Math.random() * 10 + 15 + 's';
                container.appendChild(particle);
            }
        }

        // Function to update total for a specific student
        function updateTotal(studentId) {
            const written = parseFloat(document.querySelector(`input[name="written[${studentId}]"]`).value) || 0;
            const assignment = parseFloat(document.querySelector(`input[name="assignment[${studentId}]"]`).value) || 0;
            const dinamic = parseFloat(document.querySelector(`input[name="dinamic[${studentId}]"]`).value) || 0;
            
            const total = written + assignment + dinamic;
            const totalField = document.getElementById(`total-${studentId}`);
            if(totalField) totalField.value = total;
            
            // Validate max limits
            if (written > 40) {
                alert('Written marks cannot exceed 40');
                document.querySelector(`input[name="written[${studentId}]"]`).value = 40;
                updateTotal(studentId);
            }
            if (assignment > 5) {
                alert('Assignment marks cannot exceed 5');
                document.querySelector(`input[name="assignment[${studentId}]"]`).value = 5;
                updateTotal(studentId);
            }
            if (dinamic > 5) {
                alert('Dinamic marks cannot exceed 5');
                document.querySelector(`input[name="dinamic[${studentId}]"]`).value = 5;
                updateTotal(studentId);
            }
            if (total > 50) {
                alert('Total marks cannot exceed 50');
                document.querySelector(`input[name="written[${studentId}]"]`).value = 40;
                document.querySelector(`input[name="assignment[${studentId}]"]`).value = 5;
                document.querySelector(`input[name="dinamic[${studentId}]"]`).value = 5;
                updateTotal(studentId);
            }
        }

        // Function to calculate totals for all students
        function calculateAllTotals() {
            const writtenInputs = document.querySelectorAll('.marks-input-written');
            writtenInputs.forEach(input => {
                const studentId = input.getAttribute('data-student');
                if(studentId) updateTotal(studentId);
            });
        }

        // Modal Functions
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
            document.body.style.overflow = 'auto';
            
            // If closing marks modal and it's showing students, redirect to clear session
            if(modalId === 'marksModal' && <?php echo $show_marks_students ? 'true' : 'false'; ?>) {
                window.location.href = window.location.pathname;
            }
            
            // If closing attendance modal and it's showing students, redirect to clear session
            if(modalId === 'attendanceModal' && <?php echo $show_students ? 'true' : 'false'; ?>) {
                window.location.href = window.location.pathname;
            }
            
            // If closing fee modal and a student is selected, redirect to clear session
            if(modalId === 'feeModal' && <?php echo isset($_SESSION['fee_student_id']) ? 'true' : 'false'; ?>) {
                window.location.href = window.location.pathname + '?cancel_fee=1';
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-modern.active').forEach(modal => {
                    modal.classList.remove('active');
                    
                    // If closing marks modal and it's showing students, redirect to clear session
                    if(modal.id === 'marksModal' && <?php echo $show_marks_students ? 'true' : 'false'; ?>) {
                        window.location.href = window.location.pathname;
                    }
                    
                    // If closing attendance modal and it's showing students, redirect to clear session
                    if(modal.id === 'attendanceModal' && <?php echo $show_students ? 'true' : 'false'; ?>) {
                        window.location.href = window.location.pathname;
                    }
                    
                    // If closing fee modal and a student is selected, redirect to clear session
                    if(modal.id === 'feeModal' && <?php echo isset($_SESSION['fee_student_id']) ? 'true' : 'false'; ?>) {
                        window.location.href = window.location.pathname + '?cancel_fee=1';
                    }
                });
                document.body.style.overflow = 'auto';
            }
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal-modern')) {
                event.target.classList.remove('active');
                
                // If closing marks modal and it's showing students, redirect to clear session
                if(event.target.id === 'marksModal' && <?php echo $show_marks_students ? 'true' : 'false'; ?>) {
                    window.location.href = window.location.pathname;
                }
                
                // If closing attendance modal and it's showing students, redirect to clear session
                if(event.target.id === 'attendanceModal' && <?php echo $show_students ? 'true' : 'false'; ?>) {
                    window.location.href = window.location.pathname;
                }
                
                // If closing fee modal and a student is selected, redirect to clear session
                if(event.target.id === 'feeModal' && <?php echo isset($_SESSION['fee_student_id']) ? 'true' : 'false'; ?>) {
                    window.location.href = window.location.pathname + '?cancel_fee=1';
                }
                document.body.style.overflow = 'auto';
            }
        }

        // Theme toggle
        function toggleTheme() {
            document.body.classList.toggle('light-theme');
            const icon = document.querySelector('.theme-toggle i');
            if (icon.classList.contains('fa-moon')) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        }

        // Initialize
        window.addEventListener('load', function() {
            createParticles();
        });

        // Auto-hide alerts
        setTimeout(() => {
            document.querySelectorAll('.alert-modern').forEach(alert => {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
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