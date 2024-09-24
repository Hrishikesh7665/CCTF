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
-- Database: `cdac-k_ctf`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `CheckOTPGenerationPossibility` (IN `userId` INT(11), OUT `isPossible` BOOLEAN, OUT `errorMessage` VARCHAR(255))   BEGIN
    DECLARE otpCount INT;
    DECLARE currentTime TIMESTAMP;
    DECLARE fiveMinutesAgo TIMESTAMP;
    DECLARE oneHourAgo TIMESTAMP;

    -- Get current time
    SET currentTime = NOW();
    -- Calculate time intervals
    SET fiveMinutesAgo = DATE_SUB(currentTime, INTERVAL 5 MINUTE);
    SET oneHourAgo = DATE_SUB(currentTime, INTERVAL 45 MINUTE);

    -- Check if the user has generated less than 5 OTPs in the last 30 minutes
    SELECT COUNT(*)
    INTO otpCount
    FROM logs__otp
    WHERE user_Id = userId AND generated_at >= oneHourAgo;

    IF otpCount < 5 THEN
        -- Check if the user has generated an OTP in the last 5 minutes
        SELECT COUNT(*)
        INTO otpCount
        FROM logs__otp
        WHERE user_Id = userId AND generated_at >= fiveMinutesAgo;

        IF otpCount = 0 THEN
            SET isPossible = TRUE;
            SET errorMessage = NULL;
        ELSE
            SET isPossible = FALSE;
            SET errorMessage = 'An OTP is already generated and its valid for 5 minutes.';
        END IF;
    ELSE
        SET isPossible = FALSE;
        SET errorMessage = '5 OTP\'s generated in last 45 minutes.';
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `MoveToLogAndDelete` ()   BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE reg_id INT;
    DECLARE reg_name VARCHAR(50);
    DECLARE reg_email VARCHAR(100);
    DECLARE reg_phoneNumber BIGINT(11);
    DECLARE reg_ts DATETIME;
    DECLARE reg_location INT;

    DECLARE cur CURSOR FOR SELECT id, name, email, phoneNumber, creation_ts, location FROM temp__registration;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN cur;

    read_loop: LOOP
        FETCH cur INTO reg_id, reg_name, reg_email, reg_phoneNumber, reg_ts, reg_location;
        IF done THEN
            LEAVE read_loop;
        END IF;

        IF TIMESTAMPDIFF(MINUTE, reg_ts, NOW()) > 14 THEN
            INSERT INTO logs__not_registered (name, email, phoneNumber, location) VALUES (reg_name, reg_email, reg_phoneNumber, reg_location);
            DELETE FROM temp__registration WHERE id = reg_id;
        END IF;
    END LOOP;

    CLOSE cur;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `cat_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`cat_id`, `name`) VALUES
(2, 'Easy'),
(4, 'Hard'),
(3, 'Medium'),
(5, 'Surprise'),
(1, 'Welcome Bonus');

-- --------------------------------------------------------

--
-- Table structure for table `challenges`
--

CREATE TABLE `challenges` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `flag` varchar(100) NOT NULL,
  `score` int(11) NOT NULL,
  `file` varchar(500) DEFAULT NULL,
  `cat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `challenges`
--

INSERT INTO `challenges` (`id`, `title`, `description`, `flag`, `score`, `file`, `cat_id`) VALUES
(1, 'Welcome Challenge', 'Welcome to the CDAC CTF competition! As a warm-up, we\'ve prepared a simple challenge to get you started. Your task is to locate the hidden flag within this message. This will help you get accustomed to the types of puzzles you\'ll encounter.<br><b>Flag: CDAC_CTF_FLAG{IQCZQIKHLA}</b><br><br><b><i>Good luck, for the CTF!</b></i>', 'CDAC_CTF_FLAG{IQCZQIKHLA}', 5, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `feedback__challenges`
--

CREATE TABLE `feedback__challenges` (
  `id` int(11) NOT NULL,
  `challenge_id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `feedback` text NOT NULL,
  `rating` int(1) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback__platform`
--

CREATE TABLE `feedback__platform` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `feedback` text NOT NULL,
  `advice` text NOT NULL,
  `rating` int(1) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback__platform`
--

-- --------------------------------------------------------

--
-- Table structure for table `list__center`
--

CREATE TABLE `list__center` (
  `center_id` int(11) NOT NULL,
  `center` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `list__center`
--

-- --------------------------------------------------------

--
-- Table structure for table `list__designation`
--

CREATE TABLE `list__designation` (
  `designation_id` int(11) NOT NULL,
  `designation` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `list__designation`
--

-- --------------------------------------------------------

--
-- Table structure for table `logs__auth`
--

CREATE TABLE `logs__auth` (
  `log_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `time_stamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `ts_hash` text DEFAULT NULL,
  `remark` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs__auth`
--

-- --------------------------------------------------------

--
-- Table structure for table `logs__flag`
--

CREATE TABLE `logs__flag` (
  `flagLog_id` int(11) NOT NULL,
  `u_id` int(11) NOT NULL,
  `c_id` int(11) NOT NULL,
  `submitted_flag` varchar(100) NOT NULL,
  `flag_status` tinyint(1) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs__flag`
--

-- --------------------------------------------------------

--
-- Table structure for table `logs__notification`
--

CREATE TABLE `logs__notification` (
  `log_id` int(11) NOT NULL,
  `n_id` int(11) NOT NULL,
  `u_id` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `logs__not_registered`
--

CREATE TABLE `logs__not_registered` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phoneNumber` bigint(11) DEFAULT NULL,
  `ts` datetime NOT NULL DEFAULT current_timestamp(),
  `location` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs__not_registered`
--

-- --------------------------------------------------------

--
-- Table structure for table `logs__otp`
--

CREATE TABLE `logs__otp` (
  `otp_Id` int(11) NOT NULL,
  `user_Id` int(11) NOT NULL,
  `otp` int(6) NOT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `logs__qs`
--

CREATE TABLE `logs__qs` (
  `qlog_id` int(11) NOT NULL,
  `u_id` int(11) NOT NULL,
  `c_id` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs__qs`
--

-- --------------------------------------------------------

--
-- Table structure for table `logs__user_activity`
--

CREATE TABLE `logs__user_activity` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `field_name` varchar(50) NOT NULL,
  `old_value` varchar(150) DEFAULT NULL,
  `new_value` varchar(150) DEFAULT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs__user_activity`
--

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `id` int(11) NOT NULL,
  `title` varchar(256) NOT NULL,
  `description` text NOT NULL,
  `activeTime` timestamp NULL DEFAULT NULL,
  `expiredTime` timestamp NULL DEFAULT NULL,
  `role` longtext NOT NULL CHECK (json_valid(`role`)),
  `state` text NOT NULL,
  `ts` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notification`
--

-- --------------------------------------------------------

--
-- Table structure for table `scoreboard`
--

CREATE TABLE `scoreboard` (
  `s_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `c_id` int(11) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scoreboard`
--

-- --------------------------------------------------------

--
-- Table structure for table `temp__registration`
--

CREATE TABLE `temp__registration` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phoneNumber` bigint(11) DEFAULT NULL,
  `special_key` mediumtext DEFAULT NULL,
  `creation_ts` datetime NOT NULL DEFAULT current_timestamp(),
  `location` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(10) DEFAULT 'user',
  `status` varchar(5) NOT NULL DEFAULT 'false',
  `profession` varchar(11) DEFAULT NULL,
  `designation` int(11) DEFAULT NULL,
  `phoneNumber` bigint(11) DEFAULT NULL,
  `displayPic` varchar(256) DEFAULT NULL,
  `auth_type` varchar(5) NOT NULL DEFAULT 'self',
  `creation_ts` datetime NOT NULL DEFAULT current_timestamp(),
  `location` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `status`, `profession`, `designation`, `phoneNumber`, `displayPic`, `auth_type`, `creation_ts`, `location`) VALUES
(1, 'CTF Kolkata', 'ctf-kol@cdac.in', '$2y$10$0cDuwS5LUQ63DfGcvmR5fefWBiJzNsCGDrhH2Xv6iJoKrmM2MPTwK', 'admin', 'true', 'employee', 3, 8240937669, 'avatar_65db3020ea27e.png', 'self', '2024-03-24 13:21:36', 5);

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `after_user_update` AFTER UPDATE ON `users` FOR EACH ROW BEGIN
    DECLARE old_location VARCHAR(255);
    DECLARE new_location VARCHAR(255);
    DECLARE old_designation VARCHAR(255);
    DECLARE new_designation VARCHAR(255);
    DECLARE old_name VARCHAR(255);
    DECLARE new_name VARCHAR(255);

    IF OLD.profession != NEW.profession THEN
        INSERT INTO logs__user_activity (user_id, field_name, old_value, new_value, updated_at)
        VALUES (OLD.id, 'profession', OLD.profession, NEW.profession, NOW());
    END IF;

    -- Fetching OLD.designation value from a table based on ID
    SELECT list__designation.designation INTO old_designation FROM list__designation WHERE list__designation.designation_id=OLD.designation;

    -- Fetching NEW.designation value from a table based on ID
    SELECT list__designation.designation INTO new_designation FROM list__designation WHERE list__designation.designation_id=NEW.designation;

    IF OLD.designation != NEW.designation THEN
        INSERT INTO logs__user_activity (user_id, field_name, old_value, new_value, updated_at)
        VALUES (OLD.id, 'designation', old_designation, new_designation, NOW());
    END IF;

    -- Fetching OLD.location value from a table based on ID
    SELECT list__center.center INTO old_location FROM list__center WHERE list__center.center_id=OLD.location;

    -- Fetching NEW.location value from a table based on ID
    SELECT list__center.center INTO new_location FROM list__center WHERE list__center.center_id=NEW.location;

    IF OLD.location != NEW.location THEN
        INSERT INTO logs__user_activity (user_id, field_name, old_value, new_value, updated_at)
        VALUES (OLD.id, 'location', old_location, new_location, NOW());
    END IF;

    IF OLD.phoneNumber != NEW.phoneNumber THEN
        INSERT INTO logs__user_activity (user_id, field_name, old_value, new_value, updated_at)
        VALUES (OLD.id, 'phoneNumber', OLD.phoneNumber, NEW.phoneNumber, NOW());
    END IF;

    IF OLD.displayPic != NEW.displayPic THEN
        INSERT INTO logs__user_activity (user_id, field_name, old_value, new_value, updated_at)
        VALUES (OLD.id, 'displayPic', OLD.displayPic, NEW.displayPic, NOW());
    END IF;

    IF OLD.password != NEW.password THEN
        INSERT INTO logs__user_activity (user_id, field_name, old_value, new_value, updated_at)
        VALUES (OLD.id, 'password', OLD.password, NEW.password, NOW());
    END IF;

    IF OLD.name != NEW.name THEN
        INSERT INTO logs__user_activity (user_id, field_name, old_value, new_value, updated_at)
        VALUES (OLD.id, 'name', OLD.name, NEW.name, NOW());
    END IF;
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`cat_id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `name_2` (`name`);

--
-- Indexes for table `challenges`
--
ALTER TABLE `challenges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `flag` (`flag`),
  ADD KEY `cat_id` (`cat_id`);

--
-- Indexes for table `feedback__challenges`
--
ALTER TABLE `feedback__challenges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `challenge_id` (`challenge_id`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `feedback__platform`
--
ALTER TABLE `feedback__platform`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `userId` (`userId`);

--
-- Indexes for table `list__center`
--
ALTER TABLE `list__center`
  ADD PRIMARY KEY (`center_id`),
  ADD UNIQUE KEY `center` (`center`);

--
-- Indexes for table `list__designation`
--
ALTER TABLE `list__designation`
  ADD PRIMARY KEY (`designation_id`),
  ADD UNIQUE KEY `designation` (`designation`);

--
-- Indexes for table `logs__auth`
--
ALTER TABLE `logs__auth`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `logs__flag`
--
ALTER TABLE `logs__flag`
  ADD PRIMARY KEY (`flagLog_id`);

--
-- Indexes for table `logs__notification`
--
ALTER TABLE `logs__notification`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `n_id` (`n_id`),
  ADD KEY `u_id` (`u_id`);

--
-- Indexes for table `logs__not_registered`
--
ALTER TABLE `logs__not_registered`
  ADD PRIMARY KEY (`id`),
  ADD KEY `location` (`location`);

--
-- Indexes for table `logs__otp`
--
ALTER TABLE `logs__otp`
  ADD PRIMARY KEY (`otp_Id`),
  ADD KEY `user_Id` (`user_Id`);

--
-- Indexes for table `logs__qs`
--
ALTER TABLE `logs__qs`
  ADD PRIMARY KEY (`qlog_id`);

--
-- Indexes for table `logs__user_activity`
--
ALTER TABLE `logs__user_activity`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scoreboard`
--
ALTER TABLE `scoreboard`
  ADD PRIMARY KEY (`s_id`,`user_id`,`c_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `c_id` (`c_id`);

--
-- Indexes for table `temp__registration`
--
ALTER TABLE `temp__registration`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phoneNumber` (`phoneNumber`),
  ADD KEY `location` (`location`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phoneNumber` (`phoneNumber`),
  ADD KEY `location` (`location`),
  ADD KEY `designation` (`designation`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `challenges`
--
ALTER TABLE `challenges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `feedback__challenges`
--
ALTER TABLE `feedback__challenges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback__platform`
--
ALTER TABLE `feedback__platform`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `list__center`
--
ALTER TABLE `list__center`
  MODIFY `center_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `list__designation`
--
ALTER TABLE `list__designation`
  MODIFY `designation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `logs__auth`
--
ALTER TABLE `logs__auth`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1469;

--
-- AUTO_INCREMENT for table `logs__flag`
--
ALTER TABLE `logs__flag`
  MODIFY `flagLog_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=655;

--
-- AUTO_INCREMENT for table `logs__notification`
--
ALTER TABLE `logs__notification`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `logs__not_registered`
--
ALTER TABLE `logs__not_registered`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `logs__otp`
--
ALTER TABLE `logs__otp`
  MODIFY `otp_Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `logs__qs`
--
ALTER TABLE `logs__qs`
  MODIFY `qlog_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=642;

--
-- AUTO_INCREMENT for table `logs__user_activity`
--
ALTER TABLE `logs__user_activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `scoreboard`
--
ALTER TABLE `scoreboard`
  MODIFY `s_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=276;

--
-- AUTO_INCREMENT for table `temp__registration`
--
ALTER TABLE `temp__registration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=180;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `challenges`
--
ALTER TABLE `challenges`
  ADD CONSTRAINT `challenges_ibfk_1` FOREIGN KEY (`cat_id`) REFERENCES `category` (`cat_id`) ON DELETE CASCADE;

--
-- Constraints for table `feedback__challenges`
--
ALTER TABLE `feedback__challenges`
  ADD CONSTRAINT `feedback__challenges_ibfk_1` FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`),
  ADD CONSTRAINT `feedback__challenges_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `users` (`id`);

--
-- Constraints for table `feedback__platform`
--
ALTER TABLE `feedback__platform`
  ADD CONSTRAINT `feedback__platform_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`);

--
-- Constraints for table `logs__notification`
--
ALTER TABLE `logs__notification`
  ADD CONSTRAINT `logs__notification_ibfk_1` FOREIGN KEY (`n_id`) REFERENCES `notification` (`id`),
  ADD CONSTRAINT `logs__notification_ibfk_2` FOREIGN KEY (`u_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `logs__not_registered`
--
ALTER TABLE `logs__not_registered`
  ADD CONSTRAINT `logs__not_registered_ibfk_1` FOREIGN KEY (`location`) REFERENCES `list__center` (`center_id`);

--
-- Constraints for table `logs__otp`
--
ALTER TABLE `logs__otp`
  ADD CONSTRAINT `logs__otp_ibfk_1` FOREIGN KEY (`user_Id`) REFERENCES `users` (`id`);

--
-- Constraints for table `logs__user_activity`
--
ALTER TABLE `logs__user_activity`
  ADD CONSTRAINT `fk_logs__user_activity_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `scoreboard`
--
ALTER TABLE `scoreboard`
  ADD CONSTRAINT `scoreboard_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `scoreboard_ibfk_2` FOREIGN KEY (`c_id`) REFERENCES `challenges` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `temp__registration`
--
ALTER TABLE `temp__registration`
  ADD CONSTRAINT `temp__registration_ibfk_1` FOREIGN KEY (`location`) REFERENCES `list__center` (`center_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`location`) REFERENCES `list__center` (`center_id`),
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`designation`) REFERENCES `list__designation` (`designation_id`);

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`phpmyadmin`@`localhost` EVENT `call_MoveToLogAndDelete` ON SCHEDULE EVERY 60 SECOND STARTS '2024-04-17 06:07:19' ON COMPLETION NOT PRESERVE ENABLE DO CALL `MoveToLogAndDelete`()$$

CREATE DEFINER=`phpmyadmin`@`localhost` EVENT `autoUserActive` ON SCHEDULE EVERY 1 MINUTE STARTS '2024-08-01 11:23:05' ON COMPLETION NOT PRESERVE DISABLE DO UPDATE users
    SET status = 'true'
    WHERE status = 'false'
    AND TIMESTAMPDIFF(MINUTE, creation_ts, NOW()) > 15$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
