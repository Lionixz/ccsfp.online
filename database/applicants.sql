-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 19, 2026 at 01:34 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ccsfp`
--

-- --------------------------------------------------------

--
-- Table structure for table `applicants`
--

CREATE TABLE `applicants` (
  `id` int(11) NOT NULL,
  `google_id` varchar(255) NOT NULL,
  `course_first` varchar(100) NOT NULL,
  `course_second` varchar(100) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `birth_place` varchar(255) DEFAULT NULL,
  `marital_status` varchar(50) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `religion` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `home_address` varchar(255) DEFAULT NULL,
  `relative_name` varchar(255) DEFAULT NULL,
  `relative_address` varchar(255) DEFAULT NULL,
  `college` varchar(255) DEFAULT NULL,
  `college_course` varchar(100) DEFAULT NULL,
  `college_address` varchar(255) DEFAULT NULL,
  `college_year` varchar(20) DEFAULT NULL,
  `shs` varchar(255) DEFAULT NULL,
  `shs_year` int(11) DEFAULT NULL,
  `shs_address` varchar(255) DEFAULT NULL,
  `shs_lrn` varchar(50) DEFAULT NULL,
  `shs_awards` varchar(255) DEFAULT NULL,
  `jhs` varchar(255) DEFAULT NULL,
  `jhs_year` int(11) DEFAULT NULL,
  `jhs_address` varchar(255) DEFAULT NULL,
  `jhs_awards` varchar(255) DEFAULT NULL,
  `primary_school` varchar(255) DEFAULT NULL,
  `primary_year` int(11) DEFAULT NULL,
  `skills` varchar(255) DEFAULT NULL,
  `sports` varchar(255) DEFAULT NULL,
  `father_name` varchar(255) DEFAULT NULL,
  `father_occupation` varchar(255) DEFAULT NULL,
  `father_employer` varchar(255) DEFAULT NULL,
  `mother_name` varchar(255) DEFAULT NULL,
  `mother_occupation` varchar(255) DEFAULT NULL,
  `mother_employer` varchar(255) DEFAULT NULL,
  `guardian_name` varchar(255) DEFAULT NULL,
  `guardian_occupation` varchar(255) DEFAULT NULL,
  `guardian_employer` varchar(255) DEFAULT NULL,
  `guardian_address` varchar(255) DEFAULT NULL,
  `guardian_contact` varchar(50) DEFAULT NULL,
  `family_income` varchar(50) DEFAULT NULL,
  `how_heard` varchar(255) DEFAULT NULL,
  `sibling_names` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sibling_names`)),
  `sibling_educations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sibling_educations`)),
  `sibling_occupations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sibling_occupations`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applicants`
--

INSERT INTO `applicants` (`id`, `google_id`, `course_first`, `course_second`, `photo`, `last_name`, `first_name`, `middle_name`, `age`, `gender`, `dob`, `birth_place`, `marital_status`, `contact`, `religion`, `email`, `home_address`, `relative_name`, `relative_address`, `college`, `college_course`, `college_address`, `college_year`, `shs`, `shs_year`, `shs_address`, `shs_lrn`, `shs_awards`, `jhs`, `jhs_year`, `jhs_address`, `jhs_awards`, `primary_school`, `primary_year`, `skills`, `sports`, `father_name`, `father_occupation`, `father_employer`, `mother_name`, `mother_occupation`, `mother_employer`, `guardian_name`, `guardian_occupation`, `guardian_employer`, `guardian_address`, `guardian_contact`, `family_income`, `how_heard`, `sibling_names`, `sibling_educations`, `sibling_occupations`, `created_at`, `updated_at`) VALUES
(2, '105234567890123456789', 'BSED-ENG', 'BSED-SCI', '1771342702_images.jpg', 'Santos', 'Maria', 'Luna', 20, 'Female', '2003-08-25', 'Quezon City', 'Single', '09221234567', 'Roman Catholic', 'maria.santos@email.com', '45 Mabini St, Quezon City', 'Jose Santos', '67 Rizal Ave, Manila', 'Ateneo de Manila University', 'BS Information Technology', 'Loyola Heights, Quezon City', '2nd Year', 'Quezon City Science High School', 2020, 'Bago Bantay, Quezon City', '109876543210', 'With High Honors', 'Commonwealth High School', 2017, 'Fairview, Quezon City', 'Conduct Award', 'Diliman Elementary School', 2011, 'Java, HTML, CSS, MySQL', 'Volleyball, Badminton', 'Ramon Santos', 'Accountant', 'ABC Accounting Firm', 'Luzviminda Santos', 'Nurse', 'Philippine General Hospital', 'Pedro Santos', 'Mechanic', 'Auto Repair Shop', 'Caloocan City', '09153456789', '10k 20k', 'social media', '[\"Ana Santos\",\"Ben Santos\"]', '[\"College\",\"High School\"]', '[\"Nurse\",\"Student\"]', '2026-02-10 03:45:33', '2026-02-17 15:40:18'),
(3, '106789012345678901234', 'BSENTREP', 'BECTE', '1771342668_download (9).jpg', 'Reyes', 'John', 'Dela Cruz', 22, 'Male', '2001-11-12', 'Manila', 'Single', '09331234567', 'Iglesia ni Cristo', 'john.reyes@email.com', '88 España Blvd, Manila', 'Elena Reyes', '22 Taft Ave, Manila', 'University of Santo Tomas', 'BS Computer Science', 'España, Manila', '4th Year', 'UST Senior High School', 2019, 'España, Manila', '123450987654', 'With Honors', 'Manila High School', 2016, 'Intramuros, Manila', 'Leadership Award', 'Manila Elementary School', 2010, 'Python, Django, PostgreSQL, Git', 'Basketball, Swimming', 'Ricardo Reyes', 'Police Officer', 'Manila Police District', 'Elena Reyes', 'Teacher', 'Manila High School', 'Ramon Reyes', 'Retired', 'N/A', 'Bulacan', '09456789123', 'below 10k', 'social media', '[\"Maricar Reyes\",\"Paolo Reyes\"]', '[\"College\",\"College\"]', '[\"Accountant\",\"Civil Engineer\"]', '2026-02-03 02:15:22', '2026-02-17 15:40:25'),
(4, '107654321098765432109', 'BSBA-OM', 'BSENTREP', '1771342625_download (8).jpg', 'Garcia', 'Anna', 'M.', 19, 'Female', '2004-03-04', 'Makati', 'Single', '09179876543', 'Roman Catholic', 'anna.garcia@email.com', '12 Poblacion, Makati', 'Antonio Garcia', '34 Buendia, Makati', 'De La Salle University', 'BS Information Systems', 'Taft Ave, Manila', '1st Year', 'Makati Science High School', 2021, 'Makati', '102938475601', 'With High Honors', 'Makati High School', 2018, 'Makati', 'Best in Math', 'Makati Elementary School', 2012, 'C++, JavaScript, PHP, Laravel', 'Chess, Table Tennis', 'Antonio Garcia', 'Businessman', 'Garcia Trading', 'Luz Garcia', 'Housewife', 'N/A', 'Luz Garcia', 'Housewife', 'N/A', 'Makati', '09287654321', '20k 30k', 'school visit', '[\"Jose Garcia\",\"Clara Garcia\"]', '[\"College\",\"High School\"]', '[\"Engineer\",\"Student\"]', '2026-01-27 06:30:00', '2026-02-17 15:40:31'),
(5, '109876543210987654321', 'BSBA-OM', 'BSENTREP', '1771342599_download (7).jpg', 'Fernandez', 'Carlos', 'R.', 21, 'Male', '2002-07-19', 'Pasig', 'Single', '09051234567', 'Roman Catholic', 'carlos.fernandez@email.com', '56 Ortigas Ave, Pasig', 'Maria Fernandez', '78 Pasig Blvd, Pasig', 'Polytechnic University of the Philippines', 'BS Computer Science', 'Sta. Mesa, Manila', '3rd Year', 'Pasig City Science High School', 2020, 'Pasig', '112233445566', 'With Honors', 'Pasig National High School', 2017, 'Pasig', 'Science Award', 'Pasig Elementary School', 2011, 'Java, Android, Firebase, XML', 'Soccer, Running', 'Rogelio Fernandez', 'Driver', 'Jeepney Operator', 'Teresita Fernandez', 'Vendor', 'Public Market', 'Rogelio Fernandez', 'Driver', 'Jeepney Operator', 'Pasig', '09123456789', 'below 10k', 'social media', '[\"Kristine Fernandez\"]', '[\"College\"]', '[\"Nursing Student\"]', '2026-01-20 00:45:12', '2026-02-17 15:40:36'),
(6, '101112131415161718192', 'BECTE', 'BSED-MATH', '1771342565_download (6).jpg', 'Mendoza', 'Paolo', 'G.', 20, 'Male', '2003-01-30', 'Taguig', 'Single', '09261234567', 'Roman Catholic', 'paolo.mendoza@email.com', '123 BGC, Taguig', 'Luzviminda Mendoza', '456 C-5, Taguig', 'University of the Philippines', 'BS Computer Science', 'Diliman, Quezon City', '2nd Year', 'Taguig Science High School', 2020, 'Taguig', '998877665544', 'With High Honors', 'Taguig High School', 2017, 'Taguig', 'Best in English', 'Taguig Elementary School', 2011, 'Python, R, Machine Learning, SQL', 'Basketball, E-sports', 'Ben Mendoza', 'Engineer', 'DPWH', 'Gloria Mendoza', 'Teacher', 'Taguig High School', 'Ricardo Mendoza', 'Security Guard', 'Private Company', 'Laguna', '09198765432', '10k 20k', 'friend', '[\"Angela Mendoza\",\"David Mendoza\"]', '[\"College\",\"Elementary\"]', '[\"Architecture Student\",\"Student\"]', '2026-01-13 08:20:45', '2026-02-17 15:40:42'),
(7, '10293847561029384756', 'BECTE', 'BSAIS', '1771342531_download (5).jpg', 'Villanueva', 'Sofia', 'D.', 18, 'Female', '2005-06-15', 'Mandaluyong', 'Single', '09151234567', 'Roman Catholic', 'sofia.villanueva@email.com', '77 Shaw Blvd, Mandaluyong', 'Ramon Villanueva', '88 EDSA, Mandaluyong', 'San Beda University', 'BS Information Technology', 'Mendiola, Manila', '1st Year', 'Mandaluyong Science High School', 2022, 'Mandaluyong', '887766554433', 'With Honors', 'Mandaluyong High School', 2019, 'Mandaluyong', 'Leadership Award', 'Mandaluyong Elementary School', 2013, 'HTML, CSS, JavaScript, Photoshop', 'Badminton, Dance', 'Ramon Villanueva', 'Architect', 'Villanueva Designs', 'Leticia Villanueva', 'Dentist', 'Villanueva Dental Clinic', 'Leticia Villanueva', 'Dentist', 'Villanueva Dental Clinic', 'Mandaluyong', '09177654321', '10k 20k', 'friend', '[\"Andres Villanueva\"]', '[\"High School\"]', '[\"Student\"]', '2026-01-06 04:10:33', '2026-02-17 15:40:49'),
(8, '10325476980123456789', 'BECTE', 'BSED-ENG', '1771342503_download (1).jpg', 'Aquino', 'Kristine', 'B.', 22, 'Female', '2001-09-08', 'Las Piñas', 'Single', '09451234567', 'Iglesia ni Cristo', 'kristine.aquino@email.com', '56 Alabang-Zapote Rd, Las Piñas', 'Eduardo Aquino', '78 Daang Hari, Las Piñas', 'Adamson University', 'BS Computer Science', 'San Marcelino, Manila', '4th Year', 'Las Piñas Science High School', 2019, 'Las Piñas', '556677889900', 'With High Honors', 'Las Piñas National High School', 2016, 'Las Piñas', 'Science Award', 'Las Piñas Elementary School', 2010, 'Java, Spring, Hibernate, MySQL', 'Volleyball, Swimming', 'Eduardo Aquino', 'Electrician', 'Meralco', 'Luz Aquino', 'Cashier', 'Supermarket', 'Luz Aquino', 'Cashier', 'Supermarket', 'Las Piñas', '09231234567', '10k 20k', 'friend', '[\"Mark Aquino\",\"Jenny Aquino\"]', '[\"College\",\"College\"]', '[\"IT Staff\",\"Teacher\"]', '2026-02-16 07:45:22', '2026-02-17 15:41:02'),
(9, '10445566778899001122', 'BSBA-HRM', 'BSENTREP', '1771342485_download (4).jpg', 'Torres', 'Miguel', 'C.', 21, 'Male', '2002-12-02', 'Parañaque', 'Single', '09091234567', 'Roman Catholic', 'miguel.torres@email.com', '34 Sucat Rd, Parañaque', 'Carmen Torres', '56 BF Homes, Parañaque', 'FEU Institute of Technology', 'BS Information Technology', 'Sampaloc, Manila', '3rd Year', 'Parañaque Science High School', 2020, 'Parañaque', '223344556677', 'With Honors', 'Parañaque National High School', 2017, 'Parañaque', 'Best in Math', 'Parañaque Elementary School', 2011, 'PHP, Laravel, Vue.js, MySQL', 'Basketball, E-sports', 'Antonio Torres', 'OFW', 'Construction Company (Saudi)', 'Carmen Torres', 'Housewife', 'N/A', 'Carmen Torres', 'Housewife', 'N/A', 'Parañaque', '09123456789', '10k 20k', 'friend', '[\"Isabel Torres\",\"Leo Torres\"]', '[\"College\",\"High School\"]', '[\"Nurse\",\"Student\"]', '2026-02-14 02:30:15', '2026-02-17 15:34:45'),
(10, '10556677889900112233', 'BSBA-FM', 'BSBA-OM', '1771342443_download (3).jpg', 'Hernandez', 'Camille', 'R.', 19, 'Female', '2004-04-21', 'Valenzuela', 'Single', '09161234567', 'Roman Catholic', 'camille.hernandez@email.com', '89 MacArthur Hwy, Valenzuela', 'Roberto Hernandez', '12 Polo, Valenzuela', 'National University', 'BS Computer Science', 'Sampaloc, Manila', '1st Year', 'Valenzuela Science High School', 2022, 'Valenzuela', '667788990011', 'With High Honors', 'Valenzuela High School', 2019, 'Valenzuela', 'Leadership Award', 'Valenzuela Elementary School', 2013, 'C, Python, Flask, SQLite', 'Badminton, Chess', 'Roberto Hernandez', 'Factory Worker', 'Manufacturing Corp', 'Fe Hernandez', 'Sales Clerk', 'Department Store', 'Fe Hernandez', 'Sales Clerk', 'Department Store', 'Valenzuela', '09171234567', '10k 20k', 'Tiktok', '[\"Joseph Hernandez\"]', '[\"High School\"]', '[\"Student\"]', '2026-01-08 01:20:45', '2026-02-17 15:34:03'),
(11, '10667788990011223344', 'BSBA-MM', 'BSBA-FM', '1771342410_download (2).jpg', 'Cruz', 'Angelo', 'D.', 20, 'Male', '2003-10-10', 'Marikina', 'Single', '09221234567', 'Roman Catholic', 'angelo.cruz@email.com', '45 J.P. Rizal St, Marikina', 'Luzviminda Cruz', '67 Lilac St, Marikina', 'University of the East', 'BS Information Technology', 'Manila', '2nd Year', 'Marikina Science High School', 2021, 'Marikina', '998877665544', 'With Honors', 'Marikina High School', 2018, 'Marikina', 'Best in Science', 'Marikina Elementary School', 2012, 'JavaScript, React, Node.js, MongoDB', 'Basketball, Running', 'Rogelio Cruz', 'Carpenter', 'Construction', 'Luzviminda Cruz', 'Teacher', 'Marikina High School', 'Rogelio Cruz', 'Carpenter', 'Construction', 'Marikina', '09181234567', 'below 10k', 'social media', '[\"Mica Cruz\",\"Paul Cruz\"]', '[\"College\",\"Elementary\"]', '[\"Accountancy Student\",\"Student\"]', '2026-02-17 00:15:30', '2026-02-17 15:33:30'),
(12, '104043157871790817151', 'BSIT', 'BSBA-FM', '1771461234_images (1).jpg', 'Dela Cruz', 'Juan', 'Santos', 21, 'Male', '2002-05-12', 'Manila', 'Single', '09171234567', 'Roman Catholic', 'juan.delacruz@email.com', '123 Rizal St, Manila', 'Maria Dela Cruz', '456 Mabini St, Manila', 'University of the Philippines', 'BS Computer Science', 'Diliman, Quezon City', '3rd Year', 'Manila Science High School', 2019, 'P. Faura St, Manila', '123456789012', 'With Honors', 'Rizal High School', 2016, 'Pasig City', 'Best in Math', 'Paco Elementary School', 2010, 'Python, Java, SQL', 'Basketball, Chess', 'Pedro Dela Cruz', 'Electrician', 'City Electric Coop', 'Maria Dela Cruz', 'Teacher', 'Paco Elementary School', 'Jose Dela Cruz', 'Driver', 'LRT Line 1', 'Pasay City', '9181234567', 'below 10k', 'social media', '[\"Andres Dela Cruz\",\"Isabella Dela Cruz\"]', '[\"College\",\"High School\"]', '[\"Civil Engineer\",\"Student\"]', '2026-01-15 01:23:45', '2026-02-19 00:33:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applicants`
--
ALTER TABLE `applicants`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applicants`
--
ALTER TABLE `applicants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
