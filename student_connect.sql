-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 12, 2026 at 07:23 AM
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
-- Database: `student_connect`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `period` int(11) NOT NULL,
  `status` enum('Present','Absent') NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `status` varchar(30) DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `reason` text NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `status` enum('pending','forwarded','approved','rejected') DEFAULT 'pending',
  `teacher_remarks` text DEFAULT NULL,
  `hod_remarks` text DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_requests`
--

INSERT INTO `leave_requests` (`id`, `student_id`, `reason`, `from_date`, `to_date`, `status`, `teacher_remarks`, `hod_remarks`, `attachment`, `created_at`) VALUES
(25, 145, 'sick leave', '2026-02-11', '2026-02-14', 'approved', '', '', 'doc_698c48e16cca65.46741225.jpg', '2026-02-11 09:16:17'),
(26, 68, 'feeling sick', '2026-02-11', '2026-02-14', 'rejected', '', '', 'doc_698c4a62bb68f4.41678645.jpg', '2026-02-11 09:22:42');

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','teacher','student') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `section` varchar(20) NOT NULL,
  `roll_no` varchar(20) NOT NULL,
  `login_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `section` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `login_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teacher_subject`
--

CREATE TABLE `teacher_subject` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `section` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `roll_no` varchar(50) DEFAULT NULL,
  `email` varchar(120) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','teacher','hod') NOT NULL,
  `class` varchar(20) DEFAULT NULL,
  `dept` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `attendance_percentage` decimal(5,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `roll_no`, `email`, `password`, `role`, `class`, `dept`, `created_at`, `attendance_percentage`) VALUES
(68, 'Aditi Sharma', '323103210100', '323103210100@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 72.80),
(69, 'Akshara N', '323103210101', '323103210101@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 91.40),
(70, 'Alekhya S', '323103210102', '323103210102@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 47.20),
(71, 'Amulya R', '323103210103', '323103210103@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 83.20),
(72, 'Ananya Gupta', '323103210104', '323103210104@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 88.50),
(73, 'Ankitha Reddy', '323103210105', '323103210105@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 92.10),
(74, 'Ashika P', '323103210106', '323103210106@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 74.60),
(75, 'Bhavana Sri', '323103210107', '323103210107@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 85.35),
(76, 'Chaitra S', '323103210108', '323103210108@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 90.80),
(77, 'Deepika Rao', '323103210109', '323103210109@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 43.80),
(78, 'Divya M', '323103210110', '323103210110@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 87.90),
(79, 'Harika S', '323103210111', '323103210111@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 95.10),
(80, 'Hima Bindu', '323103210112', '323103210112@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 82.30),
(81, 'Ishika Jain', '323103210113', '323103210113@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 96.70),
(82, 'Jaya Sri', '323103210114', '323103210114@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 70.10),
(83, 'Keerthana G', '323103210115', '323103210115@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 89.50),
(84, 'Lavanya S', '323103210116', '323103210116@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 84.60),
(85, 'Lohitha M', '323103210117', '323103210117@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 73.90),
(86, 'Manisha R', '323103210118', '323103210118@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 87.70),
(87, 'Meghana P', '323103210119', '323103210119@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 92.50),
(88, 'Navya Sri', '323103210120', '323103210120@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 86.10),
(89, 'Neha Krishna', '323103210121', '323103210121@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 68.20),
(90, 'Niharika R', '323103210122', '323103210122@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 91.50),
(91, 'Praneetha S', '323103210123', '323103210123@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 81.60),
(92, 'Priya Lakshmi', '323103210124', '323103210124@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 93.40),
(93, 'Rekha Sri', '323103210125', '323103210125@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 45.10),
(94, 'Rithika M', '323103210126', '323103210126@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 88.40),
(95, 'Sanjana Rao', '323103210127', '323103210127@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 92.30),
(96, 'Shreya Reddy', '323103210128', '323103210128@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 79.80),
(97, 'Sindhuja P', '323103210129', '323103210129@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 85.10),
(98, 'Sravani K', '323103210130', '323103210130@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 72.60),
(99, 'Sushma Devi', '323103210131', '323103210131@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 96.20),
(100, 'Tejaswi M', '323103210132', '323103210132@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 87.10),
(101, 'Vaishnavi S', '323103210133', '323103210133@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 82.70),
(102, 'Varshitha P', '323103210134', '323103210134@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 77.40),
(103, 'Anusha R', '323103210135', '323103210135@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 90.90),
(104, 'Spandana K', '323103210136', '323103210136@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 81.20),
(105, 'Greeshma R', '323103210137', '323103210137@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 97.00),
(106, 'Harshini P', '323103210138', '323103210138@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 83.40),
(107, 'Jahnavi K', '323103210139', '323103210139@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 74.20),
(108, 'Keerthi Sri', '323103210140', '323103210140@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 88.10),
(109, 'Mounika R', '323103210141', '323103210141@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 94.10),
(110, 'Naga Lakshmi', '323103210142', '323103210142@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 69.50),
(111, 'Pavani P', '323103210143', '323103210143@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 81.90),
(112, 'Poojitha G', '323103210144', '323103210144@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 91.10),
(113, 'Renu Sri', '323103210145', '323103210145@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 87.30),
(114, 'Rashmitha S', '323103210146', '323103210146@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 70.60),
(115, 'Sai Priya', '323103210147', '323103210147@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 96.70),
(116, 'Sandhya R', '323103210148', '323103210148@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 78.80),
(117, 'Siri Chandana', '323103210149', '323103210149@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 83.10),
(118, 'Sruthi M', '323103210150', '323103210150@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 90.55),
(119, 'Supraja G', '323103210151', '323103210151@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 85.40),
(120, 'Swathi R', '323103210152', '323103210152@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 71.10),
(121, 'Tanuja K', '323103210153', '323103210153@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 90.20),
(122, 'Triveni P', '323103210154', '323103210154@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 88.90),
(123, 'Usha Rani', '323103210155', '323103210155@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 72.10),
(124, 'Vanaja K', '323103210156', '323103210156@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 84.20),
(125, 'Yamini S', '323103210157', '323103210157@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 92.75),
(126, 'Bhavani R', '323103210158', '323103210158@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 86.40),
(127, 'Chandana P', '323103210159', '323103210159@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 76.20),
(128, 'Madhuri S', '323103210160', '323103210160@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 94.30),
(129, 'Roopa Sri', '323103210161', '323103210161@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 83.70),
(130, 'Sneha R', '323103210162', '323103210162@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 73.80),
(131, 'Taruni M', '323103210163', '323103210163@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 87.90),
(132, 'Varalakshmi P', '323103210164', '323103210164@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-1', 'CSE', '2025-11-28 08:53:02', 93.10),
(137, 'Aaradhya', '323103210165', '323103210165@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-2', 'CSE', '2025-11-28 09:08:06', 88.40),
(138, 'Bhavana', '323103210166', '323103210166@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-2', 'CSE', '2025-11-28 09:08:06', 46.10),
(139, 'Charitha', '323103210167', '323103210167@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-2', 'CSE', '2025-11-28 09:08:06', 92.60),
(140, 'Dharani', '323103210168', '323103210168@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-2', 'CSE', '2025-11-28 09:08:06', 73.80),
(141, 'Eshwari', '323103210169', '323103210169@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-2', 'CSE', '2025-11-28 09:08:06', 95.10),
(142, 'Gayathri', '323103210170', '323103210170@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-2', 'CSE', '2025-11-28 09:08:06', 87.30),
(143, 'Hamsika', '323103210171', '323103210171@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-2', 'CSE', '2025-11-28 09:08:06', 93.20),
(144, 'Indu', '323103210172', '323103210172@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-2', 'CSE', '2025-11-28 09:08:06', 82.60),
(145, 'Jyothi', '323103210173', '323103210173@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-2', 'CSE', '2025-11-28 09:08:06', 48.90),
(146, 'Kavya', '323103210174', '323103210174@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-2', 'CSE', '2025-11-28 09:08:06', 91.20),
(147, 'Lalitha', '323103210175', '323103210175@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-2', 'CSE', '2025-11-28 09:08:06', 72.60),
(148, 'Monika', '323103210176', '323103210176@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-2', 'CSE', '2025-11-28 09:08:06', 89.10),
(149, 'Nidhi', '323103210177', '323103210177@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-2', 'CSE', '2025-11-28 09:08:06', 94.40),
(150, 'Ojaswi', '323103210178', '323103210178@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-2', 'CSE', '2025-11-28 09:08:06', 44.50),
(151, 'Padmavathi', '323103210179', '323103210179@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-2', 'CSE', '2025-11-28 09:08:06', 88.70),
(152, 'Ranjitha', '323103210180', '323103210180@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-2', 'CSE', '2025-11-28 09:08:06', 81.50),
(153, 'Sailaja', '323103210181', '323103210181@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-2', 'CSE', '2025-11-28 09:08:06', 92.40),
(154, 'Tanvi', '323103210182', '323103210182@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-2', 'CSE', '2025-11-28 09:08:06', 86.90),
(155, 'Uma', '323103210183', '323103210183@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-2', 'CSE', '2025-11-28 09:08:06', 74.20),
(156, 'Vasundhara', '323103210184', '323103210184@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-2', 'CSE', '2025-11-28 09:08:06', 83.60),
(157, 'Yogitha', '323103210185', '323103210185@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-2', 'CSE', '2025-11-28 09:08:06', 96.10),
(158, 'Akhila', '323103210230', '323103210230@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-3', 'CSE', '2025-11-28 09:08:42', 86.70),
(159, 'Bhuvana', '323103210231', '323103210231@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-3', 'CSE', '2025-11-28 09:08:42', 91.75),
(160, 'Chandrika', '323103210232', '323103210232@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-3', 'CSE', '2025-11-28 09:08:42', 49.30),
(161, 'Devika', '323103210233', '323103210233@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-3', 'CSE', '2025-11-28 09:08:42', 83.10),
(162, 'Eshita', '323103210234', '323103210234@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-3', 'CSE', '2025-11-28 09:08:42', 95.60),
(163, 'Gagana', '323103210235', '323103210235@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-3', 'CSE', '2025-11-28 09:08:42', 40.80),
(164, 'Haripriya', '323103210236', '323103210236@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-3', 'CSE', '2025-11-28 09:08:42', 93.40),
(165, 'Ishwarya', '323103210237', '323103210237@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-3', 'CSE', '2025-11-28 09:08:42', 86.90),
(166, 'Joshna', '323103210238', '323103210238@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-3', 'CSE', '2025-11-28 09:08:42', 45.60),
(167, 'Keerthi', '323103210239', '323103210239@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-3', 'CSE', '2025-11-28 09:08:42', 89.95),
(168, 'Likitha', '323103210240', '323103210240@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-3', 'CSE', '2025-11-28 09:08:42', 82.60),
(169, 'Manjula', '323103210241', '323103210241@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-3', 'CSE', '2025-11-28 09:08:42', 94.30),
(170, 'Naveena', '323103210242', '323103210242@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-3', 'CSE', '2025-11-28 09:08:42', 78.70),
(171, 'Roshini', '323103210243', '323103210243@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-3', 'CSE', '2025-11-28 09:08:42', 90.80),
(172, 'Sowmya', '323103210244', '323103210244@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-3', 'CSE', '2025-11-28 09:08:42', 72.90),
(173, 'Aparna', '323103210245', '323103210245@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-4', 'CSE', '2025-11-28 09:09:02', 89.60),
(174, 'Bhargavi', '323103210246', '323103210246@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-4', 'CSE', '2025-11-28 09:09:02', 93.10),
(175, 'Chitra', '323103210247', '323103210247@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-4', 'CSE', '2025-11-28 09:09:02', 81.25),
(176, 'Daksha', '323103210248', '323103210248@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-4', 'CSE', '2025-11-28 09:09:02', 76.40),
(177, 'Eshani', '323103210249', '323103210249@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-4', 'CSE', '2025-11-28 09:09:02', 95.20),
(178, 'Fathima', '323103210250', '323103210250@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-4', 'CSE', '2025-11-28 09:09:02', 48.70),
(179, 'Gowri', '323103210251', '323103210251@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-4', 'CSE', '2025-11-28 09:09:02', 84.30),
(180, 'Harshitha', '323103210252', '323103210252@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-4', 'CSE', '2025-11-28 09:09:02', 91.50),
(181, 'Indira', '323103210253', '323103210253@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-4', 'CSE', '2025-11-28 09:09:02', 46.90),
(182, 'Jyotsna', '323103210254', '323103210254@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-4', 'CSE', '2025-11-28 09:09:02', 92.95),
(183, 'Kamala', '323103210255', '323103210255@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-4', 'CSE', '2025-11-28 09:09:02', 79.20),
(184, 'Laxmi', '323103210256', '323103210256@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-4', 'CSE', '2025-11-28 09:09:02', 86.90),
(185, 'Meenakshi', '323103210257', '323103210257@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-4', 'CSE', '2025-11-28 09:09:02', 94.60),
(186, 'Nirmala', '323103210258', '323103210258@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-4', 'CSE', '2025-11-28 09:09:02', 83.10),
(187, 'Padma', '323103210259', '323103210259@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-4', 'CSE', '2025-11-28 09:09:02', 41.60),
(188, 'Revathi', '323103210260', '323103210260@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'student', '3CSE-4', 'CSE', '2025-11-28 09:09:02', 90.30),
(189, 'Dr.N.Sharmili', 'TCH001', 'sharmili@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'teacher', '3CSE-1', 'CSE', '2025-11-28 09:18:34', 0.00),
(190, 'Mrs.V.Gowtami Annapurna', 'TCH002', 'gowtami@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'teacher', '3CSE-2', 'CSE', '2025-11-28 09:18:34', 0.00),
(191, 'Mrs.K.Suneetha', 'TCH003', 'suneetha@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'teacher', '3CSE-3', 'CSE', '2025-11-28 09:18:34', 0.00),
(192, 'Dr.G.Sankara Rao', 'TCH004', 'sankararao@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'teacher', '3CSE-4', 'CSE', '2025-11-28 09:18:34', 0.00),
(193, 'Prof.Dr.P.V.S.Lakshmi Jagadamba', 'HOD001', 'jagadamba@gvpcew.ac.in', '3a5d075fa0189fbb2bb7a50cdad37087', 'hod', '', 'CSE', '2025-11-28 09:18:34', 0.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `login_id` (`login_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `login_id` (`login_id`);

--
-- Indexes for table `teacher_subject`
--
ALTER TABLE `teacher_subject`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `roll_no` (`roll_no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `teacher_subject`
--
ALTER TABLE `teacher_subject`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=194;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `attendance_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `login` (`id`);

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD CONSTRAINT `leave_requests_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`login_id`) REFERENCES `login` (`id`);

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`login_id`) REFERENCES `login` (`id`);

--
-- Constraints for table `teacher_subject`
--
ALTER TABLE `teacher_subject`
  ADD CONSTRAINT `teacher_subject_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`),
  ADD CONSTRAINT `teacher_subject_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
