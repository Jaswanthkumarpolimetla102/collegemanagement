-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 04, 2026 at 10:57 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fee_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin123'),
(2, 'naveen', 'naveen@102'),
(3, 'jaswanth', 'jaswanth@102'),
(4, 'harsha', 'harsha@101'),
(5, 'ricky', 'ricky@073'),
(7, 'sampath', 'sampath@076'),
(8, 'ikkram', 'ikkram@112');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `subject_code` varchar(20) DEFAULT NULL,
  `section` varchar(10) DEFAULT NULL,
  `period` int(11) DEFAULT NULL,
  `attendance_date` date DEFAULT NULL,
  `status` enum('Present','Absent') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `student_id`, `subject_code`, `section`, `period`, `attendance_date`, `status`) VALUES
(11, 2, ' AIM502', '3B', 1, '2026-03-04', 'Present'),
(12, 2, ' AIM502', '3B', NULL, '2026-03-03', 'Absent'),
(13, 2, ' AIM502', '3B', NULL, '2026-03-04', 'Present'),
(14, 2, ' AIM502', '3B', NULL, '2026-03-04', 'Present'),
(15, 2, ' AIM502', '3B', NULL, '2026-03-04', 'Present'),
(16, 2, ' AIM502', '3B', NULL, '2026-03-04', 'Present'),
(17, 2, 'AIM501', '3B', NULL, '2026-03-04', 'Present');

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `name` varchar(100) DEFAULT NULL,
  `profession` varchar(100) DEFAULT NULL,
  `salary` int(11) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `phone` bigint(15) DEFAULT NULL,
  `department` tinytext DEFAULT NULL,
  `emp_id` varchar(15) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `father_name` varchar(100) DEFAULT NULL,
  `blood_group` varchar(10) DEFAULT NULL,
  `permanent_address` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `email` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`name`, `profession`, `salary`, `username`, `password`, `dob`, `phone`, `department`, `emp_id`, `category`, `father_name`, `blood_group`, `permanent_address`, `photo`, `email`) VALUES
('Pasupuleti Venkata Vinay', 'lecturer', 75000, 'vinay', 'vinay', '2008-02-16', 9704144711, 'AIML', 'Vinay096', 'oc', 'P Venkatateswara Rao', 'O+', '8-26,Kornepadu,Guntur,AP,India', '1772440351_WIN_20260302_12_40_52_Pro.jpg', 'vinay@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `employee_subjects`
--

CREATE TABLE `employee_subjects` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) DEFAULT NULL,
  `subject_code` varchar(20) DEFAULT NULL,
  `section` varchar(10) DEFAULT NULL,
  `department` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_subjects`
--

INSERT INTO `employee_subjects` (`id`, `employee_id`, `subject_code`, `section`, `department`) VALUES
(1, 'Vinay096', 'AIM501', '3B', 'aiml'),
(2, 'Vinay096', ' AIM502', '3B', 'aiml');

-- --------------------------------------------------------

--
-- Table structure for table `hod`
--

CREATE TABLE `hod` (
  `id` int(11) NOT NULL,
  `hod_id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `department` varchar(50) NOT NULL,
  `qualification` varchar(100) DEFAULT NULL,
  `experience` int(11) DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hod`
--

INSERT INTO `hod` (`id`, `hod_id`, `name`, `email`, `phone`, `department`, `qualification`, `experience`, `joining_date`, `username`, `password`, `photo`, `created_at`) VALUES
(1, 'HOD001', 'Dr. R. Kumar', 'hod.cse@aannmvr.edu', '9876543210', 'CSE', 'Ph.D in Computer Science', 15, '2015-06-01', 'hod_cse', 'hod123', NULL, '2026-03-03 03:28:48'),
(2, 'HOD002', 'Dr. S. Reddy', 'hod.ece@aannmvr.edu', '9876543211', 'ECE', 'Ph.D in Electronics', 12, '2016-07-15', 'hod_ece', 'hod123', NULL, '2026-03-03 03:28:48'),
(4, 'HOD004', 'Dr. V. Sharma', 'hod.civil@aannmvr.edu', '9876543213', 'CIVIL', 'Ph.D in Civil Engineering', 14, '2017-05-20', 'hod_civil', 'hod123', NULL, '2026-03-03 03:28:48'),
(5, 'hod123', 'N HEMANTH kumar', 'NHK@gmail.com', '9876541230', 'aiml', 'ph D in aiml', 15, '0000-00-00', 'hod_aiml', 'hod123', NULL, '2026-03-03 04:00:06');

-- --------------------------------------------------------

--
-- Table structure for table `internal_marks`
--

CREATE TABLE `internal_marks` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `subject_code` varchar(20) DEFAULT NULL,
  `section` varchar(10) DEFAULT NULL,
  `exam_type` enum('Mid1','Mid2','Mid3','Slip1','Slip2') DEFAULT NULL,
  `marks` int(11) DEFAULT NULL,
  `assignment_marks` int(20) NOT NULL,
  `dinamic_marks` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `internal_marks`
--

INSERT INTO `internal_marks` (`id`, `student_id`, `subject_code`, `section`, `exam_type`, `marks`, `assignment_marks`, `dinamic_marks`) VALUES
(3, 2, ' AIM502', '3B', 'Mid1', 48, 5, 5),
(5, 2, ' AIM502', '3B', 'Mid2', 50, 5, 5),
(10, 2, 'AIM501', '3B', 'Mid1', 48, 5, 3),
(11, 2, 'AIM501', '3B', 'Mid3', 43, 4, 4);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `pay_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `student_id`, `amount`, `pay_date`) VALUES
(6, 2, 10000, '2026-03-04 03:21:49'),
(7, 2, 10000, '2026-03-04 03:27:13');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `roll_no` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `total_fee` int(11) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `phone` bigint(20) DEFAULT NULL,
  `course` tinytext DEFAULT NULL,
  `department` varchar(50) DEFAULT NULL,
  `ssc_marks` int(11) DEFAULT NULL,
  `polycet_rank` int(11) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `father_name` varchar(100) DEFAULT NULL,
  `mother_name` varchar(100) DEFAULT NULL,
  `blood_group` varchar(10) DEFAULT NULL,
  `permanent_address` text DEFAULT NULL,
  `local_address` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `section` varchar(10) NOT NULL,
  `semester` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `roll_no`, `email`, `password`, `total_fee`, `dob`, `phone`, `course`, `department`, `ssc_marks`, `polycet_rank`, `category`, `father_name`, `mother_name`, `blood_group`, `permanent_address`, `local_address`, `photo`, `section`, `semester`) VALUES
(2, 'ricky', '23030-aim-073', 'ricky1@gmail.com', '$2y$10$2PSMqzbBvi2syqdecVc9xupjZyealVvnG8gLodSuf8Se6t/OELKle', 25000, '2008-02-02', 9987456321, 'diploma', 'AIML', 447, 32741, 'sc', 'uncle', 'aunti', 'B+', 'guntur', 'daysciler', '1772550035_IMG_20250215_153014.jpg', '3B', '6');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_code` varchar(20) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `department` varchar(50) NOT NULL,
  `semester` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_code`, `subject_name`, `department`, `semester`, `created_at`) VALUES
(1, 'CS101', 'Programming Fundamentals', 'CSE', 1, '2026-03-03 03:52:43'),
(2, 'CS102', 'Data Structures', 'CSE', 2, '2026-03-03 03:52:43'),
(3, 'CS201', 'Database Management', 'CSE', 3, '2026-03-03 03:52:43'),
(4, 'CS202', 'Operating Systems', 'CSE', 4, '2026-03-03 03:52:43'),
(5, 'EC101', 'Digital Electronics', 'ECE', 1, '2026-03-03 03:52:43'),
(6, 'EC102', 'Analog Circuits', 'ECE', 2, '2026-03-03 03:52:43'),
(7, 'ME101', 'Engineering Mechanics', 'MECH', 1, '2026-03-03 03:52:43'),
(8, 'ME102', 'Thermodynamics', 'MECH', 2, '2026-03-03 03:52:43'),
(9, 'CE101', 'Building Materials', 'CIVIL', 1, '2026-03-03 03:52:43'),
(10, 'CE102', 'Surveying', 'CIVIL', 2, '2026-03-03 03:52:43'),
(13, 'AIM-101', 'English-I', 'AIML', 1, '2026-03-04 09:31:24'),
(14, 'AIM-102', 'Engineering Mathematics-I', 'AIML', 1, '2026-03-04 09:31:24'),
(15, 'AIM-103', 'Engineering Physics', 'AIML', 1, '2026-03-04 09:31:24'),
(16, 'AIM-104', 'Engineering Chemistry and Environmental Studies', 'AIML', 1, '2026-03-04 09:31:24'),
(17, 'AIM-105', 'Basics of Artificial Intelligence & Machine Learning', 'AIML', 1, '2026-03-04 09:31:24'),
(18, 'AIM-106', 'Programming in C', 'AIML', 1, '2026-03-04 09:31:24'),
(19, 'AIM-107', 'Engineering Drawing (Practical)', 'AIML', 1, '2026-03-04 09:31:24'),
(20, 'AIM-108', 'Programming in C Lab', 'AIML', 1, '2026-03-04 09:31:24'),
(21, 'AIM-109A', 'Physics Lab', 'AIML', 1, '2026-03-04 09:31:24'),
(22, 'AIM-109B', 'Chemistry Lab', 'AIML', 1, '2026-03-04 09:31:24'),
(23, 'AIM-110', 'Computer Fundamentals Lab', 'AIML', 1, '2026-03-04 09:31:24'),
(24, 'AIM-301', 'Engineering Mathematics-II', 'AIML', 3, '2026-03-04 09:34:30'),
(25, 'AIM-302', 'Java Programming', 'AIML', 3, '2026-03-04 09:34:30'),
(26, 'AIM-303', 'Operating Systems', 'AIML', 3, '2026-03-04 09:34:30'),
(27, 'AIM-304', 'Digital Electronics & Computer Organization', 'AIML', 3, '2026-03-04 09:34:30'),
(28, 'AIM-305', 'Database Management Systems (DBMS)', 'AIML', 3, '2026-03-04 09:34:30'),
(29, 'AIM-306', 'Java Programming Lab', 'AIML', 3, '2026-03-04 09:34:30'),
(30, 'AIM-307', 'Computer Networking & Cyber Security Lab', 'AIML', 3, '2026-03-04 09:34:30'),
(31, 'AIM-308', 'DBMS Lab', 'AIML', 3, '2026-03-04 09:34:30'),
(32, 'AIM-309', 'Android Programming Lab', 'AIML', 3, '2026-03-04 09:34:30'),
(33, 'AIM-401', 'Web Technologies', 'AIML', 4, '2026-03-04 09:35:51'),
(34, 'AIM-402', 'Python Programming', 'AIML', 4, '2026-03-04 09:35:51'),
(35, 'AIM-403', 'Artificial Intelligence', 'AIML', 4, '2026-03-04 09:35:51'),
(36, 'AIM-404', 'Software Engineering', 'AIML', 4, '2026-03-04 09:35:51'),
(37, 'AIM-405', 'Fundamentals of Machine Learning', 'AIML', 4, '2026-03-04 09:35:51'),
(38, 'AIM-406', 'Web Technologies Lab', 'AIML', 4, '2026-03-04 09:35:51'),
(39, 'AIM-407', 'Python Programming Lab', 'AIML', 4, '2026-03-04 09:35:51'),
(40, 'AIM-408', 'Communication Skills Lab', 'AIML', 4, '2026-03-04 09:35:51'),
(41, 'AIM-409', 'AI Lab using PROLOG', 'AIML', 4, '2026-03-04 09:35:51'),
(42, 'AIM-501', 'Industrial Management & Entrepreneurship', 'AIML', 5, '2026-03-04 09:36:59'),
(43, 'AIM-502', 'Big Data & Cloud Computing', 'AIML', 5, '2026-03-04 09:36:59'),
(44, 'AIM-503', 'Natural Language Processing', 'AIML', 5, '2026-03-04 09:36:59'),
(45, 'AIM-504', 'Internet of Things', 'AIML', 5, '2026-03-04 09:36:59'),
(46, 'AIM-505', 'Artificial Neural Networks & Deep Learning', 'AIML', 5, '2026-03-04 09:36:59'),
(47, 'AIM-506', 'NLP Lab using Python', 'AIML', 5, '2026-03-04 09:36:59'),
(48, 'AIM-507', 'Machine Learning Lab', 'AIML', 5, '2026-03-04 09:36:59'),
(49, 'AIM-508', 'Life Skills', 'AIML', 5, '2026-03-04 09:36:59'),
(50, 'AIM-509', 'Project Work', 'AIML', 5, '2026-03-04 09:36:59');

-- --------------------------------------------------------

--
-- Table structure for table `timetable`
--

CREATE TABLE `timetable` (
  `id` int(11) NOT NULL,
  `day_of_week` varchar(20) DEFAULT NULL,
  `period` int(11) DEFAULT NULL,
  `subject_code` varchar(20) DEFAULT NULL,
  `employee_id` varchar(50) DEFAULT NULL,
  `section` varchar(10) DEFAULT NULL,
  `department` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `timetable`
--

INSERT INTO `timetable` (`id`, `day_of_week`, `period`, `subject_code`, `employee_id`, `section`, `department`) VALUES
(1, 'Wednesday', 1, ' AIM502', 'Vinay096', 'B', 'aiml'),
(2, 'Wednesday', 2, 'AIM501', 'Vinay096', 'B', 'aiml');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`,`subject_code`,`period`,`attendance_date`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`emp_id`);

--
-- Indexes for table `employee_subjects`
--
ALTER TABLE `employee_subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hod`
--
ALTER TABLE `hod`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hod_id` (`hod_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `department` (`department`);

--
-- Indexes for table `internal_marks`
--
ALTER TABLE `internal_marks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`,`subject_code`,`exam_type`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subject_code` (`subject_code`);

--
-- Indexes for table `timetable`
--
ALTER TABLE `timetable`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `employee_subjects`
--
ALTER TABLE `employee_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `hod`
--
ALTER TABLE `hod`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `internal_marks`
--
ALTER TABLE `internal_marks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `timetable`
--
ALTER TABLE `timetable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `internal_marks`
--
ALTER TABLE `internal_marks`
  ADD CONSTRAINT `internal_marks_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
