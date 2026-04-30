<?php
session_start();
include "../config.php";

// ✅ Check admin login
if (!isset($_SESSION['admin'])) {
    header("Location: ../root.php");
    exit();
}

// ==================== DASHBOARD STATS ====================
$totalStudents = $conn->query("SELECT COUNT(*) as total FROM students")->fetch_assoc()['total'];
$totalCollected = $conn->query("SELECT SUM(amount) as total FROM payments")->fetch_assoc()['total'] ?? 0;
$totalFees = $conn->query("SELECT SUM(total_fee) as total FROM students")->fetch_assoc()['total'] ?? 0;
$totalDue = $totalFees - $totalCollected;

$totalEmployee = $conn->query("SELECT COUNT(*) as total FROM employee")->fetch_assoc()['total'];
$totalSalary = $conn->query("SELECT SUM(salary) as total FROM employee")->fetch_assoc()['total'] ?? 0;

// ==================== HOD STATS ====================
$totalHOD = $conn->query("SELECT COUNT(*) as total FROM hod")->fetch_assoc()['total'];

// ==================== FETCH ALL DATA ====================
$students = $conn->query("SELECT * FROM students ORDER BY roll_no ASC");
$employees = $conn->query("SELECT * FROM employee ORDER BY emp_id ASC");
$hods = $conn->query("SELECT * FROM hod ORDER BY department ASC");

// ==================== HANDLE DELETIONS ====================
if(isset($_GET['delete_student'])){
    $id = intval($_GET['delete_student']);
    
    // First delete related records to avoid foreign key constraints
    $conn->query("DELETE FROM payments WHERE student_id=$id");
    $conn->query("DELETE FROM attendance WHERE student_id=$id");
    $conn->query("DELETE FROM internal_marks WHERE student_id=$id");
    
    // Then delete the student using correct column name 'id'
    if($conn->query("DELETE FROM students WHERE id=$id")){
        header("Location: dashboard.php?msg=student_deleted");
    } else {
        header("Location: dashboard.php?msg=student_delete_error");
    }
    exit();
}

if(isset($_GET['delete_employee'])){
    $emp_id = $conn->real_escape_string($_GET['delete_employee']);
    
    // Delete related records if any
    $conn->query("DELETE FROM employee_subjects WHERE employee_id='$emp_id'");
    $conn->query("DELETE FROM timetable WHERE employee_id='$emp_id'");
    
    // Delete employee
    if($conn->query("DELETE FROM employee WHERE emp_id='$emp_id'")){
        header("Location: dashboard.php?msg=employee_deleted");
    } else {
        header("Location: dashboard.php?msg=employee_delete_error");
    }
    exit();
}

if(isset($_GET['delete_hod'])){
    $hod_id = intval($_GET['delete_hod']);
    
    // Delete HOD
    if($conn->query("DELETE FROM hod WHERE id=$hod_id")){
        header("Location: dashboard.php?msg=hod_deleted");
    } else {
        header("Location: dashboard.php?msg=hod_delete_error");
    }
    exit();
}

// ==================== HANDLE ADD/EDIT OPERATIONS ====================

// Add Student (with section)
if(isset($_POST['add_student'])){
    $roll_no = trim($_POST['roll_no']);
    $name = trim($_POST['name']);
    $dob = $_POST['dob'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $total_fee = $_POST['total_fee'];
    $course = $_POST['course'];
    $department = $_POST['department'];
    $semester = $_POST['semester'];
    $section = $_POST['section'];
    $phone = $_POST['phone'];
    $ssc_marks = $_POST['ssc_marks'];
    $polycet_rank = $_POST['polycet_rank'];
    $category = $_POST['category'];
    $father_name = $_POST['father_name'];
    $mother_name = $_POST['mother_name'];
    $blood_group = $_POST['blood_group'];
    $permanent_address = $_POST['permanent_address'];
    $local_address = $_POST['local_address'];
    
    
    $photo_name = "";
    if(isset($_FILES['photo']) && $_FILES['photo']['error'] == 0){
        $target_dir = "../uploads/";
        if(!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $photo_name = time() . "_" . basename($_FILES["photo"]["name"]);
        move_uploaded_file($_FILES["photo"]["tmp_name"], $target_dir . $photo_name);
    }
    
    $stmt = $conn->prepare("INSERT INTO students (roll_no, name, dob, email, password, total_fee, course, department, semester, section, phone, ssc_marks, polycet_rank, category, father_name, mother_name, blood_group, permanent_address, local_address, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssississssss", $roll_no, $name, $dob, $email, $password, $total_fee, $course, $department, $semester, $section, $phone, $ssc_marks, $polycet_rank, $category, $father_name, $mother_name, $blood_group, $permanent_address, $local_address, $photo_name);
    $stmt->execute();
    $student_success = "Student added successfully!";
}

// Edit Student
if(isset($_POST['edit_student'])){
    $id = $_POST['student_id'];
    $roll_no = trim($_POST['roll_no']);
    $name = trim($_POST['name']);
    $dob = $_POST['dob'];
    $email = $_POST['email'];
    $total_fee = $_POST['total_fee'];
    $course = $_POST['course'];
    $department = $_POST['department'];
    $semester = $_POST['semester'];
    $section = $_POST['section'];
    $phone = $_POST['phone'];
    $ssc_marks = $_POST['ssc_marks'];
    $polycet_rank = $_POST['polycet_rank'];
    $category = $_POST['category'];
    $father_name = $_POST['father_name'];
    $mother_name = $_POST['mother_name'];
    $blood_group = $_POST['blood_group'];
    $permanent_address = $_POST['permanent_address'];
    $local_address = $_POST['local_address'];
    
    $photo_name = $_POST['old_photo'];
    if(isset($_FILES['photo']) && $_FILES['photo']['error'] == 0){
        $target_dir = "../uploads/";
        $photo_name = time() . "_" . basename($_FILES["photo"]["name"]);
        move_uploaded_file($_FILES["photo"]["tmp_name"], $target_dir . $photo_name);
    }
    
    $stmt = $conn->prepare("UPDATE students SET roll_no=?, name=?, dob=?, email=?, total_fee=?, course=?, department=?, semester=?, section=?, phone=?, ssc_marks=?, polycet_rank=?, category=?, father_name=?, mother_name=?, blood_group=?, permanent_address=?, local_address=?, photo=? WHERE id=?");
    $stmt->bind_param("ssssssssssiisssssssi", $roll_no, $name, $dob, $email, $total_fee, $course, $department, $semester, $section, $phone, $ssc_marks, $polycet_rank, $category, $father_name, $mother_name, $blood_group, $permanent_address, $local_address, $photo_name, $id);
    $stmt->execute();
    $student_update_success = "Student updated successfully!";
}

// Add Employee
if(isset($_POST['add_employee'])){
    $empid = $_POST['empid'];
    $name = $_POST['name'];
    $dob = $_POST['dob'];
    $department = $_POST['department'];
    $phone = $_POST['phone'];
    $category = $_POST['category'];
    $father = $_POST['father_name'];
    $blood = $_POST['blood_group'];
    $address = $_POST['permanent_address'];
    $prof = $_POST['profession'];
    $sal = intval($_POST['salary']);
    $email = $_POST['email'];
    $user = $_POST['username'];
    $pass = $_POST['password'];
    
    $photoName = "";
    if(!empty($_FILES['photo']['name'])){
        $photoName = time() . "_" . $_FILES['photo']['name'];
        move_uploaded_file($_FILES['photo']['tmp_name'], "../uploads/" . $photoName);
    }
    
    $stmt = $conn->prepare("INSERT INTO employee (emp_id, name, dob, department, phone, category, father_name, blood_group, permanent_address, profession, salary, email, username, password, photo) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("ssssssssssissss", $empid, $name, $dob, $department, $phone, $category, $father, $blood, $address, $prof, $sal, $email, $user, $pass, $photoName);
    $stmt->execute();
    $employee_success = "Employee added successfully!";
}

// Edit Employee
if(isset($_POST['edit_employee'])){
    $empid = $_POST['emp_id'];
    $name = $_POST['name'];
    $dob = $_POST['dob'];
    $department = $_POST['department'];
    $phone = $_POST['phone'];
    $category = $_POST['category'];
    $father = $_POST['father_name'];
    $blood = $_POST['blood_group'];
    $address = $_POST['permanent_address'];
    $prof = $_POST['profession'];
    $sal = intval($_POST['salary']);
    $email = $_POST['email'];
    $user = $_POST['username'];
    
    $photoName = $_POST['old_photo'];
    if(!empty($_FILES['photo']['name'])){
        $photoName = time() . "_" . $_FILES['photo']['name'];
        move_uploaded_file($_FILES['photo']['tmp_name'], "../uploads/" . $photoName);
    }
    
    $stmt = $conn->prepare("UPDATE employee SET name=?, dob=?, department=?, phone=?, category=?, father_name=?, blood_group=?, permanent_address=?, profession=?, salary=?, email=?, username=?, photo=? WHERE emp_id=?");
    $stmt->bind_param("ssssssssssssss", $name, $dob, $department, $phone, $category, $father, $blood, $address, $prof, $sal, $email, $user, $photoName, $empid);
    $stmt->execute();
    $employee_update_success = "Employee updated successfully!";
}

// Add HOD
if(isset($_POST['add_hod'])){
    $hod_id = $_POST['hod_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $department = $_POST['department'];
    $qualification = $_POST['qualification'];
    $experience = $_POST['experience'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $photoName = "";
    if(!empty($_FILES['photo']['name'])){
        $photoName = time() . "_" . $_FILES['photo']['name'];
        move_uploaded_file($_FILES['photo']['tmp_name'], "../uploads/" . $photoName);
    }
    
    $stmt = $conn->prepare("INSERT INTO hod (hod_id, name, email, phone, department, qualification, experience, username, password, photo) VALUES (?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("ssssssisss", $hod_id, $name, $email, $phone, $department, $qualification, $experience, $username, $password, $photoName);
    $stmt->execute();
    $hod_success = "HOD added successfully!";
}

// Edit HOD
if(isset($_POST['edit_hod'])){
    $id = $_POST['hod_id'];
    $hod_id = $_POST['hod_id_code'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $department = $_POST['department'];
    $qualification = $_POST['qualification'];
    $experience = $_POST['experience'];
    $username = $_POST['username'];
    
    $photoName = $_POST['old_photo'];
    if(!empty($_FILES['photo']['name'])){
        $photoName = time() . "_" . $_FILES['photo']['name'];
        move_uploaded_file($_FILES['photo']['tmp_name'], "../uploads/" . $photoName);
    }
    
    $stmt = $conn->prepare("UPDATE hod SET hod_id=?, name=?, email=?, phone=?, department=?, qualification=?, experience=?, username=?, photo=? WHERE id=?");
    $stmt->bind_param("ssssssissi", $hod_id, $name, $email, $phone, $department, $qualification, $experience, $username, $photoName, $id);
    $stmt->execute();
    $hod_update_success = "HOD updated successfully!";
}

// Get single record for editing
$edit_student = null;
$edit_employee = null;
$edit_hod = null;

if(isset($_GET['edit_student_id'])){
    $id = intval($_GET['edit_student_id']);
    $result = $conn->query("SELECT * FROM students WHERE id=$id");
    $edit_student = $result->fetch_assoc();
}

if(isset($_GET['edit_employee_id'])){
    $emp_id = $_GET['edit_employee_id'];
    $result = $conn->query("SELECT * FROM employee WHERE emp_id='$emp_id'");
    $edit_employee = $result->fetch_assoc();
}

if(isset($_GET['edit_hod_id'])){
    $id = intval($_GET['edit_hod_id']);
    $result = $conn->query("SELECT * FROM hod WHERE id=$id");
    $edit_hod = $result->fetch_assoc();
}

// Get single record for viewing
$view_student = null;
$view_employee = null;
$view_hod = null;

if(isset($_GET['view_student_id'])){
    $id = intval($_GET['view_student_id']);
    $result = $conn->query("SELECT * FROM students WHERE id=$id");
    $view_student = $result->fetch_assoc();
}

if(isset($_GET['view_employee_id'])){
    $emp_id = $_GET['view_employee_id'];
    $result = $conn->query("SELECT * FROM employee WHERE emp_id='$emp_id'");
    $view_employee = $result->fetch_assoc();
}

if(isset($_GET['view_hod_id'])){
    $id = intval($_GET['view_hod_id']);
    $result = $conn->query("SELECT * FROM hod WHERE id=$id");
    $view_hod = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | A.A.N.M.&.V.V.R.S.R. POLYTECHNIC</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background */
        .bg-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.3;
            z-index: 0;
            animation: orbFloat 20s ease-in-out infinite;
        }

        .orb-1 {
            width: 500px;
            height: 500px;
            background: #ff6b6b;
            top: -200px;
            right: -200px;
        }

        .orb-2 {
            width: 400px;
            height: 400px;
            background: #4facfe;
            bottom: -150px;
            left: -150px;
            animation-delay: -5s;
        }

        .orb-3 {
            width: 300px;
            height: 300px;
            background: #43e97b;
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
            z-index: 0;
            animation: gridMove 20s linear infinite;
        }

        @keyframes gridMove {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        /* Main Container */
        .dashboard {
            max-width: 1600px;
            margin: 0 auto;
            padding: 20px;
            position: relative;
            z-index: 2;
        }

        /* Glass Navigation */
        .glass-nav {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 30px;
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            animation: slideDown 0.8s ease;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

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

        .admin-info {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .admin-avatar {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #ffd93d, #ff6b6b);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            transform: rotate(-5deg);
            transition: transform 0.3s ease;
        }

        .admin-avatar:hover {
            transform: rotate(0deg) scale(1.1);
        }

        .admin-details h2 {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #fff, #a5b4fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .admin-details p {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 50px;
            padding: 0.7rem 1.5rem;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logout-btn:hover {
            background: #ff4757;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(255, 71, 87, 0.4);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 30px;
            padding: 1.5rem;
            color: white;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            animation: fadeInUp 0.6s ease;
            animation-fill-mode: both;
        }

        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.15s; }
        .stat-card:nth-child(3) { animation-delay: 0.2s; }
        .stat-card:nth-child(4) { animation-delay: 0.25s; }
        .stat-card:nth-child(5) { animation-delay: 0.3s; }
        .stat-card:nth-child(6) { animation-delay: 0.35s; }
        .stat-card:nth-child(7) { animation-delay: 0.4s; }

        .stat-card:hover {
            transform: translateY(-10px) scale(1.02);
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.5);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #ffd93d, #ff6b6b);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.3rem;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Action Grid */
        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
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
            color: white;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            animation: fadeInScale 0.6s ease;
            animation-fill-mode: both;
        }

        .action-card:nth-child(1) { animation-delay: 0.15s; }
        .action-card:nth-child(2) { animation-delay: 0.25s; }
        .action-card:nth-child(3) { animation-delay: 0.35s; }
        .action-card:nth-child(4) { animation-delay: 0.45s; }

        .action-card:hover {
            transform: translateY(-8px) scale(1.02);
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .action-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #ffd93d, #ff6b6b);
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
            font-size: 0.75rem;
            opacity: 0.7;
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

        /* Success Message */
        .alert-success {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 15px;
            padding: 1rem;
            margin-bottom: 1rem;
            color: #10b981;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Search Section */
        .search-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .search-wrapper {
            position: relative;
        }

        .search-icon {
            position: absolute;
            left: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.5);
            font-size: 1.2rem;
        }

        .search-field {
            width: 100%;
            padding: 1.2rem 1.2rem 1.2rem 3.5rem;
            background: rgba(255, 255, 255, 0.15);
            border: 2px solid transparent;
            border-radius: 15px;
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .search-field:focus {
            outline: none;
            border-color: #ffd93d;
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 40px rgba(255, 217, 61, 0.3);
        }

        .search-field::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        /* Tab Navigation */
        .tab-container {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .tab-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            padding: 0.8rem 2rem;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .tab-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
        }

        .tab-btn.active {
            background: linear-gradient(135deg, #ffd93d, #ff6b6b);
            color: #333;
            border-color: transparent;
        }

        /* Table Section */
        .table-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 30px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            display: none;
            animation: slideUp 0.6s ease;
        }

        .table-section.active {
            display: block;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .section-header h3 {
            font-size: 1.3rem;
            font-weight: 600;
            color: white;
        }

        .section-header i {
            margin-right: 0.5rem;
            color: #ffd93d;
        }

        .add-btn {
            background: linear-gradient(135deg, #00b09b, #96c93d);
            border: none;
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .add-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 176, 155, 0.4);
        }

        /* Modern Table */
        .table-responsive {
            overflow-x: auto;
        }

        .modern-table {
            width: 100%;
            border-collapse: collapse;
        }

        .modern-table th {
            text-align: left;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.15);
            color: white;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .modern-table td {
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }

        .modern-table tbody tr {
            transition: all 0.3s ease;
        }

        .modern-table tbody tr:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: scale(1.01);
        }

        .action-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-success {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
        }

        .badge-warning {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
        }

        .badge-danger {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        .btn-icon {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            padding: 0.4rem 0.8rem;
            color: white;
            text-decoration: none;
            font-size: 0.8rem;
            transition: all 0.3s ease;
            margin: 0 2px;
            display: inline-block;
            cursor: pointer;
        }

        .btn-icon:hover {
            background: #ffd93d;
            color: #333;
            transform: translateY(-2px);
        }

        .btn-icon.delete:hover {
            background: #ef4444;
            color: white;
        }

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(15px);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal-overlay.active {
            display: flex;
            opacity: 1;
        }

        .modal-container {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            border-radius: 40px;
            padding: 2.5rem;
            max-width: 900px;
            width: 90%;
            max-height: 85vh;
            overflow-y: auto;
            position: relative;
            transform: scale(0.9) translateY(20px);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 50px 100px rgba(0, 0, 0, 0.5);
        }

        .modal-overlay.active .modal-container {
            transform: scale(1) translateY(0);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        }

        .modal-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: white;
        }

        .modal-header i {
            color: #ffd93d;
            margin-right: 0.5rem;
        }

        .modal-close {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .modal-close:hover {
            background: #ef4444;
            transform: rotate(90deg);
        }

        /* Profile View Styles */
        .profile-view {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 2rem;
        }

        .profile-view-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 4px solid #ffd93d;
            object-fit: cover;
            margin-bottom: 1rem;
        }

        .profile-view-name {
            font-size: 1.8rem;
            font-weight: 700;
            color: white;
        }

        .profile-view-badge {
            background: rgba(255, 217, 61, 0.2);
            color: #ffd93d;
            padding: 0.3rem 1rem;
            border-radius: 50px;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
        }

        .detail-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 1rem;
        }

        .detail-label {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.3rem;
        }

        .detail-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
        }

        /* Form Styles */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid transparent;
            border-radius: 15px;
            color: white;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #ffd93d;
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 30px rgba(255, 217, 61, 0.3);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .form-control option {
            background: #1e293b;
            color: white;
        }

        .btn-submit {
            background: linear-gradient(135deg, #00b09b, #96c93d);
            border: none;
            border-radius: 15px;
            padding: 1rem 2rem;
            color: white;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(0, 176, 155, 0.4);
        }

        /* Fee Status Styles */
        .fee-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .fee-paid { background: rgba(16, 185, 129, 0.2); color: #10b981; }
        .fee-partial { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
        .fee-pending { background: rgba(239, 68, 68, 0.2); color: #ef4444; }

        /* Responsive */
        @media (max-width: 768px) {
            .glass-nav {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .admin-info {
                flex-direction: column;
            }

            .action-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .modal-container {
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

            .tab-container {
                justify-content: center;
            }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #ffd93d, #ff6b6b);
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-orb orb-1"></div>
    <div class="bg-orb orb-2"></div>
    <div class="bg-orb orb-3"></div>
    <div class="grid-pattern"></div>

    <!-- Main Dashboard -->
    <div class="dashboard">
        <!-- Navigation -->
        <nav class="glass-nav">
            <div class="admin-info">
                <div class="admin-avatar">
                    <i class="fas fa-crown"></i>
                </div>
                <div class="admin-details">
                    <h2>A.A.N.M & V.V.R.S.R POLYTECHNIC(Administrator)</h2>
                    <p>College Management System</p>
                </div>
            </div>
            <a href="../logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </nav>

        <!-- Success/Error Messages -->
        <?php if(isset($_GET['msg'])): ?>
            <?php if($_GET['msg'] == 'student_deleted'): ?>
            <div class="alert-success">
                <i class="fas fa-check-circle"></i> Student deleted successfully!
            </div>
            <?php elseif($_GET['msg'] == 'employee_deleted'): ?>
            <div class="alert-success">
                <i class="fas fa-check-circle"></i> Employee deleted successfully!
            </div>
            <?php elseif($_GET['msg'] == 'hod_deleted'): ?>
            <div class="alert-success">
                <i class="fas fa-check-circle"></i> HOD deleted successfully!
            </div>
            <?php elseif($_GET['msg'] == 'student_delete_error'): ?>
            <div class="alert-success" style="background: rgba(239,68,68,0.2); color:#ef4444;">
                <i class="fas fa-exclamation-circle"></i> Error deleting student!
            </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if(isset($student_success)): ?>
        <div class="alert-success">
            <i class="fas fa-check-circle"></i> <?php echo $student_success; ?>
        </div>
        <?php endif; ?>

        <?php if(isset($employee_success)): ?>
        <div class="alert-success">
            <i class="fas fa-check-circle"></i> <?php echo $employee_success; ?>
        </div>
        <?php endif; ?>

        <?php if(isset($hod_success)): ?>
        <div class="alert-success">
            <i class="fas fa-check-circle"></i> <?php echo $hod_success; ?>
        </div>
        <?php endif; ?>

        <!-- Statistics Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-number"><?php echo $totalStudents; ?></div>
                <div class="stat-label">Total Students</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="stat-number"><?php echo $totalEmployee; ?></div>
                <div class="stat-label">Total Employees</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-crown"></i>
                </div>
                <div class="stat-number"><?php echo $totalHOD; ?></div>
                <div class="stat-label">Department Heads</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="stat-number">₹<?php echo number_format($totalFees); ?></div>
                <div class="stat-label">Total Fees</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-number">₹<?php echo number_format($totalCollected); ?></div>
                <div class="stat-label">Collected</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-number">₹<?php echo number_format($totalDue); ?></div>
                <div class="stat-label">Due Amount</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="stat-number">₹<?php echo number_format($totalSalary); ?></div>
                <div class="stat-label">Total Salary</div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-grid">
            <div class="action-card" onclick="openModal('addStudentModal')">
                <div class="action-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="action-title">Add Student</div>
                <div class="action-subtitle">Register new student</div>
            </div>
            
            <div class="action-card" onclick="openModal('addEmployeeModal')">
                <div class="action-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="action-title">Add Employee</div>
                <div class="action-subtitle">Register new employee</div>
            </div>
            
            <div class="action-card" onclick="openModal('addHODModal')">
                <div class="action-icon">
                    <i class="fas fa-crown"></i>
                </div>
                <div class="action-title">Add HOD</div>
                <div class="action-subtitle">Department head</div>
            </div>
            
            <div class="action-card" onclick="openModal('feeStatusModal')">
                <div class="action-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="action-title">Fee Status</div>
                <div class="action-subtitle">View collection</div>
            </div>
        </div>

        <!-- Search Section -->
        <div class="search-section">
            <div class="search-wrapper">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="globalSearch" class="search-field" placeholder="Search students, employees, or HODs...">
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="tab-container">
            <div class="tab-btn active" onclick="switchTab('students')">
                <i class="fas fa-users"></i> Students
            </div>
            <div class="tab-btn" onclick="switchTab('employees')">
                <i class="fas fa-user-tie"></i> Employees
            </div>
            <div class="tab-btn" onclick="switchTab('hods')">
                <i class="fas fa-crown"></i> HODs
            </div>
        </div>

        <!-- Students Table Section -->
        <div id="students-section" class="table-section active">
            <div class="section-header">
                <h3><i class="fas fa-users"></i> Student List</h3>
                <button class="add-btn" onclick="openModal('addStudentModal')">
                    <i class="fas fa-plus"></i> Add Student
                </button>
            </div>
            
            <div class="table-responsive">
                <table class="modern-table" id="studentsTable">
                    <thead>
                        <tr>
                            <th>Roll No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Sem/Sec</th>
                            <th>Total Fee</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $students->data_seek(0);
                        while($row = $students->fetch_assoc()): 
                        ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['roll_no']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['department'] ?? $row['course']); ?></td>
                            <td><?php echo ($row['semester'] ?? 'N/A') . ' / ' . ($row['section'] ?? 'N/A'); ?></td>
                            <td>₹<?php echo number_format($row['total_fee']); ?></td>
                            <td>
                                <button class="btn-icon" onclick="viewStudent(<?php echo $row['id']; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-icon" onclick="editStudent(<?php echo $row['id']; ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?delete_student=<?php echo $row['id']; ?>" class="btn-icon delete" onclick="return confirm('Delete this student?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Employees Table Section -->
        <div id="employees-section" class="table-section">
            <div class="section-header">
                <h3><i class="fas fa-user-tie"></i> Employee List</h3>
                <button class="add-btn" onclick="openModal('addEmployeeModal')">
                    <i class="fas fa-plus"></i> Add Employee
                </button>
            </div>
            
            <div class="table-responsive">
                <table class="modern-table" id="employeesTable">
                    <thead>
                        <tr>
                            <th>Emp ID</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Profession</th>
                            <th>Salary</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $employees->data_seek(0);
                        while($row = $employees->fetch_assoc()): 
                        ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['emp_id']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['department']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td><?php echo htmlspecialchars($row['profession']); ?></td>
                            <td>₹<?php echo number_format($row['salary']); ?></td>
                            <td>
                                <button class="btn-icon" onclick="viewEmployee('<?php echo $row['emp_id']; ?>')">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-icon" onclick="editEmployee('<?php echo $row['emp_id']; ?>')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?delete_employee=<?php echo $row['emp_id']; ?>" class="btn-icon delete" onclick="return confirm('Delete this employee?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- HODs Table Section -->
        <div id="hods-section" class="table-section">
            <div class="section-header">
                <h3><i class="fas fa-crown"></i> Department Heads (HODs)</h3>
                <button class="add-btn" onclick="openModal('addHODModal')">
                    <i class="fas fa-plus"></i> Add HOD
                </button>
            </div>
            
            <div class="table-responsive">
                <table class="modern-table" id="hodsTable">
                    <thead>
                        <tr>
                            <th>HOD ID</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Qualification</th>
                            <th>Experience</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $hods->data_seek(0);
                        while($row = $hods->fetch_assoc()): 
                        ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['hod_id']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><span class="badge-success" style="padding:0.3rem 0.8rem;"><?php echo htmlspecialchars($row['department']); ?></span></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td><?php echo htmlspecialchars($row['qualification']); ?></td>
                            <td><?php echo $row['experience']; ?> years</td>
                            <td>
                                <button class="btn-icon" onclick="viewHOD(<?php echo $row['id']; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-icon" onclick="editHOD(<?php echo $row['id']; ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?delete_hod=<?php echo $row['id']; ?>" class="btn-icon delete" onclick="return confirm('Delete this HOD?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ==================== ADD STUDENT MODAL ==================== -->
    <div id="addStudentModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h2><i class="fas fa-user-graduate"></i> Add New Student</h2>
                <div class="modal-close" onclick="closeModal('addStudentModal')">&times;</div>
            </div>
            
            <?php if(isset($student_success)): ?>
            <div class="alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $student_success; ?>
            </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Roll Number</label>
                        <input type="text" name="roll_no" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="dob" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Course</label>
                        <input type="text" name="course" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Department</label>
                        <select name="department" class="form-control" required>
                            <option value="">Select Department</option>
                            <option value="AIML">AIML</option>
                            <option value="CSE">CSE</option>
                            <option value="IOT">IOT</option>
                            <option value="ECE">ECE</option>
                            <option value="MECH">MECH</option>
                            <option value="CIVIL">CIVIL</option>
                            <option value="EEE">EEE</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Semester</label>
                        <select name="semester" class="form-control" required>
                            <option value="">Select Semester</option>
                            <option value="1">Semester 1</option>
                            <option value="2">Semester 2</option>
                            <option value="3">Semester 3</option>
                            <option value="4">Semester 4</option>
                            <option value="5">Semester 5</option>
                            <option value="6">Semester 6</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Section</label>
                        <select name="section" class="form-control" required>
                            <option value="">Select Section</option>
                            <option value="1A">Section 1A</option>
                            <option value="1B">Section 1B</option>
                            <option value="1C">Section 1C</option>
                            <option value="1D">Section 1D</option>
			   
                            <option value="2A">Section 2A</option>
                            <option value="2B">Section 2B</option>
                            <option value="2C">Section 2C</option>
                            <option value="2D">Section 2D</option>
 	 		 
                            <option value="3A">Section 3A</option>
                            <option value="3B">Section 3B</option>
                            <option value="3C">Section 3C</option>
                            <option value="3D">Section 3D</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Total Fee</label>
                        <input type="number" name="total_fee" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>SSC Marks</label>
                        <input type="number" name="ssc_marks" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Polycet Rank</label>
                        <input type="number" name="polycet_rank" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" class="form-control" required>
                            <option value="">Select Category</option>
                            <option>OC</option>
                            <option>BC</option>
                            <option>SC</option>
                            <option>ST</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Father's Name</label>
                        <input type="text" name="father_name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Mother's Name</label>
                        <input type="text" name="mother_name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Blood Group</label>
                        <select name="blood_group" class="form-control" required>
                            <option value="">Select Blood Group</option>
                            <option>A+</option><option>A-</option><option>B+</option><option>B-</option>
                            <option>O+</option><option>O-</option><option>AB+</option><option>AB-</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Permanent Address</label>
                        <textarea name="permanent_address" class="form-control" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Local Address</label>
                        <textarea name="local_address" class="form-control" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Photo</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                    </div>
                </div>
                
                <button type="submit" name="add_student" class="btn-submit">
                    <i class="fas fa-save"></i> Add Student
                </button>
            </form>
        </div>
    </div>

    <!-- ==================== ADD EMPLOYEE MODAL ==================== -->
    <div id="addEmployeeModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h2><i class="fas fa-user-tie"></i> Add New Employee</h2>
                <div class="modal-close" onclick="closeModal('addEmployeeModal')">&times;</div>
            </div>
            
            <?php if(isset($employee_success)): ?>
            <div class="alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $employee_success; ?>
            </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Employee ID</label>
                        <input type="text" name="empid" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="dob" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Department</label>
                        <select name="department" class="form-control" required>
                            <option value="">Select Department</option>
                            <option value="AIML">AIML</option>
                            <option value="CSE">CSE</option>
                              <option value="IOT">IOT</option>
                            <option value="ECE">ECE</option>
                            <option value="MECH">MECH</option>
                            <option value="CIVIL">CIVIL</option>
                            <option value="EEE">EEE</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Category</label>
                        <input type="text" name="category" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Father's Name</label>
                        <input type="text" name="father_name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Blood Group</label>
                        <select name="blood_group" class="form-control" required>
                            <option value="">Select Blood Group</option>
                            <option>A+</option><option>A-</option><option>B+</option><option>B-</option>
                            <option>O+</option><option>O-</option><option>AB+</option><option>AB-</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Permanent Address</label>
                        <textarea name="permanent_address" class="form-control" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Profession</label>
                        <input type="text" name="profession" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Salary</label>
                        <input type="number" name="salary" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Photo</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                    </div>
                </div>
                
                <button type="submit" name="add_employee" class="btn-submit">
                    <i class="fas fa-save"></i> Add Employee
                </button>
            </form>
        </div>
    </div>

    <!-- ==================== ADD HOD MODAL ==================== -->
    <div id="addHODModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h2><i class="fas fa-crown"></i> Add Department Head (HOD)</h2>
                <div class="modal-close" onclick="closeModal('addHODModal')">&times;</div>
            </div>
            
            <?php if(isset($hod_success)): ?>
            <div class="alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $hod_success; ?>
            </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label>HOD ID</label>
                        <input type="text" name="hod_id" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Department</label>
                        <select name="department" class="form-control" required>
                            <option value="">Select Department</option>
                            <option value="AIML">AIML</option>
                            <option value="CSE">CSE</option>
                              <option value="IOT">IOT</option>
                            <option value="ECE">ECE</option>
                            <option value="MECH">MECH</option>
                            <option value="CIVIL">CIVIL</option>
                            <option value="EEE">EEE</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Qualification</label>
                        <input type="text" name="qualification" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Experience (Years)</label>
                        <input type="number" name="experience" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Photo</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                    </div>
                </div>
                
                <button type="submit" name="add_hod" class="btn-submit">
                    <i class="fas fa-save"></i> Add HOD
                </button>
            </form>
        </div>
    </div>

    <!-- ==================== VIEW STUDENT MODAL ==================== -->
    <?php if($view_student): ?>
    <div id="viewStudentModal" class="modal-overlay active">
        <div class="modal-container">
            <div class="modal-header">
                <h2><i class="fas fa-user-graduate"></i> Student Details</h2>
                <div class="modal-close" onclick="closeModal('viewStudentModal'); window.location.href='dashboard.php'">&times;</div>
            </div>
            
            <div class="profile-view">
                <?php if(!empty($view_student['photo']) && file_exists("../uploads/".$view_student['photo'])): ?>
                    <img src="../uploads/<?php echo $view_student['photo']; ?>" class="profile-view-img">
                <?php else: ?>
                    <div style="width:150px; height:150px; border-radius:50%; background:linear-gradient(135deg,#ffd93d,#ff6b6b); display:flex; align-items:center; justify-content:center; font-size:3rem; margin-bottom:1rem;">
                        <?php echo strtoupper(substr($view_student['name'], 0, 2)); ?>
                    </div>
                <?php endif; ?>
                <div class="profile-view-name"><?php echo $view_student['name']; ?></div>
                <div class="profile-view-badge">Roll No: <?php echo $view_student['roll_no']; ?></div>
            </div>

            <div class="details-grid">
                <div class="detail-item">
                    <div class="detail-label">Date of Birth</div>
                    <div class="detail-value"><?php echo date('d M Y', strtotime($view_student['dob'])); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Email</div>
                    <div class="detail-value"><?php echo $view_student['email']; ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Phone</div>
                    <div class="detail-value"><?php echo $view_student['phone']; ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Course</div>
                    <div class="detail-value"><?php echo $view_student['course']; ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Department</div>
                    <div class="detail-value"><?php echo $view_student['department'] ?? 'N/A'; ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Semester / Section</div>
                    <div class="detail-value"><?php echo ($view_student['semester'] ?? 'N/A') . ' / ' . ($view_student['section'] ?? 'N/A'); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">SSC Marks</div>
                    <div class="detail-value"><?php echo $view_student['ssc_marks']; ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Polycet Rank</div>
                    <div class="detail-value"><?php echo $view_student['polycet_rank']; ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Category</div>
                    <div class="detail-value"><?php echo $view_student['category']; ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Father's Name</div>
                    <div class="detail-value"><?php echo $view_student['father_name']; ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Mother's Name</div>
                    <div class="detail-value"><?php echo $view_student['mother_name']; ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Blood Group</div>
                    <div class="detail-value"><?php echo $view_student['blood_group']; ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Permanent Address</div>
                    <div class="detail-value"><?php echo $view_student['permanent_address']; ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Local Address</div>
                    <div class="detail-value"><?php echo $view_student['local_address']; ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Total Fee</div>
                    <div class="detail-value">₹<?php echo number_format($view_student['total_fee']); ?></div>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button class="btn-submit" onclick="editStudent(<?php echo $view_student['id']; ?>)">
                    <i class="fas fa-edit"></i> Edit Student
                </button>
                <button class="btn-submit" style="background: linear-gradient(135deg, #ff6b6b, #ff4757);" onclick="window.location.href='?delete_student=<?php echo $view_student['id']; ?>'">
                    <i class="fas fa-trash"></i> Delete Student
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ==================== VIEW EMPLOYEE MODAL ==================== -->
    <?php if($view_employee): ?>
    <div id="viewEmployeeModal" class="modal-overlay active">
        <div class="modal-container">
            <div class="modal-header">
                <h2><i class="fas fa-user-tie"></i> Employee Details</h2>
                <div class="modal-close" onclick="closeModal('viewEmployeeModal'); window.location.href='dashboard.php'">&times;</div>
            </div>
            
            <div class="profile-view">
                <?php if(!empty($view_employee['photo']) && file_exists("../uploads/".$view_employee['photo'])): ?>
                    <img src="../uploads/<?php echo $view_employee['photo']; ?>" class="profile-view-img">
                <?php else: ?>
                    <div style="width:150px; height:150px; border-radius:50%; background:linear-gradient(135deg,#ffd93d,#ff6b6b); display:flex; align-items:center; justify-content:center; font-size:3rem; margin-bottom:1rem;">
                        <?php echo strtoupper(substr($view_employee['name'], 0, 2)); ?>
                    </div>
                <?php endif; ?>
                <div class="profile-view-name"><?php echo $view_employee['name']; ?></div>
                <div class="profile-view-badge"><?php echo $view_employee['profession']; ?></div>
            </div>

            <div class="details-grid">
                <div class="detail-item">
                    <div class="detail-label">Employee ID</div>
                    <div class="detail-value"><?php echo $view_employee['emp_id']; ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Date of Birth</div>
                    <div class="detail-value"><?php echo date('d M Y', strtotime($view_employee['dob'])); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Department</div>
                    <div class="detail-value"><?php echo $view_employee['department']; ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Phone</div>
                    <div class="detail-value"><?php echo $view_employee['phone']; ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Email</div>
                    <div class="detail-value"><?php echo $view_employee['email']; ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Category</div>
                    <div class="detail-value"><?php echo $view_employee['category']; ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Father's Name</div>
                    <div class="detail-value"><?php echo $view_employee['father_name']; ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Blood Group</div>
                    <div class="detail-value"><?php echo $view_employee['blood_group']; ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Address</div>
                    <div class="detail-value"><?php echo $view_employee['permanent_address']; ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Salary</div>
                    <div class="detail-value">₹<?php echo number_format($view_employee['salary']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Username</div>
                    <div class="detail-value"><?php echo $view_employee['username']; ?></div>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button class="btn-submit" onclick="editEmployee('<?php echo $view_employee['emp_id']; ?>')">
                    <i class="fas fa-edit"></i> Edit Employee
                </button>
                <button class="btn-submit" style="background: linear-gradient(135deg, #ff6b6b, #ff4757);" onclick="window.location.href='?delete_employee=<?php echo $view_employee['emp_id']; ?>'">
                    <i class="fas fa-trash"></i> Delete Employee
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ==================== VIEW HOD MODAL ==================== -->
    <?php if($view_hod): ?>
    <div id="viewHODModal" class="modal-overlay active">
        <div class="modal-container">
            <div class="modal-header">
                <h2><i class="fas fa-crown"></i> HOD Details</h2>
                <div class="modal-close" onclick="closeModal('viewHODModal'); window.location.href='dashboard.php'">&times;</div>
            </div>
            
            <div class="profile-view">
                <?php if(!empty($view_hod['photo']) && file_exists("../uploads/".$view_hod['photo'])): ?>
                    <img src="../uploads/<?php echo $view_hod['photo']; ?>" class="profile-view-img">
                <?php else: ?>
                    <div style="width:150px; height:150px; border-radius:50%; background:linear-gradient(135deg,#ffd93d,#ff6b6b); display:flex; align-items:center; justify-content:center; font-size:3rem; margin-bottom:1rem;">
                        <?php echo strtoupper(substr($view_hod['name'], 0, 2)); ?>
                    </div>
                <?php endif; ?>
                <div class="profile-view-name"><?php echo $view_hod['name']; ?></div>
                <div class="profile-view-badge">Head of <?php echo $view_hod['department']; ?> Department</div>
            </div>

            <div class="details-grid">
                <div class="detail-item">
                    <div class="detail-label">HOD ID</div>
                    <div class="detail-value"><?php echo $view_hod['hod_id']; ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Email</div>
                    <div class="detail-value"><?php echo $view_hod['email']; ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Phone</div>
                    <div class="detail-value"><?php echo $view_hod['phone']; ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Qualification</div>
                    <div class="detail-value"><?php echo $view_hod['qualification']; ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Experience</div>
                    <div class="detail-value"><?php echo $view_hod['experience']; ?> years</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Username</div>
                    <div class="detail-value"><?php echo $view_hod['username']; ?></div>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button class="btn-submit" onclick="editHOD(<?php echo $view_hod['id']; ?>)">
                    <i class="fas fa-edit"></i> Edit HOD
                </button>
                <button class="btn-submit" style="background: linear-gradient(135deg, #ff6b6b, #ff4757);" onclick="window.location.href='?delete_hod=<?php echo $view_hod['id']; ?>'">
                    <i class="fas fa-trash"></i> Delete HOD
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ==================== EDIT STUDENT MODAL ==================== -->
    <?php if($edit_student): ?>
    <div id="editStudentModal" class="modal-overlay active">
        <div class="modal-container">
            <div class="modal-header">
                <h2><i class="fas fa-edit"></i> Edit Student</h2>
                <div class="modal-close" onclick="closeModal('editStudentModal'); window.location.href='dashboard.php'">&times;</div>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="student_id" value="<?php echo $edit_student['id']; ?>">
                <input type="hidden" name="old_photo" value="<?php echo $edit_student['photo']; ?>">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Roll Number</label>
                        <input type="text" name="roll_no" value="<?php echo $edit_student['roll_no']; ?>" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" value="<?php echo $edit_student['name']; ?>" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="dob" value="<?php echo $edit_student['dob']; ?>" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?php echo $edit_student['email']; ?>" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" value="<?php echo $edit_student['phone']; ?>" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Course</label>
                        <input type="text" name="course" value="<?php echo $edit_student['course']; ?>" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Department</label>
                        <select name="department" class="form-control" required>
                            <option value="AIML" <?php echo ($edit_student['department'] == 'AIML') ? 'selected' : ''; ?>>AIML</option>
                            <option value="CSE" <?php echo ($edit_student['department'] == 'CSE') ? 'selected' : ''; ?>>CSE</option>
                               <option value="IOT" <?php echo ($edit_student['department'] == 'IOT') ? 'selected' : ''; ?>>IOT</option>
                            <option value="ECE" <?php echo ($edit_student['department'] == 'ECE') ? 'selected' : ''; ?>>ECE</option>
                            <option value="MECH" <?php echo ($edit_student['department'] == 'MECH') ? 'selected' : ''; ?>>MECH</option>
                            <option value="CIVIL" <?php echo ($edit_student['department'] == 'CIVIL') ? 'selected' : ''; ?>>CIVIL</option>
                            <option value="EEE" <?php echo ($edit_student['department'] == 'EEE') ? 'selected' : ''; ?>>EEE</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Semester</label>
                        <select name="semester" class="form-control" required>
                            <?php for($i=1; $i<=6; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php echo ($edit_student['semester'] == $i) ? 'selected' : ''; ?>>Semester <?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Section</label>
                        <select name="section" class="form-control" required>
                            <option value="1A" <?php echo ($edit_student['section'] == '1A') ? 'selected' : ''; ?>>Section 1A</option>
                            <option value="1B" <?php echo ($edit_student['section'] == '1B') ? 'selected' : ''; ?>>Section 1B</option>
                            <option value="1C" <?php echo ($edit_student['section'] == '1C') ? 'selected' : ''; ?>>Section 1C</option>
                            <option value="1D" <?php echo ($edit_student['section'] == '1D') ? 'selected' : ''; ?>>Section 1D</option>
                            
                            <option value="2A" <?php echo ($edit_student['section'] == '2A') ? 'selected' : ''; ?>>Section 2A</option>
                            <option value="2B" <?php echo ($edit_student['section'] == '2B') ? 'selected' : ''; ?>>Section 2B</option>
                            <option value="2C" <?php echo ($edit_student['section'] == '2C') ? 'selected' : ''; ?>>Section 2C</option>
                            <option value="2D" <?php echo ($edit_student['section'] == '2D') ? 'selected' : ''; ?>>Section 2D</option>
                            
                            <option value="3A" <?php echo ($edit_student['section'] == '3A') ? 'selected' : ''; ?>>Section 3A</option>
                            <option value="3B" <?php echo ($edit_student['section'] == '3B') ? 'selected' : ''; ?>>Section 3B</option>
                            <option value="3C" <?php echo ($edit_student['section'] == '3C') ? 'selected' : ''; ?>>Section 3C</option>
                            <option value="3D" <?php echo ($edit_student['section'] == '3D') ? 'selected' : ''; ?>>Section 3D</option>

                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Total Fee</label>
                        <input type="number" name="total_fee" value="<?php echo $edit_student['total_fee']; ?>" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>SSC Marks</label>
                        <input type="number" name="ssc_marks" value="<?php echo $edit_student['ssc_marks']; ?>" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Polycet Rank</label>
                        <input type="number" name="polycet_rank" value="<?php echo $edit_student['polycet_rank']; ?>" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <select name="category" class="form-control" required>
                            <option value="OC" <?php echo ($edit_student['category'] == 'OC') ? 'selected' : ''; ?>>OC</option>
                            <option value="BC" <?php echo ($edit_student['category'] == 'BC') ? 'selected' : ''; ?>>BC</option>
                            <option value="SC" <?php echo ($edit_student['category'] == 'SC') ? 'selected' : ''; ?>>SC</option>
                            <option value="ST" <?php echo ($edit_student['category'] == 'ST') ? 'selected' : ''; ?>>ST</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Father's Name</label>
                        <input type="text" name="father_name" value="<?php echo $edit_student['father_name']; ?>" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Mother's Name</label>
                        <input type="text" name="mother_name" value="<?php echo $edit_student['mother_name']; ?>" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Blood Group</label>
                        <select name="blood_group" class="form-control" required>
                            <option value="A+" <?php echo ($edit_student['blood_group'] == 'A+') ? 'selected' : ''; ?>>A+</option>
                            <option value="A-" <?php echo ($edit_student['blood_group'] == 'A-') ? 'selected' : ''; ?>>A-</option>
                            <option value="B+" <?php echo ($edit_student['blood_group'] == 'B+') ? 'selected' : ''; ?>>B+</option>
                            <option value="B-" <?php echo ($edit_student['blood_group'] == 'B-') ? 'selected' : ''; ?>>B-</option>
                            <option value="O+" <?php echo ($edit_student['blood_group'] == 'O+') ? 'selected' : ''; ?>>O+</option>
                            <option value="O-" <?php echo ($edit_student['blood_group'] == 'O-') ? 'selected' : ''; ?>>O-</option>
                            <option value="AB+" <?php echo ($edit_student['blood_group'] == 'AB+') ? 'selected' : ''; ?>>AB+</option>
                            <option value="AB-" <?php echo ($edit_student['blood_group'] == 'AB-') ? 'selected' : ''; ?>>AB-</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Permanent Address</label>
                        <textarea name="permanent_address" class="form-control" required><?php echo $edit_student['permanent_address']; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Local Address</label>
                        <textarea name="local_address" class="form-control" required><?php echo $edit_student['local_address']; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Change Photo</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                        <?php if(!empty($edit_student['photo'])): ?>
                        <small style="color:rgba(255,255,255,0.6);">Current photo: <?php echo $edit_student['photo']; ?></small>
                        <?php endif; ?>
                    </div>
                </div>
                
                <button type="submit" name="edit_student" class="btn-submit">
                    <i class="fas fa-save"></i> Update Student
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- ==================== EDIT EMPLOYEE MODAL ==================== -->
    <?php if($edit_employee): ?>
    <div id="editEmployeeModal" class="modal-overlay active">
        <div class="modal-container">
            <div class="modal-header">
                <h2><i class="fas fa-edit"></i> Edit Employee</h2>
                <div class="modal-close" onclick="closeModal('editEmployeeModal'); window.location.href='dashboard.php'">&times;</div>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="emp_id" value="<?php echo $edit_employee['emp_id']; ?>">
                <input type="hidden" name="old_photo" value="<?php echo $edit_employee['photo']; ?>">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Employee ID</label>
                        <input type="text" value="<?php echo $edit_employee['emp_id']; ?>" class="form-control" disabled>
                    </div>
                    
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" value="<?php echo $edit_employee['name']; ?>" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="dob" value="<?php echo $edit_employee['dob']; ?>" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Department</label>
                        <select name="department" class="form-control" required>
                            <option value="AIML" <?php echo ($edit_employee['department'] == 'AIML') ? 'selected' : ''; ?>>AIML</option>
                            <option value="CSE" <?php echo ($edit_employee['department'] == 'CSE') ? 'selected' : ''; ?>>CSE</option>
                            <option value="IOT" <?php echo ($edit_student['department'] == 'IOT') ? 'selected' : ''; ?>>IOT</option>
                            <option value="ECE" <?php echo ($edit_employee['department'] == 'ECE') ? 'selected' : ''; ?>>ECE</option>
                            <option value="MECH" <?php echo ($edit_employee['department'] == 'MECH') ? 'selected' : ''; ?>>MECH</option>
                            <option value="CIVIL" <?php echo ($edit_employee['department'] == 'CIVIL') ? 'selected' : ''; ?>>CIVIL</option>
                            <option value="EEE" <?php echo ($edit_employee['department'] == 'EEE') ? 'selected' : ''; ?>>EEE</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" value="<?php echo $edit_employee['phone']; ?>" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Category</label>
                        <input type="text" name="category" value="<?php echo $edit_employee['category']; ?>" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Father's Name</label>
                        <input type="text" name="father_name" value="<?php echo $edit_employee['father_name']; ?>" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Blood Group</label>
                        <select name="blood_group" class="form-control" required>
                            <option value="A+" <?php echo ($edit_employee['blood_group'] == 'A+') ? 'selected' : ''; ?>>A+</option>
                            <option value="A-" <?php echo ($edit_employee['blood_group'] == 'A-') ? 'selected' : ''; ?>>A-</option>
                            <option value="B+" <?php echo ($edit_employee['blood_group'] == 'B+') ? 'selected' : ''; ?>>B+</option>
                            <option value="B-" <?php echo ($edit_employee['blood_group'] == 'B-') ? 'selected' : ''; ?>>B-</option>
                            <option value="O+" <?php echo ($edit_employee['blood_group'] == 'O+') ? 'selected' : ''; ?>>O+</option>
                            <option value="O-" <?php echo ($edit_employee['blood_group'] == 'O-') ? 'selected' : ''; ?>>O-</option>
                            <option value="AB+" <?php echo ($edit_employee['blood_group'] == 'AB+') ? 'selected' : ''; ?>>AB+</option>
                            <option value="AB-" <?php echo ($edit_employee['blood_group'] == 'AB-') ? 'selected' : ''; ?>>AB-</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Permanent Address</label>
                        <textarea name="permanent_address" class="form-control" required><?php echo $edit_employee['permanent_address']; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Profession</label>
                        <input type="text" name="profession" value="<?php echo $edit_employee['profession']; ?>" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Salary</label>
                        <input type="number" name="salary" value="<?php echo $edit_employee['salary']; ?>" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?php echo $edit_employee['email']; ?>" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" value="<?php echo $edit_employee['username']; ?>" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Change Photo</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                        <?php if(!empty($edit_employee['photo'])): ?>
                        <small style="color:rgba(255,255,255,0.6);">Current photo: <?php echo $edit_employee['photo']; ?></small>
                        <?php endif; ?>
                    </div>
                </div>
                
                <button type="submit" name="edit_employee" class="btn-submit">
                    <i class="fas fa-save"></i> Update Employee
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- ==================== EDIT HOD MODAL ==================== -->
    <?php if($edit_hod): ?>
    <div id="editHODModal" class="modal-overlay active">
        <div class="modal-container">
            <div class="modal-header">
                <h2><i class="fas fa-edit"></i> Edit HOD</h2>
                <div class="modal-close" onclick="closeModal('editHODModal'); window.location.href='dashboard.php'">&times;</div>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="hod_id" value="<?php echo $edit_hod['id']; ?>">
                <input type="hidden" name="old_photo" value="<?php echo $edit_hod['photo']; ?>">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>HOD ID</label>
                        <input type="text" name="hod_id_code" value="<?php echo $edit_hod['hod_id']; ?>" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" value="<?php echo $edit_hod['name']; ?>" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?php echo $edit_hod['email']; ?>" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" value="<?php echo $edit_hod['phone']; ?>" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Department</label>
                        <select name="department" class="form-control" required>
                            <option value="AIML" <?php echo ($edit_hod['department'] == 'AIML') ? 'selected' : ''; ?>>AIML</option>
                            <option value="CSE" <?php echo ($edit_hod['department'] == 'CSE') ? 'selected' : ''; ?>>CSE</option>
                             <option value="IOT" <?php echo ($edit_student['department'] == 'IOT') ? 'selected' : ''; ?>>IOT</option>
                            <option value="ECE" <?php echo ($edit_hod['department'] == 'ECE') ? 'selected' : ''; ?>>ECE</option>
                            <option value="MECH" <?php echo ($edit_hod['department'] == 'MECH') ? 'selected' : ''; ?>>MECH</option>
                            <option value="CIVIL" <?php echo ($edit_hod['department'] == 'CIVIL') ? 'selected' : ''; ?>>CIVIL</option>
                            <option value="EEE" <?php echo ($edit_hod['department'] == 'EEE') ? 'selected' : ''; ?>>EEE</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Qualification</label>
                        <input type="text" name="qualification" value="<?php echo $edit_hod['qualification']; ?>" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Experience (Years)</label>
                        <input type="number" name="experience" value="<?php echo $edit_hod['experience']; ?>" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" value="<?php echo $edit_hod['username']; ?>" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Change Photo</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                        <?php if(!empty($edit_hod['photo'])): ?>
                        <small style="color:rgba(255,255,255,0.6);">Current photo: <?php echo $edit_hod['photo']; ?></small>
                        <?php endif; ?>
                    </div>
                </div>
                
                <button type="submit" name="edit_hod" class="btn-submit">
                    <i class="fas fa-save"></i> Update HOD
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- ==================== FEE STATUS MODAL ==================== -->
    <div id="feeStatusModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h2><i class="fas fa-chart-pie"></i> Fee Status Report</h2>
                <div class="modal-close" onclick="closeModal('feeStatusModal')">&times;</div>
            </div>
            
            <div style="margin-bottom: 2rem;">
                <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                    <button class="tab-btn active" onclick="filterFee('all')">All</button>
                    <button class="tab-btn" onclick="filterFee('paid')">Paid</button>
                    <button class="tab-btn" onclick="filterFee('pending')">Pending</button>
                    <button class="tab-btn" onclick="filterFee('partial')">Partial</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="modern-table" id="feeStatusTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Roll No</th>
                            <th>Department</th>
                            <th>Total Fee</th>
                            <th>Paid</th>
                            <th>Remaining</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $fee_students = $conn->query("SELECT * FROM students ORDER BY name");
                        while($student = $fee_students->fetch_assoc()):
                            $paid_data = $conn->query("SELECT SUM(amount) as total_paid FROM payments WHERE student_id='{$student['id']}'")->fetch_assoc();
                            $total_paid = $paid_data['total_paid'] ?? 0;
                            $remaining = $student['total_fee'] - $total_paid;
                            
                            if($total_paid == 0){
                                $status = "Pending";
                                $status_class = "fee-pending";
                            } elseif($remaining == 0){
                                $status = "Paid";
                                $status_class = "fee-paid";
                            } else {
                                $status = "Partial";
                                $status_class = "fee-partial";
                            }
                        ?>
                        <tr data-status="<?php echo strtolower($status); ?>">
                            <td><?php echo htmlspecialchars($student['name']); ?></td>
                            <td><?php echo htmlspecialchars($student['roll_no']); ?></td>
                            <td><?php echo htmlspecialchars($student['department'] ?? $student['course']); ?></td>
                            <td>₹<?php echo number_format($student['total_fee']); ?></td>
                            <td>₹<?php echo number_format($total_paid); ?></td>
                            <td>₹<?php echo number_format($remaining); ?></td>
                            <td><span class="<?php echo $status_class; ?>" style="padding:0.3rem 0.8rem; border-radius:50px;"><?php echo $status; ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Modal Functions
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // Tab Switching
        function switchTab(tabName) {
            // Update tab buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Show corresponding section
            document.querySelectorAll('.table-section').forEach(section => {
                section.classList.remove('active');
            });
            document.getElementById(tabName + '-section').classList.add('active');
        }

        // Fee Status Filter
        function filterFee(status) {
            const rows = document.querySelectorAll('#feeStatusTable tbody tr');
            rows.forEach(row => {
                if(status === 'all' || row.dataset.status === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Update active filter button
            document.querySelectorAll('#feeStatusModal .tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
        }

        // Global Search
        document.getElementById('globalSearch').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            
            // Search in students table
            searchTable('studentsTable', filter);
            // Search in employees table
            searchTable('employeesTable', filter);
            // Search in hods table
            searchTable('hodsTable', filter);
        });

        function searchTable(tableId, filter) {
            const table = document.getElementById(tableId);
            if(!table) return;
            
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        }

        // View Functions
        function viewStudent(id) {
            window.location.href = '?view_student_id=' + id;
        }

        function editStudent(id) {
            window.location.href = '?edit_student_id=' + id;
        }

        function viewEmployee(id) {
            window.location.href = '?view_employee_id=' + id;
        }

        function editEmployee(id) {
            window.location.href = '?edit_employee_id=' + id;
        }

        function viewHOD(id) {
            window.location.href = '?view_hod_id=' + id;
        }

        function editHOD(id) {
            window.location.href = '?edit_hod_id=' + id;
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.classList.remove('active');
                document.body.style.overflow = 'auto';
                window.location.href = 'dashboard.php';
            }
        }

        // Keyboard shortcut (ESC to close)
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay.active').forEach(modal => {
                    modal.classList.remove('active');
                });
                document.body.style.overflow = 'auto';
                window.location.href = 'dashboard.php';
            }
        });

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Animation for stats cards
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        });

        document.querySelectorAll('.stat-card').forEach(card => {
            observer.observe(card);
        });
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