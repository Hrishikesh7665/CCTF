-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 24, 2024 at 11:20 AM
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
-- Database: `cdac-k_ctf_archive`
--

-- --------------------------------------------------------

--
-- Table structure for table `090824`
--

CREATE TABLE `090824` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `profession` varchar(30) NOT NULL,
  `designation` varchar(30) DEFAULT NULL,
  `phoneNumber` bigint(11) NOT NULL,
  `location` varchar(30) NOT NULL,
  `rank` int(5) NOT NULL,
  `score` int(3) NOT NULL,
  `qs_solved` int(5) NOT NULL,
  `cert_no` varchar(15) NOT NULL,
  `issuedDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `090824`
--

INSERT INTO `090824` (`id`, `name`, `email`, `profession`, `designation`, `phoneNumber`, `location`, `rank`, `score`, `qs_solved`, `cert_no`, `issuedDate`) VALUES
(1, 'Biplab Ghosh', 'biplab.801142@cdac.in', 'employee', 'Trainee/Apprentice', 6296267243, 'Kolkata', 1, 120, 7, 'CCTF/0908240001', '2024-08-16 01:45:11'),
(2, 'Mohammad Riyaz', 'riyaz.m@cdac.in', 'employee', 'Senior Project Engineer', 8553352147, 'Thiruvananthapuram', 2, 120, 7, 'CCTF/0908240002', '2024-08-16 01:45:11'),
(3, 'Jafeel V', 'jafeel.v@cdac.in', 'employee', 'Project Engineer', 8606180013, 'Thiruvananthapuram', 3, 105, 6, 'CCTF/0908240003', '2024-08-16 01:45:11'),
(4, 'Gokul Krishna B', 'k.gokul@cdac.in', 'employee', 'Project Engineer', 9745784945, 'Thiruvananthapuram', 4, 105, 6, 'CCTF/0908240004', '2024-08-16 01:45:11'),
(5, 'Rejith R S', 'rejith.rs@cdac.in', 'employee', 'Project Engineer', 8156988459, 'Thiruvananthapuram', 5, 105, 6, 'CCTF/0908240005', '2024-08-16 01:45:11'),
(6, 'Ummu Habeeba ', 'ummu.habeeba@cdac.in', 'employee', 'Project Engineer', 7994295571, 'Thiruvananthapuram', 6, 105, 6, 'CCTF/0908240006', '2024-08-16 01:45:11'),
(7, 'Amal Krishnan', 'amal.k@cdac.in', 'employee', 'Project Engineer', 6238625073, 'Thiruvananthapuram', 7, 105, 6, 'CCTF/0908240007', '2024-08-16 01:45:11'),
(8, 'JAMSHIYA BEEGAM V P', 'jamshiyab@cdac.in', 'employee', 'Senior Project Engineer', 8129424726, 'Thiruvananthapuram', 8, 105, 6, 'CCTF/0908240008', '2024-08-16 01:45:11'),
(9, 'VIPIN V', 'vipin.v@cdac.in', 'employee', 'Project Engineer', 7025187568, 'Thiruvananthapuram', 9, 105, 6, 'CCTF/0908240009', '2024-08-16 01:45:11'),
(10, 'Ashik Kabeer', 'ashik.kabeer@cdac.in', 'employee', 'Project Engineer', 9961063333, 'Thiruvananthapuram', 10, 105, 6, 'CCTF/0908240010', '2024-08-16 01:45:11'),
(11, 'kapil k', 'kapilk@cdac.in', 'employee', 'Project Engineer', 9177623185, 'Hyderabad', 11, 100, 6, 'CCTF/0908240011', '2024-08-16 01:45:11'),
(12, 'GANDIKOTA PREMKRISHNA', 'gpremkrishna@cdac.in', 'employee', 'Project Engineer', 9000701922, 'Hyderabad', 12, 100, 6, 'CCTF/0908240012', '2024-08-16 01:45:11'),
(13, 'Ganesh D', 'dganesh@cdac.in', 'employee', 'Project Engineer', 9700566200, 'Hyderabad', 13, 100, 6, 'CCTF/0908240013', '2024-08-16 01:45:11'),
(14, 'Abhijeet Thorat', 'abhijeett@cdac.in', 'employee', 'Project Engineer', 7028206003, 'Pune', 14, 100, 6, 'CCTF/0908240014', '2024-08-16 01:45:11'),
(15, 'Nachiket Vijay Maid', 'nachiketm@cdac.in', 'employee', 'Project Engineer', 8983125428, 'Pune', 15, 100, 6, 'CCTF/0908240015', '2024-08-16 01:45:11'),
(16, 'Srushti Anil Kantode', 'srushtik@cdac.in', 'employee', 'Principal Engineer', 7796331797, 'Hyderabad', 16, 100, 6, 'CCTF/0908240016', '2024-08-16 01:45:11'),
(17, 'Surajit Chakraborty', 'surajitc@cdac.in', 'employee', 'Project Engineer', 7002553839, 'Hyderabad', 17, 100, 6, 'CCTF/0908240017', '2024-08-16 01:45:11'),
(18, 'Ashutosh Jha', 'ashutoshj@cdac.in', 'employee', 'Scientist B', 9868911555, 'Patna', 18, 95, 6, 'CCTF/0908240018', '2024-08-16 01:45:11'),
(19, 'Shailendra Pratap Singh', 'shailendrasingh@cdac.in', 'employee', 'Scientist B', 8962549052, 'Patna', 19, 95, 6, 'CCTF/0908240019', '2024-08-16 01:45:11'),
(20, 'Prashant Srivastava', 'psrivastava@cdac.in', 'employee', 'Project Engineer', 7987351545, 'Patna', 20, 95, 6, 'CCTF/0908240020', '2024-08-16 01:45:11'),
(21, 'Shivam sharma', 'sshivam@cdac.in', 'employee', 'Project Engineer', 8077386739, 'Pune', 21, 85, 5, 'CCTF/0908240021', '2024-08-16 01:45:11'),
(22, 'Vishnu Vinodkumar', 'vishnu.vk@cdac.in', 'employee', 'Project Engineer', 8921636217, 'Thiruvananthapuram', 22, 85, 5, 'CCTF/0908240022', '2024-08-16 01:45:11'),
(23, 'JAI VAGWAN SINGH', 'jais@cdac.in', 'employee', 'Senior Project Engineer', 8789130052, 'Patna', 23, 80, 5, 'CCTF/0908240023', '2024-08-16 01:45:11'),
(24, 'Kanchan Arun Modhave', 'kanchanm@cdac.in', 'employee', 'Project Engineer', 9890980638, 'Hyderabad', 24, 75, 5, 'CCTF/0908240024', '2024-08-16 01:45:11'),
(25, 'Orvakanti Mahesh', 'omahesh@cdac.in', 'employee', 'Project Engineer', 6309594076, 'Hyderabad', 25, 75, 5, 'CCTF/0908240025', '2024-08-16 01:45:11'),
(26, 'Shubham Pathak', 'spathak@cdac.in', 'employee', 'Project Engineer', 9527085380, 'Pune', 26, 60, 4, 'CCTF/0908240026', '2024-08-16 01:45:11'),
(27, 'Krishnaveni S', 'krishnaveni@cdac.in', 'employee', 'Project Engineer', 8086262438, 'Thiruvananthapuram', 27, 60, 4, 'CCTF/0908240027', '2024-08-16 01:45:11'),
(28, 'VIDHYA K S', 'vidhyaks@cdac.in', 'employee', 'Senior Project Engineer', 9020164413, 'Thiruvananthapuram', 28, 60, 4, 'CCTF/0908240028', '2024-08-16 01:45:11'),
(29, 'Robin Alex Oommen', 'robin@cdac.in', 'employee', 'Project Engineer', 7907036685, 'Thiruvananthapuram', 29, 60, 4, 'CCTF/0908240029', '2024-08-16 01:45:11'),
(30, 'Sukhmeet Singh', 'sukhmeets@cdac.in', 'employee', 'Project Engineer', 9417869710, 'Mohali', 30, 60, 4, 'CCTF/0908240030', '2024-08-16 01:45:11'),
(31, 'Dewesh Kumar Kisku', 'deweshk@cdac.in', 'employee', 'Scientist B', 9547145855, 'Patna', 31, 50, 3, 'CCTF/0908240031', '2024-08-16 01:45:11'),
(32, 'Ashish Bisht', 'ashishbisht@cdac.in', 'employee', 'Project Engineer', 9166823265, 'Bangalore', 32, 50, 3, 'CCTF/0908240032', '2024-08-16 01:45:11'),
(33, 'Uday Gopinath', 'kunchalaudaygopinath@cdac.in', 'employee', 'Principal Engineer', 6303090871, 'Hyderabad', 33, 50, 3, 'CCTF/0908240033', '2024-08-16 01:45:11'),
(34, 'Kalpak Chepurwar', 'kalpakk@cdac.in', 'employee', 'Project Engineer', 8007636566, 'Pune', 34, 50, 4, 'CCTF/0908240034', '2024-08-16 01:45:11'),
(35, 'Gubbala Babajee', 'babajeeg@cdac.in', 'employee', 'Project Engineer', 9848762597, 'Hyderabad', 35, 40, 3, 'CCTF/0908240035', '2024-08-16 01:45:11'),
(36, 'Deep Narayan Nand', 'deepn@cdac.in', 'employee', 'Project Engineer', 9406941394, 'Pune', 36, 35, 3, 'CCTF/0908240036', '2024-08-16 01:45:11'),
(37, 'Vishnu Rai', 'vishnurai@cdac.in', 'employee', 'Project Engineer', 9654893168, 'Noida', 37, 35, 3, 'CCTF/0908240037', '2024-08-16 01:45:11'),
(38, 'Divyanshu Kumar', 'divyanshuk@cdac.in', 'employee', 'Scientist B', 7869897630, 'Silchar', 38, 35, 3, 'CCTF/0908240038', '2024-08-16 01:45:11'),
(39, 'Debabrata Doloi', 'debabrata.doloi@cdac.in', 'employee', 'Project Engineer', 7407118227, 'Kolkata', 39, 35, 3, 'CCTF/0908240039', '2024-08-16 01:45:11'),
(40, 'Abhishek Kumar', 'kumarabhishek@cdac.in', 'employee', 'Project Engineer', 7338472253, 'Noida', 40, 35, 3, 'CCTF/0908240040', '2024-08-16 01:45:11'),
(41, 'Mayur Bilapate', 'bmayur@cdac.in', 'employee', 'Project Engineer', 8369744751, 'Pune', 41, 30, 3, 'CCTF/0908240041', '2024-08-16 01:45:11'),
(42, 'sdasa', 'nadeemm@cdac.in', 'employee', 'Project Engineer', 8299550766, 'Hyderabad', 42, 25, 2, 'CCTF/0908240042', '2024-08-16 01:45:11'),
(43, 'Swetha M', 'mswetha@cdac.in', 'employee', 'Project Engineer', 8074754971, 'Hyderabad', 43, 25, 2, 'CCTF/0908240043', '2024-08-16 01:45:11'),
(44, 'Prashant Singh', 'prashants@cdac.in', 'employee', 'Scientist C', 9418446988, 'Hyderabad', 44, 20, 2, 'CCTF/0908240044', '2024-08-16 01:45:11'),
(45, 'Aditya Arsh', 'adityaarsh@cdac.in', 'employee', 'Project Engineer', 8709125727, 'Bangalore', 45, 20, 2, 'CCTF/0908240045', '2024-08-16 01:45:11'),
(46, 'Anushkairis Anushkairis', 'anushkairis@cdac.in', 'employee', 'Project Engineer', 9777927104, 'Patna', 46, 20, 2, 'CCTF/0908240046', '2024-08-16 01:45:11'),
(47, 'Kunal Hiremath', 'kunalhiremath@cdac.in', 'employee', 'Project Associate', 8884794130, 'Bangalore', 47, 20, 2, 'CCTF/0908240047', '2024-08-16 01:45:11'),
(48, 'Tanushree Gangwal', 'tanushreeg@cdac.in', 'employee', 'Project Engineer', 8619160252, 'Mohali', 48, 15, 2, 'CCTF/0908240048', '2024-08-16 01:45:11'),
(49, 'DEEKSHA KUSHWAH', 'deekshak@cdac.in', 'employee', 'Project Engineer', 7000793994, 'Pune', 49, 15, 2, 'CCTF/0908240049', '2024-08-16 01:45:11'),
(50, 'Aashay Pandharpatte', 'aashayp@cdac.in', 'employee', 'Knowledge Associate', 9763809908, 'Pune', 50, 15, 2, 'CCTF/0908240050', '2024-08-16 01:45:11'),
(51, 'Raj Suresh George', 'raj.suresh@cdac.in', 'employee', 'Project Engineer', 9074733605, 'Thiruvananthapuram', 51, 15, 2, 'CCTF/0908240051', '2024-08-16 01:45:11'),
(52, 'Premjith A V', 'premjith@cdac.in', 'employee', 'Project Manager', 9037387072, 'Thiruvananthapuram', 52, 15, 2, 'CCTF/0908240052', '2024-08-16 01:45:11'),
(53, 'Megha Kumari', 'meghakumari@cdac.in', 'employee', 'Knowledge Associate', 8770431177, 'Mohali', 53, 15, 2, 'CCTF/0908240053', '2024-08-16 01:45:11'),
(54, 'Divya NS', 'nsdivya@cdac.in', 'employee', 'Project Engineer', 7305800207, 'Thiruvananthapuram', 54, 15, 2, 'CCTF/0908240054', '2024-08-16 01:45:11'),
(55, 'Jomon K S', 'jomon@cdac.in', 'employee', 'Project Technician', 8075879460, 'Thiruvananthapuram', 55, 15, 2, 'CCTF/0908240055', '2024-08-16 01:45:11'),
(56, 'HRISHEESHWAR KAUSHIK', 'hrisheeshwark@cdac.in', 'employee', 'Senior Project Engineer', 9999872436, 'Pune', 56, 15, 2, 'CCTF/0908240056', '2024-08-16 01:45:11'),
(57, 'Akshita Sood', 'akshitas@cdac.in', 'employee', 'Project Engineer', 7018823314, 'Mohali', 57, 15, 2, 'CCTF/0908240057', '2024-08-16 01:45:11'),
(58, 'Arun Goyal', 'arungoyal@cdac.in', 'employee', 'Senior Project Engineer', 9560002174, 'Noida', 58, 10, 1, 'CCTF/0908240058', '2024-08-16 01:45:11'),
(59, 'Dhiraj Pralhad Ingale', 'dhiraj@cdac.in', 'employee', 'Project Engineer', 9987196594, 'Mumbai', 59, 5, 1, 'CCTF/0908240059', '2024-08-16 01:45:11'),
(60, 'Tazeen Khanam', 'tazeen.khan105@gmail.com', 'student', NULL, 8948163076, 'Delhi', 60, 5, 1, 'CCTF/0908240060', '2024-08-16 01:45:11'),
(61, 'Rupesh Malik', 'rupeshm@cdac.in', 'employee', 'Project Engineer', 7030179590, 'Delhi', 61, 5, 1, 'CCTF/0908240061', '2024-08-16 01:45:11'),
(62, 'Kirti Gupta', 'kirtiigupta12@gmail.com', 'student', NULL, 9315441339, 'Delhi', 62, 5, 1, 'CCTF/0908240062', '2024-08-16 01:45:11'),
(63, 'Reshma Ahmed', 'reshmahmed232@gmail.com', 'student', NULL, 8826438769, 'Delhi', 63, 5, 1, 'CCTF/0908240063', '2024-08-16 01:45:11'),
(64, 'Sreedevi S U', 'susreedevi@cdac.in', 'employee', 'Project Engineer', 8281043272, 'Thiruvananthapuram', 64, 5, 1, 'CCTF/0908240064', '2024-08-16 01:45:11'),
(65, 'Vaibhav Sunil Shinde', 'vshinde@cdac.in', 'employee', 'Project Engineer', 9130516066, 'Mumbai', 65, 5, 1, 'CCTF/0908240065', '2024-08-16 01:45:11'),
(66, 'Harshit Mishra', 'harshitmishra@cdac.in', 'employee', 'Project Engineer', 7011280747, 'Noida', 66, 5, 1, 'CCTF/0908240066', '2024-08-16 01:45:11'),
(67, 'Vineela Rani', 'vineelaranig@cdac.in', 'employee', 'Project Engineer', 7382496167, 'Hyderabad', 67, 5, 1, 'CCTF/0908240067', '2024-08-16 01:45:11'),
(68, 'Swagat ', 'swagat0034@gmail.com', 'student', NULL, 7627886744, 'Delhi', 68, 5, 1, 'CCTF/0908240068', '2024-08-16 01:45:11'),
(69, 'Malay Bhowmcick', 'malayb@cdac.in', 'employee', 'Knowledge Associate', 9875497939, 'Bangalore', 69, 5, 1, 'CCTF/0908240069', '2024-08-16 01:45:11'),
(70, 'Manoj Kumar', 'manojkr@cdac.in', 'employee', 'Project Engineer', 8810243320, 'Noida', 70, 5, 1, 'CCTF/0908240070', '2024-08-16 01:45:11'),
(71, 'Preeti Kumari', 'preetikumari@cdac.in', 'employee', 'Project Engineer', 8084444330, 'Bangalore', 71, 5, 1, 'CCTF/0908240071', '2024-08-16 01:45:11'),
(72, 'Nitesh Bharat Pawar', 'niteshb@cdac.in', 'employee', 'Project Associate', 7057022998, 'Bangalore', 72, 5, 1, 'CCTF/0908240072', '2024-08-16 01:45:11'),
(73, 'Yash Mayank Modi', 'myash@cdac.in', 'employee', 'Project Engineer', 7258993875, 'Patna', 73, 5, 1, 'CCTF/0908240073', '2024-08-16 01:45:11'),
(74, 'Himanshu Shekhar', 'hshekhar@cdac.in', 'employee', 'Project Engineer', 9112811563, 'Patna', 74, 5, 1, 'CCTF/0908240074', '2024-08-16 01:45:11'),
(75, 'Nebila Nizam N', 'nebila@cdac.in', 'employee', 'Project Engineer', 9048277788, 'Thiruvananthapuram', 75, 5, 1, 'CCTF/0908240075', '2024-08-16 01:45:11'),
(76, 'SHIVANI GAUTAM', 'shivanigautam@cdac.in', 'employee', 'Project Engineer', 8219094865, 'Mohali', 76, 5, 1, 'CCTF/0908240076', '2024-08-16 01:45:11'),
(77, 'Prathamesh Kadam', 'prathameshk@cdac.in', 'employee', 'Project Engineer', 9595209598, 'Pune', 77, 5, 1, 'CCTF/0908240077', '2024-08-16 01:45:11'),
(78, 'Ritika chaudhary', 'ritichaudhary112@gmail.com', 'student', NULL, 9557291374, 'Delhi', 78, 5, 1, 'CCTF/0908240078', '2024-08-16 01:45:11'),
(79, 'Mayur Lilhare', 'mayurl@cdac.in', 'employee', 'Project Engineer', 9098140954, 'Pune', 79, 5, 1, 'CCTF/0908240079', '2024-08-16 01:45:11'),
(80, 'Neelkumar Shah', 'neelkumars@cdac.in', 'employee', 'Project Engineer', 8866108533, 'Pune', 80, 5, 1, 'CCTF/0908240080', '2024-08-16 01:45:11'),
(81, 'Rohan Baghel', 'rohan.b@cdac.in', 'employee', 'Knowledge Associate', 8839437325, 'Thiruvananthapuram', 81, 5, 1, 'CCTF/0908240081', '2024-08-16 01:45:11'),
(82, 'Anurag Rajput', 'anuragr@cdac.in', 'employee', 'Scientist D', 9650804320, 'Delhi', 82, 5, 1, 'CCTF/0908240082', '2024-08-16 01:45:11'),
(83, 'Mahesh A', 'adsurekacharu@cdac.in', 'employee', 'Project Engineer', 7276466498, 'Noida', 83, 5, 1, 'CCTF/0908240083', '2024-08-16 01:45:11'),
(84, 'Sachin Kumar', 'kumarsachin@cdac.in', 'employee', 'Project Engineer', 7258889287, 'Patna', 84, 5, 1, 'CCTF/0908240084', '2024-08-16 01:45:11'),
(85, 'SRUTHI A K', 'sruthi.ak@cdac.in', 'employee', 'Project Associate', 9048721071, 'Thiruvananthapuram', 85, 5, 1, 'CCTF/0908240085', '2024-08-16 01:45:11'),
(86, 'Pankaj Gharde', 'pankajg@cdac.in', 'employee', 'Project Engineer', 8788131068, 'Pune', 86, 5, 1, 'CCTF/0908240086', '2024-08-16 01:45:11'),
(87, 'Saurabh Rai', 'saurabh.rai@cdac.in', 'employee', 'Knowledge Associate', 8810331987, 'Thiruvananthapuram', 87, 5, 1, 'CCTF/0908240087', '2024-08-16 01:45:11'),
(88, 'Priyanka Zaware', 'priyankaz@cdac.in', 'employee', 'Project Leader', 7276736504, 'Pune', 88, 5, 1, 'CCTF/0908240088', '2024-08-16 01:45:11'),
(89, 'MANJUNATH R', 'manjunath@cdac.in', 'employee', 'Knowledge Associate', 7760991818, 'Thiruvananthapuram', 89, 5, 1, 'CCTF/0908240089', '2024-08-16 01:45:11'),
(90, 'Jinesh Dutt Joshi', 'jineshdjoshi@cdac.in', 'employee', 'Project Engineer', 8559813490, 'Noida', 90, 5, 1, 'CCTF/0908240090', '2024-08-16 01:45:11'),
(91, 'Dinesh Santosh Sarode', 'dsarode@cdac.in', 'employee', 'Project Engineer', 9307701171, 'Pune', 91, 5, 1, 'CCTF/0908240091', '2024-08-16 01:45:11'),
(92, 'Prabhakar Mishra', 'prabhakarm@cdac.in', 'employee', 'Scientist B', 9650640402, 'Patna', 92, 5, 1, 'CCTF/0908240092', '2024-08-16 01:45:11'),
(93, 'Atharva Kulkarni', 'atharvak@cdac.in', 'employee', 'Project Engineer', 8554918262, 'Pune', 93, 5, 1, 'CCTF/0908240093', '2024-08-16 01:45:11'),
(94, 'Akshay Kaushik', 'akaushik@cdac.in', 'employee', 'Project Engineer', 9122587153, 'Bangalore', 94, 5, 1, 'CCTF/0908240094', '2024-08-16 01:45:11'),
(95, 'ANITA Sonawane', 'anitag@cdac.in', 'employee', 'Joint Director', 9689972040, 'Pune', 95, 5, 1, 'CCTF/0908240095', '2024-08-16 01:45:11'),
(96, 'Saishiva Reddy Gatla', 'gsaishivareddy@cdac.in', 'employee', 'Project Engineer', 9949855297, 'Hyderabad', 96, 5, 1, 'CCTF/0908240096', '2024-08-16 01:45:11'),
(97, 'Abhishek Fulmali', 'abhishek.bf@cdac.in', 'employee', 'Project Engineer', 8459014065, 'Thiruvananthapuram', 97, 5, 1, 'CCTF/0908240097', '2024-08-16 01:45:11'),
(98, 'ATHIRA  DAS', 'athira.das@cdac.in', 'employee', 'Project Engineer', 8606226248, 'Thiruvananthapuram', 98, 5, 1, 'CCTF/0908240098', '2024-08-16 01:45:11'),
(99, 'Swapna Yenishetti', 'swapnae@cdac.in', 'employee', 'Joint Director', 8055347620, 'Pune', 99, 5, 1, 'CCTF/0908240099', '2024-08-16 01:45:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `090824`
--
ALTER TABLE `090824`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cert_no` (`cert_no`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `090824`
--
ALTER TABLE `090824`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
