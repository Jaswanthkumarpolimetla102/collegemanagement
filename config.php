<?php
/*
========================================================
COLLEGE ERP MASTER CONFIG
A.A.N.M.&V.V.R.S.R.S POLYTECHNIC GUDLAVALERU
Single File Configuration
========================================================
*/


/* =========================
   START SESSION
========================= */
if(session_status() === PHP_SESSION_NONE){
    session_start();
}


/* =========================
   DATABASE CONFIG
========================= */
$DB_HOST = "localhost";
$DB_USER = "u941670923_jaswanth";
$DB_PASS = "Fee_system@102";
$DB_NAME = "u941670923_fee_system";


/* =========================
   CONNECT DATABASE
========================= */
$conn = new mysqli($DB_HOST,$DB_USER,$DB_PASS,$DB_NAME);

if($conn->connect_error){
    die("Database Connection Failed : ".$conn->connect_error);
}

$conn->set_charset("utf8mb4");


/* =========================
   BASE URL (change folder)
========================= */
$BASE_URL = "http://localhost/erp_project/";


/* =========================
   TIMEZONE + ERRORS
========================= */
date_default_timezone_set("Asia/Kolkata");
error_reporting(E_ALL);
ini_set("display_errors",1);


/* =================================================
   SECURITY HELPERS
================================================= */

/* clean input */
function clean($data){
    return htmlspecialchars(trim($data));
}

/* hash password */
function hashPass($p){
    return password_hash($p,PASSWORD_DEFAULT);
}

/* verify password */
function verifyPass($p,$h){
    return password_verify($p,$h);
}


/* =================================================
   REDIRECT
================================================= */
function go($path){
    global $BASE_URL;
    header("Location: ".$BASE_URL.$path);
    exit();
}


/* =================================================
   ROLE CHECKS
================================================= */

function adminOnly(){
    if(!isset($_SESSION['admin'])){
        go("admin/login.php");
    }
}

function studentOnly(){
    if(!isset($_SESSION['student'])){
        go("student/login.php");
    }
}

function employeeOnly(){
    if(!isset($_SESSION['employee'])){
        go("employee/login.php");
    }
}

// ✅ NEW: HOD role check
function hodOnly(){
    if(!isset($_SESSION['hod_logged_in'])){
        go("hod/login.php");
    }
}


/* =================================================
   LOGIN FUNCTIONS
================================================= */

function loginAdmin($id){
    $_SESSION['admin']=$id;
}

function loginStudent($id){
    $_SESSION['student']=$id;
}

function loginEmployee($id){
    $_SESSION['employee']=$id;
}

// ✅ NEW: HOD login function
function loginHOD($id, $hod_id, $name, $department){
    $_SESSION['hod_logged_in'] = true;
    $_SESSION['hod_id'] = $hod_id;
    $_SESSION['hod_name'] = $name;
    $_SESSION['hod_department'] = $department;
    $_SESSION['hod_db_id'] = $id;
}


/* =================================================
   LOGOUT
================================================= */
function logout(){
    session_unset();
    session_destroy();
    go("home.php");
}


/* =================================================
   COMMON UTILITIES
================================================= */

function money($amt){
    return "₹ ".number_format($amt);
}

function today(){
    return date("Y-m-d");
}

function now(){
    return date("Y-m-d H:i:s");
}

function success($msg){
    echo "<div class='alert alert-success'>$msg</div>";
}

function errorMsg($msg){
    echo "<div class='alert alert-danger'>$msg</div>";
}

// ✅ NEW: Get HOD details by department
function getHODByDepartment($conn, $department){
    $stmt = $conn->prepare("SELECT * FROM hod WHERE department = ?");
    $stmt->bind_param("s", $department);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// ✅ NEW: Get department employees
function getDepartmentEmployees($conn, $department){
    $stmt = $conn->prepare("SELECT * FROM employee WHERE department = ? ORDER BY name");
    $stmt->bind_param("s", $department);
    $stmt->execute();
    return $stmt->get_result();
}

// ✅ NEW: Get department students
function getDepartmentStudents($conn, $department){
    $stmt = $conn->prepare("SELECT * FROM students WHERE department = ? ORDER BY roll_no");
    $stmt->bind_param("s", $department);
    $stmt->execute();
    return $stmt->get_result();
}

?>