-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 22, 2025 at 08:15 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hospital_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

DROP TABLE IF EXISTS `appointments`;
CREATE TABLE IF NOT EXISTS `appointments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `patient_id` int DEFAULT NULL,
  `doctor_id` int DEFAULT NULL,
  `specialty_id` int DEFAULT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled','rescheduled') DEFAULT 'pending',
  `notes` text,
  `rescheduled_date` date DEFAULT NULL,
  `rescheduled_time` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `specialty_id` (`specialty_id`),
  KEY `idx_appointments_status` (`status`),
  KEY `idx_appointments_date` (`appointment_date`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `doctor_id`, `specialty_id`, `appointment_date`, `appointment_time`, `status`, `notes`, `rescheduled_date`, `rescheduled_time`, `created_at`, `updated_at`) VALUES
(1, 44, 68, 9, '2025-10-30', '14:30:00', 'completed', 'Type: follow-up\nrrcxi7e7', NULL, NULL, '2025-10-10 06:11:17', '2025-10-10 16:59:19'),
(2, 44, 68, 9, '2025-10-15', '09:00:00', 'completed', 'Type: follow-up\netwruterwt', NULL, NULL, '2025-10-10 06:46:01', '2025-10-14 05:47:28'),
(3, 44, 68, 9, '2025-10-16', '12:30:00', 'completed', 'Type: check-up\nhello doctor this is not a emergency but stilll', NULL, NULL, '2025-10-10 16:57:35', '2025-10-14 05:39:51'),
(4, 44, 65, 11, '2025-10-15', '14:00:00', 'pending', 'Type: follow-up\nwetriv', NULL, NULL, '2025-10-14 10:43:21', '2025-10-14 10:43:21'),
(5, 44, 68, 9, '2025-10-16', '16:30:00', 'completed', 'Type: follow-up\nytcyt', NULL, NULL, '2025-10-15 04:27:35', '2025-10-15 04:32:38'),
(6, 44, 68, 9, '2025-10-16', '15:30:00', 'pending', 'Type: follow-up\n', NULL, NULL, '2025-10-15 06:12:23', '2025-10-15 06:12:23'),
(7, 44, 68, 9, '2025-10-17', '16:00:00', 'completed', 'Type: follow-up\nhea', NULL, NULL, '2025-10-16 05:04:25', '2025-10-17 11:54:28');

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

DROP TABLE IF EXISTS `blogs`;
CREATE TABLE IF NOT EXISTS `blogs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `author` varchar(100) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `views` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `blogs`
--

INSERT INTO `blogs` (`id`, `title`, `content`, `image`, `author`, `category`, `created_at`, `views`) VALUES
(4, '5 Ways to Boost Immunity', 'Learn natural ways to improve your immune system.', 'pics/blog1.jpg', NULL, NULL, '2025-10-02 07:13:33', 0),
(5, 'Why Regular Health Checkups Matter', 'Prevention is better than cure. Stay ahead with checkups.', 'pics/blog2.jpg', NULL, NULL, '2025-10-02 07:13:33', 0),
(6, 'Managing Diabetes Effectively', 'Tips to lead a healthy life with diabetes.', 'pics/blog3.jpg', NULL, NULL, '2025-10-02 07:13:33', 0);

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
CREATE TABLE IF NOT EXISTS `cart_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `item_id` int DEFAULT NULL,
  `quantity` int DEFAULT '1',
  `added_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `type` enum('package','test') NOT NULL DEFAULT 'package',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `package_id` (`item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `user_id`, `item_id`, `quantity`, `added_at`, `type`) VALUES
(1, 44, 6, 1, '2025-10-15 05:11:33', 'package'),
(2, 44, 5, 1, '2025-10-15 05:13:30', 'test');

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

DROP TABLE IF EXISTS `cities`;
CREATE TABLE IF NOT EXISTS `cities` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `name`) VALUES
(9, 'Margao'),
(10, 'Panjim'),
(11, 'Mapusa'),
(12, 'Curchorem'),
(13, 'Canacona'),
(14, 'Vasco da Gama'),
(15, 'Ponda'),
(21, 'Calangute'),
(32, 'Quepem');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

DROP TABLE IF EXISTS `doctors`;
CREATE TABLE IF NOT EXISTS `doctors` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `specialty_id` int DEFAULT NULL,
  `experience` varchar(50) DEFAULT NULL,
  `availability` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `rating` decimal(3,1) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `busi_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `idx_doctors_specialty` (`specialty_id`)
) ENGINE=MyISAM AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `user_id`, `specialty_id`, `experience`, `availability`, `city`, `rating`, `phone`, `busi_email`, `image`, `is_active`, `created_at`) VALUES
(66, 42, 12, '3 Years', 'Mon-Wed 9am-5pm', 'Margao', NULL, '98239 82310', 'drpres@ly.com', NULL, 0, '2025-10-06 11:57:40'),
(65, 41, 11, '3 Years', 'Mon-Fri, 9am-5pm', 'Calangute', NULL, '99777 67975', 'drpratham@gm.com', NULL, 1, '2025-10-06 11:44:19'),
(67, 43, 11, '3 Years', 'Tue 10am-4pm', 'nigga', NULL, '68768 71455', 'drkris@gmail.co', NULL, 0, '2025-10-07 05:11:40'),
(68, 45, 9, '5 Years', 'Mon-Tue 9am-2pm', 'Panjim', NULL, '28965 62677', '', NULL, 1, '2025-10-09 04:40:45'),
(69, 46, 10, '5', 'mon-tue, 9am-5pm', 'Mapusa', NULL, '9813981300', 'drri@gm.com', NULL, 1, '2025-10-15 09:26:40');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_services`
--

DROP TABLE IF EXISTS `doctor_services`;
CREATE TABLE IF NOT EXISTS `doctor_services` (
  `doctor_id` int NOT NULL,
  `service_id` int NOT NULL,
  PRIMARY KEY (`doctor_id`,`service_id`),
  KEY `service_id` (`service_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doctor_services`
--

INSERT INTO `doctor_services` (`doctor_id`, `service_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 4),
(2, 5),
(2, 6),
(3, 1),
(3, 2),
(3, 7),
(4, 3),
(4, 6),
(4, 8),
(5, 9),
(5, 10),
(5, 11),
(6, 12),
(6, 13),
(6, 14),
(7, 10),
(7, 15),
(7, 16),
(8, 9),
(8, 11),
(8, 17),
(9, 18),
(9, 19),
(9, 20),
(10, 21),
(10, 22),
(10, 23),
(11, 18),
(11, 24),
(11, 25),
(12, 19),
(12, 20),
(12, 22),
(13, 26),
(13, 27),
(13, 28),
(14, 29),
(14, 30),
(14, 31),
(15, 27),
(15, 28),
(15, 32),
(16, 26),
(16, 29),
(16, 33),
(17, 34),
(17, 35),
(17, 36),
(18, 37),
(18, 38),
(18, 39),
(19, 34),
(19, 35),
(19, 36),
(20, 37),
(20, 38),
(20, 40),
(21, 41),
(21, 42),
(21, 43),
(22, 44),
(22, 45),
(22, 46),
(23, 41),
(23, 42),
(23, 43),
(24, 44),
(24, 45),
(24, 46);

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
CREATE TABLE IF NOT EXISTS `feedback` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `comment` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `user_id`, `rating`, `comment`, `created_at`) VALUES
(2, 28, 4, 'wdqku fdyqtivqtveytivqyetbciwetrcwdqku fdyqtivqtveytivqyetbciwetrcwdqku fdyqtivqtveytivqyetbciwetrcwdqku fdyqtivqtveytivqyetbciwetrcwdqku fdyqtivqtveytivqyetbciwetrcwdqku fdyqtivqtveytivqyetbciwetrc', '2025-10-02 12:13:24');

-- --------------------------------------------------------

--
-- Table structure for table `labs`
--

DROP TABLE IF EXISTS `labs`;
CREATE TABLE IF NOT EXISTS `labs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `address` text,
  `phone` varchar(20) DEFAULT NULL,
  `services` json DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `labs`
--

INSERT INTO `labs` (`id`, `name`, `city`, `address`, `phone`, `services`, `image`, `created_at`) VALUES
(3, 'Lab in Margao', 'Margao', NULL, '+1 212 555 1234', '[\"advanced diagnostic and pathology services\"]', 'https://picsum.photos/id/1012/600/300', '2025-10-02 07:13:33'),
(4, 'Lab in Panjim', 'Panjim', NULL, '+1 310 555 5678', '[\"imaging, microbiology, and blood tests\"]', 'https://picsum.photos/id/1015/600/300', '2025-10-02 07:13:33'),
(5, 'Lab in Mapusa', 'Mapusa', NULL, '+1 713 555 9012', '[\"full diagnostic services and emergency testing\"]', 'https://picsum.photos/id/1016/600/300', '2025-10-02 07:13:33'),
(6, 'Lab in Curchorem', 'Curchorem', NULL, '+1 305 555 4321', '[\"tropical disease testing and pathology\"]', 'https://picsum.photos/id/1018/600/300', '2025-10-02 07:13:33'),
(7, 'Lab in Canacona', 'Canacona', NULL, '+1 312 555 8765', '[\"radiology, pathology, and blood analysis\"]', 'https://picsum.photos/id/1019/600/300', '2025-10-02 07:13:33'),
(8, 'Lab in Margao', 'Margao', NULL, '+1 206 555 3456', '[\"microbiology, COVID-19, and advanced imaging tests\"]', 'https://picsum.photos/id/1020/600/300', '2025-10-02 07:13:33'),
(9, 'Lab in Panjim', 'Panjim', NULL, '+1 614 555 6543', '[\"pathology and modern diagnostic services\"]', 'https://picsum.photos/id/1021/600/300', '2025-10-02 07:13:33'),
(10, 'Lab in Mapusa', 'Mapusa', NULL, '+1 215 555 3210', '[\"infectious disease testing\"]', 'https://picsum.photos/id/1022/600/300', '2025-10-02 07:13:33'),
(11, 'Lab in Curchorem', 'Curchorem', NULL, '+1 404 555 7890', '[\"preventive health diagnostics\"]', 'https://picsum.photos/id/1023/600/300', '2025-10-02 07:13:33'),
(12, 'Lab in Vasco da Gama', 'Vasco da Gama', NULL, '+1 407 555 1122', '[\"cardiology and blood analysis\"]', 'https://picsum.photos/id/1024/600/300', '2025-10-02 07:13:33'),
(13, 'Lab in Ponda', 'Ponda', NULL, '+1 305 555 3344', '[\"neurology and advanced diagnostics\"]', 'https://picsum.photos/id/1025/600/300', '2025-10-02 07:13:33'),
(14, 'Lab in Benaulim', 'Benaulim', NULL, '+1 212 555 7788', '[\"general pathology and lab tests\"]', 'https://picsum.photos/id/1026/600/300', '2025-10-02 07:13:33'),
(15, 'Lab in Margao', 'Margao', NULL, '+1 310 555 9988', '[\"dermatology and microbiology\"]', 'https://picsum.photos/id/1027/600/300', '2025-10-02 07:13:33'),
(16, 'Lab in Panjim', 'Panjim', NULL, '+1 714 555 2233', '[\"radiology and imaging tests\"]', 'https://picsum.photos/id/1028/600/300', '2025-10-02 07:13:33'),
(17, 'Lab in Mapusa', 'Mapusa', NULL, '+1 206 555 5566', '[\"blood tests and emergency diagnostics\"]', 'https://picsum.photos/id/1029/600/300', '2025-10-02 07:13:33'),
(18, 'Lab in Curchorem', 'Curchorem', NULL, '+1 305 555 6677', '[\"preventive healthcare and lab tests\"]', 'https://picsum.photos/id/1030/600/300', '2025-10-02 07:13:33'),
(19, 'Lab in Canacona', 'Canacona', NULL, '+1 312 555 8899', '[\"microbiology and infectious disease testing\"]', 'https://picsum.photos/id/1031/600/300', '2025-10-02 07:13:33'),
(20, 'Lab in Vasco da Gama', 'Vasco da Gama', NULL, '+1 407 555 9900', '[\"cardiology and preventive health\"]', 'https://picsum.photos/id/1032/600/300', '2025-10-02 07:13:33'),
(21, 'Lab in Ponda', 'Ponda', NULL, '+1 305 555 4455', '[\"full diagnostic services\"]', 'https://picsum.photos/id/1033/600/300', '2025-10-02 07:13:33'),
(22, 'Lab in Benaulim', 'Benaulim', NULL, '+1 212 555 6677', '[\"advanced imaging and lab services\"]', 'https://picsum.photos/id/1034/600/300', '2025-10-02 07:13:33');

-- --------------------------------------------------------

--
-- Table structure for table `medical_history`
--

DROP TABLE IF EXISTS `medical_history`;
CREATE TABLE IF NOT EXISTS `medical_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `patient_id` int DEFAULT NULL,
  `doctor_id` int DEFAULT NULL,
  `visit_date` date NOT NULL,
  `conditions` json DEFAULT NULL,
  `allergies` json DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `idx_medical_history_patient` (`patient_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `medical_history`
--

INSERT INTO `medical_history` (`id`, `patient_id`, `doctor_id`, `visit_date`, `conditions`, `allergies`, `notes`, `created_at`) VALUES
(1, 44, 68, '2025-10-14', '[\"ggvrucuw\", \"fwvcuye\", \"dwei\"]', '[\"augdq\"]', 'ewtr2uetrt2ourtctwerqoerbetr', '2025-10-14 05:39:51'),
(2, 44, 68, '2025-10-14', '[\"ggvrucuw\", \"fwvcuye\", \"dwei\"]', '[\"augdq\"]', 'uwietrbiviwue', '2025-10-14 05:47:28'),
(3, 44, 68, '2025-10-15', '[\"ggvrucuw\", \"fwvcuye\", \"dwei\"]', '[\"augdq\"]', 'hello ji', '2025-10-15 04:32:38'),
(4, 44, 68, '2025-10-17', '[\"ggvrucuw\", \"fwvcuye\", \"dwei\"]', '[\"augdq\"]', 'rc6iqf', '2025-10-17 11:54:28');

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

DROP TABLE IF EXISTS `packages`;
CREATE TABLE IF NOT EXISTS `packages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `discounted_price` decimal(10,2) DEFAULT NULL,
  `description` text,
  `duration` varchar(50) DEFAULT NULL,
  `features` json DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`id`, `name`, `price`, `discounted_price`, `description`, `duration`, `features`, `image`, `is_active`, `created_at`) VALUES
(4, 'Basic Screening', 99.00, 79.00, 'Routine checkup with essential tests.', '1 Year', '[\"Blood & Urine Tests\", \"Doctor Consultation\", \"Digital Report\"]', NULL, 1, '2025-10-02 07:13:33'),
(5, 'Full Body Checkup', 199.00, 169.00, 'Comprehensive tests with specialist guidance.', '1 Year', '[\"Comprehensive Lab Tests\", \"2 Specialist Consultations\", \"Priority Support\"]', NULL, 1, '2025-10-02 07:13:33'),
(6, 'Advanced Diagnostic', 299.00, 239.00, 'In-depth tests with imaging and unlimited consultations.', '1 Year', '[\"Advanced Imaging (X-ray, Ultrasound)\", \"Unlimited Consultations\", \"24/7 Support\"]', NULL, 1, '2025-10-02 07:13:33'),
(7, 'Diabetes & Heart Care', 199.00, 149.00, 'Focused package for lifestyle-related conditions.', '1 Year', '[\"Blood Sugar & HbA1c\", \"ECG & Cholesterol Profile\", \"Cardiologist Consultation\"]', NULL, 1, '2025-10-02 07:13:33'),
(8, 'Senior Citizen Checkup', 179.00, 152.00, 'Specially designed for 60+ age group.', '1 Year', '[\"Vision & Hearing Tests\", \"Respiratory & Cardiac Screening\", \"Geriatric Specialist Guidance\"]', NULL, 1, '2025-10-02 07:13:33'),
(9, 'Women Wellness', 189.00, 151.00, 'Complete care designed for women?s health.', '1 Year', '[\"Thyroid & Hormone Profile\", \"Breast & Cervical Screening\", \"Gynecologist Consultation\"]', NULL, 1, '2025-10-02 07:13:33'),
(10, 'Executive Health Checkup', 249.00, 224.00, 'Tailored for professionals with busy schedules.', '1 Year', '[\"Stress & Lifestyle Screening\", \"Energy & Fitness Assessment\", \"Workplace Health Report\"]', NULL, 1, '2025-10-02 07:13:33'),
(11, 'Executive Health Checkup', 449.00, 424.00, 'Tailored for professionals with busy schedules.', '2 Year', '[\"Stress & Lifestyle Screening\", \"Energy & Fitness Assessment\", \"Workplace Health Report\"]', NULL, 1, '2025-10-02 07:13:33'),
(12, 'Executive Health Checkup', 649.00, 624.00, 'Tailored for professionals with busy schedules.', '3 Year', '[\"Stress & Lifestyle Screening\", \"Energy & Fitness Assessment\", \"Workplace Health Report\"]', NULL, 1, '2025-10-02 07:13:33'),
(13, 'Child Health Package', 99.00, 89.00, 'Essential health checks for kids under 12.', '1 Year', '[\"Vaccination Review\", \"Growth & Nutrition Screening\", \"Pediatric Specialist\"]', NULL, 1, '2025-10-02 07:13:33'),
(14, 'Kidney & Liver Health', 219.00, 175.00, 'Specialized tests for kidney and liver functions.', '1 Year', '[\"Kidney Function Test\", \"Liver Enzyme Profile\", \"Consultation with Specialist\"]', NULL, 1, '2025-10-02 07:13:33'),
(15, 'Thyroid & Hormonal Check', 159.00, 135.00, 'Focus on thyroid and endocrine health.', '1 Year', '[\"Thyroid Panel\", \"Hormone Level Analysis\", \"Endocrinologist Consultation\"]', NULL, 1, '2025-10-02 07:13:33'),
(16, 'Immunity Booster Package', 129.00, 97.00, 'Tests to enhance immunity and detect deficiencies.', '1 Year', '[\"Vitamin & Mineral Tests\", \"Immunity Profile\", \"Doctor Consultation\"]', NULL, 1, '2025-10-02 07:13:33'),
(17, 'Cardio Plus', 249.00, 199.00, 'Advanced heart-focused package for risk assessment.', '1 Year', '[\"ECG\", \"Echocardiogram\", \"Cardiologist Consultation\"]', NULL, 1, '2025-10-02 07:13:33'),
(18, 'Women Advanced Wellness', 229.00, 195.00, 'Complete package for women?s advanced health screening.', '1 Year', '[\"Hormone Profile\", \"Bone Density Scan\", \"Gynecologist Consultation\"]', NULL, 1, '2025-10-02 07:13:33'),
(19, 'Men Wellness Package', 219.00, 197.00, 'Comprehensive male health checkup.', '1 Year', '[\"Prostate & Hormone Tests\", \"Cardiac Screening\", \"Urologist Consultation\"]', NULL, 1, '2025-10-02 07:13:33'),
(20, 'Cancer Screening', 399.00, 279.00, 'Preventive screening for early detection of common cancers.', '1 Year', '[\"Blood Markers\", \"Imaging Tests\", \"Oncologist Consultation\"]', NULL, 1, '2025-10-02 07:13:33'),
(26, 'test package', 999.00, 799.00, 'this is a test package ', '3 years', '[\"testing\\r\", \"the \\r\", \"website\"]', '', 1, '2025-10-04 06:43:49'),
(22, 'Heart Health Check', 199.00, 169.00, 'Special package for cardiac health monitoring.', '1 Year', '[\"ECG\", \"Cholesterol & Lipid Profile\", \"Cardiologist Consultation\"]', NULL, 1, '2025-10-02 07:13:33'),
(23, 'Comprehensive Senior Checkup', 299.00, 224.00, 'Extensive health check for senior citizens.', '1 Year', '[\"Complete Blood Work\", \"Cardiac & Respiratory Screening\", \"Geriatric Specialist Review\"]', NULL, 1, '2025-10-02 07:13:33');

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

DROP TABLE IF EXISTS `prescriptions`;
CREATE TABLE IF NOT EXISTS `prescriptions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `doctor_id` int DEFAULT NULL,
  `patient_id` int DEFAULT NULL,
  `appointment_id` int DEFAULT NULL,
  `medication` json DEFAULT NULL,
  `notes` text,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `patient_id` (`patient_id`),
  KEY `appointment_id` (`appointment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `prescriptions`
--

INSERT INTO `prescriptions` (`id`, `doctor_id`, `patient_id`, `appointment_id`, `medication`, `notes`, `file_path`, `created_at`) VALUES
(1, 68, 44, NULL, '\"paracitamol, dolo\"', 'take 2\r\n', NULL, '2025-10-10 16:59:49'),
(2, 68, 44, NULL, '\"dolo paracitamol\"', 'fgficerbcq', NULL, '2025-10-13 14:55:19'),
(3, 68, 44, NULL, '\"dolo paracitamol\"', 'fgficerbcq', NULL, '2025-10-13 15:01:56'),
(4, 68, 44, NULL, '\"riyan, lobo\"', 'ewqtgwe', NULL, '2025-10-14 05:38:44'),
(5, 68, 44, 3, '[{\"dose\": \"rewrvw\", \"name\": \"qwiutvri\"}, {\"dose\": \"tert\", \"name\": \"erwet\"}]', 'ewtr2uetrt2ourtctwerqoerbetr', 'uploads/1760420391_Screenshot 2025-09-16 124822.png', '2025-10-14 05:39:51'),
(6, 68, 44, 2, '[{\"dose\": \"rewrvw\", \"name\": \"qwiutvri\"}]', 'uwietrbiviwue', '../uploads/1760420848_Screenshot 2025-09-16 124822.png', '2025-10-14 05:47:28'),
(7, 68, 44, 5, '[{\"dose\": \"rewrvw\", \"name\": \"qwiutvri\"}]', 'hello ji', '../uploads/1760502758_Screenshot2025-09-28002539.png', '2025-10-15 04:32:38'),
(8, 68, 44, 7, '[{\"dose\": \"rewrvw\", \"name\": \"qwiutvri\"}]', 'rc6iqf', '../uploads/1760702068_Screenshot2025-10-17163533.png', '2025-10-17 11:54:28');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
CREATE TABLE IF NOT EXISTS `services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=97 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `description`, `created_at`) VALUES
(52, 'Heart Checkup', NULL, '2025-10-02 07:13:33'),
(53, 'ECG', NULL, '2025-10-02 07:13:33'),
(54, 'Cardiac Surgery', NULL, '2025-10-02 07:13:33'),
(55, 'Angioplasty', NULL, '2025-10-02 07:13:33'),
(56, 'Heart Transplant', NULL, '2025-10-02 07:13:33'),
(57, 'Cardiac Rehab', NULL, '2025-10-02 07:13:33'),
(58, 'Stress Test', NULL, '2025-10-02 07:13:33'),
(59, 'Acne Treatment', NULL, '2025-10-02 07:13:33'),
(60, 'Skin Allergy', NULL, '2025-10-02 07:13:33'),
(61, 'Laser Therapy', NULL, '2025-10-02 07:13:33'),
(62, 'Psoriasis Care', NULL, '2025-10-02 07:13:33'),
(63, 'Cosmetic Dermatology', NULL, '2025-10-02 07:13:33'),
(64, 'Skin Cancer Screening', NULL, '2025-10-02 07:13:33'),
(65, 'Mole Removal', NULL, '2025-10-02 07:13:33'),
(66, 'Dermatologic Surgery', NULL, '2025-10-02 07:13:33'),
(67, 'Root Canal', NULL, '2025-10-02 07:13:33'),
(68, 'Teeth Whitening', NULL, '2025-10-02 07:13:33'),
(69, 'Dental Implants', NULL, '2025-10-02 07:13:33'),
(70, 'Orthodontics', NULL, '2025-10-02 07:13:33'),
(71, 'Oral Surgery', NULL, '2025-10-02 07:13:33'),
(72, 'Pediatric Dentistry', NULL, '2025-10-02 07:13:33'),
(73, 'Braces', NULL, '2025-10-02 07:13:33'),
(74, 'Dental Cleaning', NULL, '2025-10-02 07:13:33'),
(75, 'Epilepsy Treatment', NULL, '2025-10-02 07:13:33'),
(76, 'Stroke Care', NULL, '2025-10-02 07:13:33'),
(77, 'Neurological Exams', NULL, '2025-10-02 07:13:33'),
(78, 'Parkinson\'s Disease', NULL, '2025-10-02 07:13:33'),
(79, 'Headache Management', NULL, '2025-10-02 07:13:33'),
(80, 'Neuroimaging', NULL, '2025-10-02 07:13:33'),
(81, 'Migraine Treatment', NULL, '2025-10-02 07:13:33'),
(82, 'Cognitive Therapy', NULL, '2025-10-02 07:13:33'),
(83, 'Joint Replacement', NULL, '2025-10-02 07:13:33'),
(84, 'Fracture Care', NULL, '2025-10-02 07:13:33'),
(85, 'Sports Injuries', NULL, '2025-10-02 07:13:33'),
(86, 'Arthroscopy', NULL, '2025-10-02 07:13:33'),
(87, 'Spine Surgery', NULL, '2025-10-02 07:13:33'),
(88, 'Orthopedic Rehab', NULL, '2025-10-02 07:13:33'),
(89, 'Rehabilitation', NULL, '2025-10-02 07:13:33'),
(90, 'Vaccinations', NULL, '2025-10-02 07:13:33'),
(91, 'Growth Monitoring', NULL, '2025-10-02 07:13:33'),
(92, 'Child Nutrition', NULL, '2025-10-02 07:13:33'),
(93, 'Newborn Care', NULL, '2025-10-02 07:13:33'),
(94, 'Developmental Checkups', NULL, '2025-10-02 07:13:33'),
(95, 'Pediatric Emergencies', NULL, '2025-10-02 07:13:33'),
(96, 'Nutritional Guidance', NULL, '2025-10-02 07:13:33');

-- --------------------------------------------------------

--
-- Table structure for table `specialties`
--

DROP TABLE IF EXISTS `specialties`;
CREATE TABLE IF NOT EXISTS `specialties` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `specialties`
--

INSERT INTO `specialties` (`id`, `name`, `image`, `description`, `created_at`) VALUES
(7, 'Cardiologist', 'pics/cardio.png', 'Cardiologists specialize in the heart and blood vessels. They diagnose and treat heart disease, congenital heart defects, heart attacks, hypertension, and arrhythmias.', '2025-10-02 07:13:33'),
(8, 'Dermatologist', 'pics/dermat.png', 'Dermatologists are experts in skin, hair, and nails. They treat everything from acne to serious conditions like melanoma.', '2025-10-02 07:13:33'),
(9, 'Dentist', 'pics/dental.png', 'Dentists focus on oral health ? teeth, gums, and overall mouth care. They help with preventive care and dental treatments.', '2025-10-02 07:13:33'),
(10, 'Neurologist', 'pics/neuro.png', 'Neurologists diagnose and treat diseases of the brain, spinal cord, and nerves.', '2025-10-02 07:13:33'),
(11, 'Orthopedic', 'pics/ortho.png', 'Orthopedic doctors treat the musculoskeletal system ? bones, joints, muscles, ligaments, and spine.', '2025-10-02 07:13:33'),
(12, 'Pediatrician', 'pics/pedia.png', 'Pediatricians provide medical care for infants, children, and teenagers ? ensuring healthy growth and development.', '2025-10-02 07:13:33');

-- --------------------------------------------------------

--
-- Table structure for table `tests`
--

DROP TABLE IF EXISTS `tests`;
CREATE TABLE IF NOT EXISTS `tests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lab_id` int DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `discounted_price` decimal(10,2) DEFAULT '0.00',
  `description` text,
  `duration` varchar(50) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT '1',
  `icon` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lab_id` (`lab_id`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tests`
--

INSERT INTO `tests` (`id`, `lab_id`, `name`, `price`, `discounted_price`, `description`, `duration`, `is_available`, `icon`) VALUES
(4, 1, 'Fever Panel - Advanced', 800.00, 28.00, 'Comprehensive fever analysis for accurate diagnosis.', NULL, 1, '&#129298'),
(5, 1, 'Basic Diabetes Package', 450.00, 62.00, 'Check your blood sugar and monitor diabetes risk.', NULL, 1, '&#129298'),
(6, 1, 'Thyroid Profile', 250.00, 30.00, 'Evaluate thyroid hormones for overall metabolic health.', NULL, 1, '&#129298'),
(7, 1, 'Lipid Profile', 300.00, 165.00, 'Assess cholesterol and triglyceride levels.', NULL, 1, '&#129298'),
(8, 1, 'Kidney Profile / KFT', 300.00, 134.00, 'Check kidney function and detect potential issues.', NULL, 1, '&#129298'),
(9, 1, 'Liver Function Test (LFT)', 350.00, 176.00, 'Monitor liver health and detect liver disorders.', NULL, 1, '&#129298'),
(10, 1, 'Complete Blood Count (CBC)', 200.00, 78.00, 'Comprehensive blood analysis for overall health.', NULL, 1, '&#129298'),
(11, 1, 'COVID-19 RTPCR', 600.00, 64.00, 'Accurate COVID-19 testing with rapid results.', NULL, 1, '&#129298'),
(12, 1, 'Vitamin D Test', 400.00, 84.00, 'Check vitamin D levels for bone and immune health.', NULL, 1, '&#129298'),
(13, 1, 'H1N1 (Swine Flu) Test', 700.00, 30.00, 'Detect H1N1 infection quickly and reliably.', NULL, 1, '&#129298'),
(14, 1, 'Iron & Ferritin Test', 350.00, 96.00, 'Evaluate iron levels and detect anemia or deficiency.', NULL, 1, '&#129298'),
(15, 1, 'HbA1c Test', 500.00, 194.00, 'Monitor long-term blood sugar control for diabetics.', NULL, 1, '&#129298'),
(16, 1, 'Vitamin B12 Test', 400.00, 80.00, 'Check Vitamin B12 for energy levels and nerve health.', NULL, 1, '&#129298'),
(17, 1, 'CRP (C-Reactive Protein) Test', 350.00, 17.00, 'Detect inflammation and infection markers in the body.', NULL, 1, '&#129298'),
(18, 1, 'Complete Urine Analysis', 200.00, 48.00, 'Check kidney and urinary tract health comprehensively.', NULL, 1, '&#129298'),
(19, 1, 'HIV 1 & 2 Test', 600.00, 191.00, 'Screen for HIV infection accurately and confidentially.', NULL, 1, '&#129298'),
(20, 1, 'Hepatitis B & C Panel', 700.00, 10.00, 'Check liver infection status with Hepatitis B & C tests.', NULL, 1, '&#129298'),
(21, 1, 'Vitamin A Test', 300.00, 78.00, 'Assess Vitamin A levels for vision and immunity.', NULL, 1, '&#129298'),
(22, 1, 'Electrolyte Panel', 350.00, 160.00, 'Check sodium, potassium, and other minerals balance.', NULL, 1, '&#129298'),
(23, 1, 'Hormone Profile Test', 500.00, 167.00, 'Evaluate key hormones for metabolic and reproductive health.', NULL, 1, '&#129298'),
(25, 14, 'testing', 666.00, 333.00, 'wuetfivWTEVTWTEBVWTVWETYVtwteyuturtvw', NULL, 1, ''),
(32, 8, 'eren', 500.00, 400.00, 'this is a eren test', '1 year', 1, ''),
(31, 12, 'shishant', 2.00, 1.00, 'this is my friend', '3 minutes', 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT 'other',
  `address` text,
  `role` enum('patient','doctor','admin') DEFAULT 'patient',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_users_role` (`role`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `phone`, `city`, `date_of_birth`, `gender`, `address`, `role`, `is_active`, `created_at`) VALUES
(46, 'riyannn', 'ri@gm.co', '$2y$10$CUdXWvd8Fa6P6Z8t8pNdceLMTmtE7tHy/.MvABWJb8266PpIh1oZW', 'riyan l', '9813981300', 'Mapusa', NULL, 'other', NULL, 'doctor', 1, '2025-10-15 09:26:40'),
(45, 'doccc', 'doc@ex.com', 'f9f16d97c90d8c6f2cab37bb6d1f1992', 'Doctor', '28965 62677', 'Panjim', '1980-05-05', 'male', 'I m a doctor ', 'doctor', 1, '2025-10-09 04:40:45'),
(44, 'patient', 'pat@gm.com', 'b39024efbc6de61976f585c8421c6bba', 'Patient', '5566556655', 'Margao', '2005-06-06', 'male', 'madgaon', 'patient', 1, '2025-10-07 05:23:24'),
(43, 'kriskross', 'kris@gmail.co', '2d9b03cc4a24d788b585766d33952c32', 'Kris Andrade', '68768 71455', 'nigga', '2002-09-18', 'male', 'what will you do with my address broh', 'doctor', 1, '2025-10-07 05:11:40'),
(42, 'annnu', 'pres@ly.com', 'e0b5219e70c2dba78e8a97cf9e1f077f', 'Presley Ferns', '98239 82310', 'Margao', '1970-10-05', 'male', 'tereko kya ', 'doctor', 0, '2025-10-06 11:57:40'),
(41, 'prathammm', 'pratham@yh.com', '2d9b03cc4a24d788b585766d33952c32', 'Pratham Naik', '99777 67975', 'Calangute', '1980-10-08', 'male', 'garakaden asa', 'doctor', 1, '2025-10-06 11:44:19'),
(40, 'riyan7', 'riyan7@example.com', '2d9b03cc4a24d788b585766d33952c32', 'Riyan', '80007 00006', 'Margao', '1980-10-24', 'male', 'garakaden', 'doctor', 1, '2025-10-06 11:35:23'),
(39, 'suiii', 'cr7@ex.com', '2d9b03cc4a24d788b585766d33952c32', 'Ronaldo suiii', '77777 77777', 'Margao', '1985-02-05', 'male', 'lisbon portugal\r\n\r\n', 'patient', 1, '2025-10-06 11:33:35'),
(38, 'ri', 'r7@ex.com', '21232f297a57a5a743894a0e4a801fc3', 'Admin User', '+91 9876543210', 'Margao', '1990-01-01', 'other', 'Admin Address', 'admin', 1, '2025-10-06 11:29:45'),
(37, 'admin_user', 'admin@example.com', 'dc3565645d8002becb5fd7977aeef3e1', 'Admin User', '+91 9876543210', 'Margao', '1990-01-01', 'other', 'Admin Address', 'admin', 1, '2025-10-06 11:27:46'),
(36, 'riyan', 'riyan@gm.com', 'afb96f064402f4cdce69034cd48ff5c2', 'Riyan Lobo', '80071 94157', 'Margao', '2006-10-24', 'male', '309 bazarwada collem goa ', 'patient', 1, '2025-10-06 11:18:48');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
