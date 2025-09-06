-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 06, 2025 at 02:39 PM
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
-- Database: `truth_uncovered`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_messages`
--

CREATE TABLE `admin_messages` (
  `message_id` int(10) UNSIGNED NOT NULL,
  `admin_message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `alerts`
--

CREATE TABLE `alerts` (
  `Alert_ID` int(11) NOT NULL,
  `User_ID` int(11) DEFAULT NULL,
  `Alert_Type` enum('SMS','Email') DEFAULT NULL,
  `Message` text DEFAULT NULL,
  `Date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alerts`
--

INSERT INTO `alerts` (`Alert_ID`, `User_ID`, `Alert_Type`, `Message`, `Date`) VALUES
(501, 1, 'Email', 'Your report on bribery has been received.', '2025-08-20'),
(502, 3, 'SMS', 'Your report on unsafe building has been verified.', '2025-08-22'),
(503, 4, 'Email', 'Dowry case submitted successfully.', '2025-08-21'),
(504, 5, 'SMS', 'Harassment case is under investigation.', '2025-08-22'),
(505, 6, 'Email', 'Bridge collapse risk report received.', '2025-08-22'),
(506, 7, 'SMS', 'Extortion report verified.', '2025-08-23'),
(507, 8, 'Email', 'Land grabbing report is under review.', '2025-08-23'),
(508, 9, 'SMS', 'Fraudulent NGO case submitted.', '2025-08-24'),
(509, 10, 'Email', 'Child abuse case verified.', '2025-08-24'),
(510, 2, 'SMS', 'Corruption report at hospital received.', '2025-08-25');

-- --------------------------------------------------------

--
-- Table structure for table `analytics`
--

CREATE TABLE `analytics` (
  `Analytics_ID` int(11) NOT NULL,
  `Type` enum('Bribe Calculator','Trend Analysis','Heatmap') DEFAULT NULL,
  `Data_Snapshot` text DEFAULT NULL,
  `Generated_Date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `analytics`
--

INSERT INTO `analytics` (`Analytics_ID`, `Type`, `Data_Snapshot`, `Generated_Date`) VALUES
(901, 'Trend Analysis', 'Bribery rising in Dhaka region.', '2025-08-25'),
(902, 'Heatmap', 'Khulna marked as hazard hotspot.', '2025-08-26'),
(903, 'Trend Analysis', 'Dowry complaints increasing in Sylhet.', '2025-08-26'),
(904, 'Heatmap', 'Rajshahi flagged for harassment reports.', '2025-08-26'),
(905, 'Trend Analysis', 'Bridge hazards reported in Narayanganj.', '2025-08-26'),
(906, 'Heatmap', 'Extortion high in Barisal.', '2025-08-26'),
(907, 'Trend Analysis', 'Land grabbing cases in Mymensingh.', '2025-08-26'),
(908, 'Heatmap', 'Fraud hotspots in Rangpur.', '2025-08-26'),
(909, 'Trend Analysis', 'Child abuse cases increasing in Comilla.', '2025-08-26'),
(910, 'Heatmap', 'Hospital corruption widespread in Chattogram.', '2025-08-26');

-- --------------------------------------------------------

--
-- Table structure for table `blogposts`
--

CREATE TABLE `blogposts` (
  `Post_ID` int(11) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Slug` varchar(300) GENERATED ALWAYS AS (replace(lcase(`Title`),' ','-')) STORED,
  `Content` mediumtext NOT NULL,
  `Category` varchar(50) NOT NULL,
  `Author_ID` int(11) DEFAULT NULL,
  `Date_Published` date NOT NULL,
  `Status` enum('draft','published') DEFAULT 'published',
  `Published_At` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blogposts`
--

INSERT INTO `blogposts` (`Post_ID`, `Title`, `Content`, `Category`, `Author_ID`, `Date_Published`, `Status`, `Published_At`) VALUES
(7, 'Hospital Corruption Exposed', 'Hospital Corruption Exposed\r\nA citizen\'s report led to the investigation of a major hospital corruption scandal. Five officials were arrested, $2.1M in misappropriated funds recovered, and hospital services improved for over 50,000 patients in the Dhaka region.', 'Corruption', 2, '2025-09-06', 'published', '2025-09-06 16:09:35'),
(8, 'Environmental Violation Stopped', 'Environmental Violation Stopped\r\nIndustrial waste dumping into the Buriganga River was halted after a whistleblower\'s evidence. The company faced legal action, cleanup operations began, and 12 communities now have access to cleaner water.', 'Harassment', 2, '2025-09-06', 'published', '2025-09-06 16:10:25'),
(9, 'Environmental Violation Stopped', 'Industrial waste dumping into the Buriganga River was halted after a whistleblower\'s evidence. The company faced legal action, cleanup operations began, and 12 communities now have access to cleaner water.', 'Harassment', 2, '2025-09-06', 'published', '2025-09-06 16:10:45'),
(10, 'Education', 'Education \r\nSchool boFund Recoveryard corruption exposed through our platform resulted in the recovery of ৳15 lakh in education funds. A new oversight committee was established to prevent future misconduct.', 'Public Hazards', 2, '2025-09-06', 'published', '2025-09-06 16:11:35'),
(11, 'Workplace Safety Improved', 'Workplace Safety Improved\r\nAnonymous report about unsafe working conditions led to factory inspection and mandatory safety upgrades, protecting 300+ garment workers from potential hazards', 'Antisocial Behavior', 2, '2025-09-06', 'published', '2025-09-06 16:12:09');

-- --------------------------------------------------------

--
-- Table structure for table `cases`
--

CREATE TABLE `cases` (
  `Case_ID` int(11) NOT NULL,
  `Report_ID` int(11) NOT NULL,
  `Status` enum('Received','Verified','Under Investigation','Action Taken','Closed') DEFAULT 'Received',
  `Assigned_Agency` varchar(255) DEFAULT NULL,
  `Timeline` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`Timeline`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `Category_ID` int(11) NOT NULL,
  `Category_Name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`Category_ID`, `Category_Name`) VALUES
(1, 'Corruption'),
(2, 'Antisocial Behavior'),
(3, 'Public Hazards'),
(4, 'Harassment'),
(5, 'Dowry Violence');

-- --------------------------------------------------------

--
-- Table structure for table `institutions`
--

CREATE TABLE `institutions` (
  `Institution_ID` int(11) NOT NULL,
  `Name` varchar(200) NOT NULL,
  `Type` enum('Government','Police','NGO') NOT NULL,
  `Region` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `institutions`
--

INSERT INTO `institutions` (`Institution_ID`, `Name`, `Type`, `Region`) VALUES
(1, 'Anti-Corruption Commission', 'Government', 'Dhaka'),
(2, 'Dhaka Metropolitan Police', 'Police', 'Dhaka'),
(3, 'Bangladesh Police Headquarters', 'Police', 'Chittagong'),
(4, 'Ministry of Home Affairs', 'Government', 'Dhaka'),
(5, 'Ministry of Women and Children Affairs', 'Government', 'Rajshahi'),
(6, 'Bangladesh Bank', 'Government', 'Dhaka'),
(7, 'National Board of Revenue', 'Government', 'Sylhet'),
(8, 'Rapid Action Battalion - RAB', 'Police', 'Chittagong'),
(9, 'Detective Branch - DB', 'Police', 'Khulna'),
(10, 'Traffic Police', 'Police', 'Barishal'),
(11, 'Transparency International Bangladesh', 'NGO', 'Dhaka'),
(12, 'BRAC', 'NGO', 'Rangpur'),
(13, 'Ain o Salish Kendra', 'NGO', 'Dhaka'),
(14, 'Bangladesh Legal Aid and Services Trust', 'NGO', 'Mymensingh'),
(15, 'Manusher Jonno Foundation', 'NGO', 'Chittagong'),
(16, 'Odhikar', 'NGO', 'Dhaka'),
(17, 'Bangladesh Environmental Lawyers Association', 'NGO', 'Sylhet'),
(18, 'Dhaka Ahsania Mission', 'NGO', 'Khulna'),
(19, 'Proshika', 'NGO', 'Rajshahi'),
(20, 'ActionAid Bangladesh', 'NGO', 'Barishal');

-- --------------------------------------------------------

--
-- Table structure for table `rankings`
--

CREATE TABLE `rankings` (
  `Ranking_ID` int(11) NOT NULL,
  `Institution_ID` int(11) NOT NULL,
  `User_ID` int(11) NOT NULL,
  `Score` int(11) NOT NULL CHECK (`Score` >= 0 and `Score` <= 10),
  `Category` enum('Corruption','Harassment','Public Hazards','Dowry','Antisocial Behavior') NOT NULL,
  `Description` text NOT NULL,
  `Created_At` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rankings`
--

INSERT INTO `rankings` (`Ranking_ID`, `Institution_ID`, `User_ID`, `Score`, `Category`, `Description`, `Created_At`) VALUES
(1, 2, 1, 5, 'Public Hazards', 'opip', '2025-09-06 05:34:59'),
(2, 2, 1, 5, 'Public Hazards', 'opip', '2025-09-06 05:36:30'),
(3, 2, 1, 5, 'Public Hazards', 'opip', '2025-09-06 05:37:35'),
(4, 2, 1, 4, 'Corruption', 'jpjpookojnnlnoo;pbyf', '2025-09-06 05:38:09'),
(5, 2, 1, 6, 'Public Hazards', 'jcduefefjefjrgndiuue', '2025-09-06 05:39:09'),
(6, 2, 1, 6, 'Corruption', 'kofkrojgoeropjhjhjyjy', '2025-09-06 05:39:35'),
(7, 2, 1, 6, 'Corruption', 'kofkrojgoeropjhjhjyjy', '2025-09-06 05:39:51'),
(8, 1, 1, 4, 'Corruption', 'hioi[oiiuh8yhyyttttttt', '2025-09-06 06:05:58');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `Report_ID` int(11) NOT NULL,
  `User_ID` int(11) DEFAULT NULL,
  `Title` varchar(200) NOT NULL,
  `Description` text DEFAULT NULL,
  `Date_Submitted` date DEFAULT NULL,
  `Status` varchar(100) DEFAULT NULL,
  `Incident_Address` text DEFAULT NULL,
  `Incident_Division` varchar(50) NOT NULL DEFAULT 'Dhaka',
  `Incident_Date` date DEFAULT NULL,
  `Incident_Time` time DEFAULT NULL,
  `Category_ID` int(11) DEFAULT NULL,
  `AssignedAgency` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `mode` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`Report_ID`, `User_ID`, `Title`, `Description`, `Date_Submitted`, `Status`, `Incident_Address`, `Incident_Division`, `Incident_Date`, `Incident_Time`, `Category_ID`, `AssignedAgency`, `file_path`, `mode`) VALUES
(119, 1, 'sara', 'good', '2025-09-04', 'pending', 'ctg', 'Dhaka', '2025-10-04', '16:25:00', 1, 'ActionAid Bangladesh (NGO)', 'uploads/1756981373_sara.png', NULL),
(120, 1, 'tusher', 'bad', '2025-09-04', 'accepted', 'barishal', 'Dhaka', '2025-09-16', '16:29:00', 1, 'Ministry of Women and Children Affairs (Government)', 'uploads/1756981613_Untitled Diagram.drawio (1).png', NULL),
(121, 1, 'tusher', 'bad', '2025-09-04', 'pending', 'barishal', 'Dhaka', '2025-09-16', '16:29:00', 1, 'Ministry of Women and Children Affairs (Government)', 'uploads/1756981642_Untitled Diagram.drawio (1).png', NULL),
(122, 1, 'tusher', 'bad', '2025-09-04', 'pending', 'barishal', 'Dhaka', '2025-09-16', '16:29:00', 1, 'Ministry of Women and Children Affairs (Government)', 'uploads/1756981658_Untitled Diagram.drawio (1).png', NULL),
(123, 1, 'tusher', 'bad', '2025-09-04', 'accepted', 'barishal', 'Dhaka', '2025-09-16', '16:29:00', 1, 'Dhaka Ahsania Mission (NGO)', 'uploads/1756981731_Untitled Diagram.drawio (1).png', NULL),
(124, 1, 'tusher', 'bad', '2025-09-04', 'accepted', 'barishal', 'Dhaka', '2025-09-16', '16:29:00', 1, 'ActionAid Bangladesh (NGO)', 'uploads/1756981932_Untitled Diagram.drawio (1).png', NULL),
(125, 1, 'tusher', 'bad', '2025-09-04', 'pending', 'barishal', 'Dhaka', '2025-09-16', '16:29:00', 1, 'null', 'uploads/1756982011_Untitled Diagram.drawio (1).png', NULL),
(126, 1, 'tusher', 'bad', '2025-09-04', 'pending', 'barishal', 'Dhaka', '2025-09-16', '16:29:00', 1, 'Ministry of Women and Children Affairs (Government)', 'uploads/1756982088_Untitled Diagram.drawio (1).png', NULL),
(127, 1, 'zara', 'adguh', '2025-09-04', 'pending', 'bali', 'Dhaka', '2025-09-18', '22:38:00', 2, 'null', '', NULL),
(128, 1, 'zara', 'adguh', '2025-09-04', 'accepted', 'bali', 'Dhaka', '2025-09-18', '22:38:00', 2, 'null', 'uploads/1757003872_sara.png', NULL),
(129, 1, 'anonymous report ', 'anonymous report ', '2025-09-05', 'pending', 'Dhaka', 'Dhaka', '2025-09-20', '00:52:00', 1, 'ActionAid Bangladesh (NGO)', 'uploads/1757011700_Screenshot (526).png', 'anonymous'),
(130, 1, 'corruption', 'cckdslvs', '2025-09-05', 'Draft', 'sdfasfsadfsdadfsafd', 'Dhaka', '2025-09-10', '20:54:00', 2, NULL, 'uploads/1757069666_Screenshot (529).png', NULL),
(131, 1, 'dfrgtg', 'gthj', '2025-09-05', 'Draft', 'fgtg', 'Dhaka', '2025-09-07', '19:18:00', 1, NULL, '', NULL),
(132, 1, 'hazards', 'i gjdoojdoa', '2025-09-06', 'Draft', 'barishal', 'Dhaka', '2025-10-05', '01:43:00', 3, NULL, 'uploads/1757101261_Screenshot (530).png', NULL),
(133, 10, 'corruption found in Brac bank', 'Yesterday I visited in brac bank and saw some customers gave bribe to the bank accountent to get the cary asap', '2025-09-06', 'declined', 'barishal', 'Dhaka', '2025-09-24', '01:00:00', 1, 'ActionAid Bangladesh (NGO)', 'uploads/1757102159_Screenshot 2025-08-27 224130.png', NULL),
(134, 10, 'public trasport hazards', 'public tranport hazards are become one of the crucial problem in bangladesh', '2025-09-06', 'Draft', 'khulna', 'Dhaka', '2025-09-25', '06:25:00', 3, NULL, 'uploads/1757103944_Screenshot (529).png', NULL),
(135, 10, '18 years old girl has beaten for dowry', 'In my nearest village 18 years old girl has beaten for dowry', '2025-09-06', 'Draft', 'dhaka', 'Dhaka', '2025-10-01', '02:30:00', 5, NULL, 'uploads/1757104058_Screenshot 2025-08-17 225902.png', NULL),
(136, 10, 'harassment issue', 'feokfpdgsgsg', '2025-09-06', 'Draft', 'chittagong', 'Dhaka', '2025-09-28', '02:34:00', 4, NULL, '', NULL),
(137, 10, 'ijpo', 'mokp[d', '2025-09-06', 'Draft', 'oifi0fi3p', 'Barishal', '2025-08-28', '11:59:00', 1, NULL, 'uploads/1757138120_Screenshot (530).png', NULL),
(138, 1, 'njiop', 'mkp', '2025-09-06', 'Draft', 'ffffffrfsdfsadfsadfs', 'Sylhet', '2025-09-06', '15:06:00', 2, NULL, 'uploads/1757149609_Screenshot (526).png', NULL);

-- --------------------------------------------------------

--
-- Stand-in structure for view `reports_with_users`
-- (See below for the actual view)
--
CREATE TABLE `reports_with_users` (
`Report_ID` int(11)
,`Title` varchar(200)
,`Description` text
,`Date_Submitted` date
,`Status` varchar(100)
,`Category_ID` int(11)
,`AssignedAgency` varchar(255)
,`User_Name` varchar(100)
);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `User_ID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `DOB` date DEFAULT NULL,
  `National_ID` varchar(50) DEFAULT NULL,
  `Gender` varchar(10) DEFAULT NULL,
  `Role` enum('Citizen','Admin','NGO_Partner','Govt_Officer') DEFAULT NULL,
  `Street` varchar(150) DEFAULT NULL,
  `City` varchar(100) DEFAULT NULL,
  `Region` varchar(100) DEFAULT NULL,
  `Postal_Code` varchar(20) DEFAULT NULL,
  `Sub_SMS` tinyint(1) DEFAULT NULL,
  `Sub_Email` tinyint(1) DEFAULT NULL,
  `Sub_Blog_Following` tinyint(1) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`User_ID`, `Name`, `Email`, `Phone`, `DOB`, `National_ID`, `Gender`, `Role`, `Street`, `City`, `Region`, `Postal_Code`, `Sub_SMS`, `Sub_Email`, `Sub_Blog_Following`, `Password`) VALUES
(1, 'Alice Rahman Sarika', 'alice.rahman@example.com', '01710001122', '1990-05-15', 'NID1234567890', 'Female', 'Citizen', '12 Main St', 'Dhaka', 'Dhaka', '1207', 1, 1, 0, '12345678'),
(2, 'Admin Sarika    ', 'Admin.sarika@gmail.com', '01820002233', '1985-11-20', 'NID9876543210', 'Female', 'Admin', '45 Central Ave', 'Chittagong', 'Chattogram', '5000', 1, 0, 0, '$2y$10$3od9s5P7MaS14yygpxp.b.OuZu7GxMW.GltXeUReLWRsBQvKLwju6'),
(3, 'Shila Akter', 'shila.akter@example.com', '01930003344', '1992-07-08', 'NID5432167890', 'Female', 'NGO_Partner', '89 Pine Rd', 'Khulna', 'Khulna', '9000', 1, 0, 1, NULL),
(4, 'Rashid Karim', 'rashid.karim@example.com', '01640004455', '1995-03-12', 'NID3216549870', 'Male', 'Citizen', '22 Lake Rd', 'Sylhet', 'Sylhet', '3100', 1, 1, 1, NULL),
(5, 'Tania Jahan', 'tania.jahan@example.com', '01550005566', '1993-09-30', 'NID8529637410', 'Female', 'Citizen', '76 Rose St', 'Rajshahi', 'Rajshahi', '6000', 0, 1, 0, NULL),
(6, 'Imran Chowdhury', 'imran.chowdhury@example.com', '01760006677', '1988-01-25', 'NID7418529630', 'Male', 'Govt_Officer', '90 Green Ave', 'Dhaka', 'Dhaka', '1212', 1, 0, 1, NULL),
(7, 'Maya Sultana', 'maya.sultana@example.com', '01870007788', '1991-12-11', 'NID9638527410', 'Female', 'NGO_Partner', '33 Lotus Ln', 'Barisal', 'Barisal', '8200', 1, 1, 0, NULL),
(8, 'Naimur Rahman', 'naimur.rahman@example.com', '01980008899', '1994-06-14', 'NID1593574860', 'Male', 'Citizen', '55 Sunflower Rd', 'Mymensingh', 'Mymensingh', '2200', 0, 1, 1, NULL),
(9, 'Lamia Khatun', 'lamia.khatun@gmail.com', '01690009900', '1990-10-05', 'NID2589631470', 'Female', 'Citizen', '21 Orchid St', 'Rangpur', 'Rangpur', '5400', 1, 0, 1, '12345678\r\n'),
(10, 'Rahul Ahmed', 'rahul.ahmed@example.com', '01700001133', '1987-02-19', 'NID7539514560', 'Male', 'Citizen', '11 Park Ln', 'Comilla', 'Comilla', '3500', 1, 1, 1, '12345678'),
(29, 'nazia', 'nazia@gmail.com', '018181763513', '2025-09-11', '445678904325', 'Female', 'Citizen', 'badda', 'dhaka', 'bangladesh', '5609', 0, 1, 0, '$2y$10$C71OADwQkIc2uNC79J.ZBOUqpBoUOeeK2V/s64Oo92F4cBtZZtt2a'),
(33, 'kamal', 'kamal@gmailcom', '01234567890', '2025-09-13', '097632345678', 'Male', 'Citizen', 'mohakhali', 'dhaka', 'bangladesh', '4203', 0, 1, 0, '$2y$10$V2nJUIoM8gJi1W22b5T2SeIMWFNYWahLRZWzAayUoheVAuWTrFcWC');

-- --------------------------------------------------------

--
-- Structure for view `reports_with_users`
--
DROP TABLE IF EXISTS `reports_with_users`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `reports_with_users`  AS SELECT `r`.`Report_ID` AS `Report_ID`, `r`.`Title` AS `Title`, `r`.`Description` AS `Description`, `r`.`Date_Submitted` AS `Date_Submitted`, `r`.`Status` AS `Status`, `r`.`Category_ID` AS `Category_ID`, `r`.`AssignedAgency` AS `AssignedAgency`, `u`.`Name` AS `User_Name` FROM (`reports` `r` join `users` `u` on(`r`.`User_ID` = `u`.`User_ID`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_messages`
--
ALTER TABLE `admin_messages`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `alerts`
--
ALTER TABLE `alerts`
  ADD PRIMARY KEY (`Alert_ID`),
  ADD KEY `User_ID` (`User_ID`);

--
-- Indexes for table `analytics`
--
ALTER TABLE `analytics`
  ADD PRIMARY KEY (`Analytics_ID`);

--
-- Indexes for table `blogposts`
--
ALTER TABLE `blogposts`
  ADD PRIMARY KEY (`Post_ID`);

--
-- Indexes for table `cases`
--
ALTER TABLE `cases`
  ADD PRIMARY KEY (`Case_ID`),
  ADD KEY `Report_ID` (`Report_ID`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`Category_ID`) USING BTREE;

--
-- Indexes for table `institutions`
--
ALTER TABLE `institutions`
  ADD PRIMARY KEY (`Institution_ID`);

--
-- Indexes for table `rankings`
--
ALTER TABLE `rankings`
  ADD PRIMARY KEY (`Ranking_ID`),
  ADD KEY `idx_institution_rankings` (`Institution_ID`),
  ADD KEY `idx_user_rankings` (`User_ID`),
  ADD KEY `idx_category` (`Category`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`Report_ID`),
  ADD KEY `User_ID` (`User_ID`),
  ADD KEY `Category_ID` (`Category_ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`User_ID`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `National_ID` (`National_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_messages`
--
ALTER TABLE `admin_messages`
  MODIFY `message_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blogposts`
--
ALTER TABLE `blogposts`
  MODIFY `Post_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `cases`
--
ALTER TABLE `cases`
  MODIFY `Case_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rankings`
--
ALTER TABLE `rankings`
  MODIFY `Ranking_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `Report_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `User_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alerts`
--
ALTER TABLE `alerts`
  ADD CONSTRAINT `alerts_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`User_ID`);

--
-- Constraints for table `cases`
--
ALTER TABLE `cases`
  ADD CONSTRAINT `cases_ibfk_1` FOREIGN KEY (`Report_ID`) REFERENCES `reports` (`Report_ID`);

--
-- Constraints for table `rankings`
--
ALTER TABLE `rankings`
  ADD CONSTRAINT `fk_rankings_user` FOREIGN KEY (`User_ID`) REFERENCES `users` (`User_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `rankings_ibfk_1` FOREIGN KEY (`Institution_ID`) REFERENCES `institutions` (`Institution_ID`) ON DELETE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`User_ID`),
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`Category_ID`) REFERENCES `categories` (`Category_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
