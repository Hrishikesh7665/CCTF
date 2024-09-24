-- Database: `cdac-k_ctf_archive`

CREATE TABLE `{{table_name}}` (
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

ALTER TABLE `{{table_name}}`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cert_no` (`cert_no`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `{{table_name}}`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
