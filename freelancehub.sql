-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Apr 02, 2026 at 04:58 PM
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
-- Database: `freelancehub`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `full_name`, `email`, `password`) VALUES
(1, 'admin', 'a@gmail.com', '1234'),
(2, 'admin2', 'a2@gmail.com', '1234');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `client_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_suspended` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`client_id`, `full_name`, `email`, `password`, `created_at`, `is_suspended`) VALUES
(1, 'C1', 'c1@gmail.com', '222222', '2026-01-15 20:25:11', 1),
(2, 'Client2', 'c2@gmail.com', '222222', '2026-01-19 09:48:12', 0),
(3, 'Client3', 'c3@gmail.com', '222222', '2026-01-20 07:01:49', 0);

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `course_title` varchar(200) DEFAULT NULL,
  `course_url` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `course_title`, `course_url`, `created_at`) VALUES
(1, 'Learn HTML: Fundamentals', 'https://www.codecademy.com/learn/learn-html-fundamentals', '2026-01-15 07:53:48'),
(2, 'IBM Introduction to Mobile App Development', 'https://www.coursera.org/learn/introduction-to-mobile-app-development', '2026-01-15 07:55:13');

-- --------------------------------------------------------

--
-- Table structure for table `freelancers`
--

CREATE TABLE `freelancers` (
  `freelancer_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `skill_id` int(11) DEFAULT NULL,
  `test_score` decimal(5,2) DEFAULT NULL,
  `status` enum('active','suspended') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_verified` tinyint(1) DEFAULT 0,
  `bio` text DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `is_suspended` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `freelancers`
--

INSERT INTO `freelancers` (`freelancer_id`, `full_name`, `email`, `password`, `skill_id`, `test_score`, `status`, `created_at`, `is_verified`, `bio`, `profile_picture`, `is_suspended`) VALUES
(4, 'Freelancer1', 'f1@gmail.com', '111111', 1, NULL, 'active', '2026-01-16 14:03:42', 1, 'Hi! I am Freelancer 1.', NULL, 0),
(5, 'naimur rahman', 'darthvader007@gmail.com', '111111', 1, NULL, 'active', '2026-01-17 08:19:54', 1, 'I am a sick man. I am aurthur morgan', NULL, 0),
(6, 'sadat', 'sadat@gmail.com', '111111', 2, NULL, 'active', '2026-01-20 19:22:24', 1, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `freelancer_test_attempts`
--

CREATE TABLE `freelancer_test_attempts` (
  `attempt_id` int(11) NOT NULL,
  `freelancer_email` varchar(150) NOT NULL,
  `skill_id` int(11) NOT NULL,
  `score` decimal(5,2) NOT NULL,
  `passed` tinyint(1) NOT NULL,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `freelancer_test_attempts`
--

INSERT INTO `freelancer_test_attempts` (`attempt_id`, `freelancer_email`, `skill_id`, `score`, `passed`, `attempted_at`) VALUES
(1, 'f1@gmail.com', 1, 80.00, 1, '2026-01-15 12:36:44'),
(2, 'f2@gmail.com', 1, 100.00, 1, '2026-01-15 12:49:46'),
(3, 'f3@gmail.com', 1, 90.00, 1, '2026-01-15 12:57:53'),
(4, 'f3@gmail.com', 1, 20.00, 0, '2026-01-15 13:01:26'),
(5, 'f1@gmail.com', 1, 80.00, 1, '2026-01-16 14:03:42'),
(6, 'f2@gmail.com', 1, 30.00, 0, '2026-01-16 14:09:44'),
(7, 'darthvader007@gmail.com', 1, 80.00, 1, '2026-01-17 08:19:54'),
(8, 'g@gmail.com', 2, 10.00, 0, '2026-01-17 08:23:25'),
(9, 'd@gmail.com', 1, 20.00, 0, '2026-01-17 09:31:09'),
(10, 'sadat@gmail.com', 2, 100.00, 1, '2026-01-20 19:22:24');

-- --------------------------------------------------------

--
-- Table structure for table `hired_freelancers`
--

CREATE TABLE `hired_freelancers` (
  `hire_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `freelancer_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `hired_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','completed','cancelled') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hired_freelancers`
--

INSERT INTO `hired_freelancers` (`hire_id`, `job_id`, `freelancer_id`, `client_id`, `hired_at`, `status`) VALUES
(1, 1, 4, 1, '2026-01-16 15:52:42', 'active'),
(3, 3, 5, 2, '2026-01-19 10:07:50', 'completed'),
(5, 4, 5, 3, '2026-01-20 07:10:39', 'active'),
(6, 5, 6, 2, '2026-01-20 20:38:13', 'completed'),
(7, 6, 6, 2, '2026-01-24 08:51:21', 'completed');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `job_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `skill_id` int(11) NOT NULL,
  `job_title` varchar(150) DEFAULT NULL,
  `job_description` text DEFAULT NULL,
  `status` enum('open','closed') DEFAULT 'open',
  `is_hired` tinyint(1) DEFAULT 0,
  `hired_freelancer_id` int(11) DEFAULT NULL,
  `hired_at` timestamp NULL DEFAULT NULL,
  `budget` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`job_id`, `client_id`, `skill_id`, `job_title`, `job_description`, `status`, `is_hired`, `hired_freelancer_id`, `hired_at`, `budget`, `created_at`) VALUES
(1, 1, 1, 'Full Stack Web Developer', 'We are looking for a proactive, skilled, and creative Full Stack Web Developer to join our growing technology & marketing-driven team.\r\n\r\nThe ideal candidate must be passionate about clean coding practices, problem–solving, and building scalable web solutions.\r\n\r\nYou will work closely with product, design, and marketing teams to develop, maintain, and optimize our digital platforms, e-commerce features, and internal tools.', 'closed', 0, NULL, NULL, 40000.00, '2026-01-16 15:18:43'),
(2, 1, 2, 'Assistant Manager - Mobile Application Developer (Android & iOS)', 'The Mobile Application Developer is responsible for designing, developing, and maintaining high-quality mobile applications for Android and iOS. This role focuses on building secure, scalable, and high-performance mobile solutions that align with Tekvision’s enterprise systems and digital transformation initiatives. The developer will work closely with backend engineers, UI/UX designers, product owners, and QA teams to translate business and technical requirements into reliable mobile applications. This role also requires a strong understanding of mobile platforms, application lifecycles, performance optimization, and the complete setup of modern mobile development environments.\r\n\r\nAt Tekvision, we value professionalism, collaboration, continuous learning, and ownership. The Mobile Application Developer is expected to demonstrate strong communication skills, a proactive mindset, and a commitment to building high-quality, secure, and sustainable software solutions that support long-term business goals.', 'closed', 0, NULL, NULL, 60000.00, '2026-01-16 15:36:41'),
(3, 2, 1, 'Executive / Senior Executive - Full Stack Developer', 'Candidates at Chottagram get priority for this position.\r\n\r\n    Develop and maintain web applications using .NET (C#) and Angular.\r\n\r\n    Design and implement RESTful APIs for seamless integration.\r\n\r\n    Work with MSSQL databases to manage and optimize data storage.\r\n\r\n    Collaborate with cross-functional teams to deliver scalable solutions.\r\n\r\n    Participate in code reviews, testing, and debugging to ensure high-quality output.', 'closed', 1, 5, '2026-01-19 10:07:50', 50000.00, '2026-01-19 09:51:41'),
(4, 3, 1, 'Web Developer IX (Remote)', 'We are seeking a detail-oriented and creative Web Developer to design, build, and maintain our web applications. The ideal candidate has strong coding skills, an eye for design, and a passion for creating seamless digital experiences.\r\n\r\nRequirements\r\n\r\n    Proven experience as a Web Developer (portfolio or projects required).\r\n    Strong knowledge of front-end technologies: HTML5, CSS3, JavaScript (and frameworks like React, Vue, or Angular).\r\n    Familiarity with back-end technologies such as Node.js, Python, PHP, or Ruby (depending on role).\r\n    Experience with databases (SQL/NoSQL) and RESTful APIs.\r\n    Understanding of UI/UX principles and responsive design.\r\n    Knowledge of cloud hosting platforms (AWS, Vercel, Netlify, etc.) is a plus.\r\n    Excellent problem-solving and communication skills.\r\n    Bachelor’s degree in Computer Science, Engineering, or equivalent practical experience.\r\n     By applying to this position, we’ll create your Simera Professional Key (SPK) — a unique key that helps you connect with employers, stand out, and secure the right match.', 'closed', 1, 5, '2026-01-20 07:10:39', 45000.00, '2026-01-20 07:05:48'),
(5, 2, 2, 'Assistant Manager - Mobile Application Developer (Android & iOS)', 'The Mobile Application Developer is responsible for designing, developing, and maintaining high-quality mobile applications for Android and iOS. This role focuses on building secure, scalable, and high-performance mobile solutions that align with Tekvision’s enterprise systems and digital transformation initiatives. The developer will work closely with backend engineers, UI/UX designers, product owners, and QA teams to translate business and technical requirements into reliable mobile applications. This role also requires a strong understanding of mobile platforms, application lifecycles, performance optimization, and the complete setup of modern mobile development environments.\r\n\r\nAt Tekvision, we value professionalism, collaboration, continuous learning, and ownership. The Mobile Application Developer is expected to demonstrate strong communication skills, a proactive mindset, and a commitment to building high-quality, secure, and sustainable software solutions that support long-term business goals.', 'closed', 1, 6, '2026-01-20 20:38:13', 70000.00, '2026-01-20 19:24:17'),
(6, 2, 2, 'React Native Developer (FinTech Mobile App)', 'We’re looking for a React Native Developer to join our team and help build a high-quality mobile application in the FinTech space. The app involves user interaction with various financial operations, so experience working on financial or transactional applications will be a big plus.\r\n\r\nRequirements:\r\n• Strong experience with React Native and modern JavaScript/TypeScript.\r\n• Proven experience building and deploying mobile apps (iOS & Android).\r\n• Familiarity with RESTful APIs and version control (Git).\r\n• Prior experience in FinTech or other financial systems is a major plus.\r\n• Good verbal English communication skills are required.\r\n• Must be able to work during US business hours (Eastern Time Zone).', 'closed', 1, 6, '2026-01-24 08:51:21', 20000.00, '2026-01-20 19:35:15');

-- --------------------------------------------------------

--
-- Table structure for table `job_applications`
--

CREATE TABLE `job_applications` (
  `application_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `freelancer_id` int(11) NOT NULL,
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_applications`
--

INSERT INTO `job_applications` (`application_id`, `job_id`, `freelancer_id`, `applied_at`) VALUES
(1, 1, 4, '2026-01-16 15:40:11'),
(2, 1, 5, '2026-01-17 08:20:37'),
(3, 3, 5, '2026-01-19 09:56:09'),
(4, 4, 5, '2026-01-20 07:09:16'),
(5, 5, 6, '2026-01-20 19:35:51'),
(6, 6, 6, '2026-01-24 08:50:03');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `sender_type` enum('freelancer','client') NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `job_id`, `sender_type`, `sender_id`, `message_text`, `created_at`) VALUES
(1, 1, 'client', 1, 'hi', '2026-01-18 17:27:35'),
(2, 1, 'freelancer', 4, 'hello', '2026-01-18 20:22:46'),
(3, 3, 'freelancer', 5, 'hi client 2', '2026-01-19 10:10:39'),
(4, 3, 'client', 2, 'yeah, how you doing', '2026-01-19 10:11:05'),
(5, 3, 'freelancer', 5, 'good', '2026-01-19 10:11:16'),
(6, 4, 'freelancer', 5, 'hi', '2026-01-20 07:11:09'),
(7, 4, 'client', 3, 'hello naimur', '2026-01-20 07:11:43'),
(8, 4, 'client', 3, 'how is my work going', '2026-01-20 07:11:56'),
(9, 4, 'freelancer', 5, 'bad', '2026-01-20 07:12:17'),
(10, 3, 'client', 2, 'hi there', '2026-01-20 17:32:12'),
(11, 3, 'client', 2, 'hey', '2026-01-20 19:15:30'),
(12, 4, 'freelancer', 5, 'Hey man, pay me', '2026-01-22 14:18:37'),
(13, 5, 'freelancer', 6, 'hi', '2026-01-24 08:24:49'),
(14, 5, 'freelancer', 6, 'hlw', '2026-01-24 08:59:38'),
(15, 5, 'client', 2, 'check', '2026-01-24 09:02:21');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `hire_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','completed','failed') DEFAULT 'pending',
  `paid_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `hire_id`, `amount`, `payment_status`, `paid_at`) VALUES
(1, 3, 50000.00, 'completed', '2026-01-20 17:15:11'),
(2, 6, 70000.00, 'completed', '2026-01-20 20:45:03'),
(3, 7, 20000.00, 'completed', '2026-01-24 08:51:30');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `question_id` int(11) NOT NULL,
  `skill_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`question_id`, `skill_id`, `question_text`, `created_at`) VALUES
(1, 1, 'What does HTML stand for?', '2026-01-15 07:27:33'),
(2, 1, 'Which tag is used to insert an image in HTML?', '2026-01-15 07:28:29'),
(3, 1, 'Which CSS property is used to change text color?', '2026-01-15 07:29:35'),
(4, 1, 'Which HTML tag is used to create a hyperlink?', '2026-01-15 07:30:26'),
(5, 1, 'Which CSS property controls the space inside an element?', '2026-01-15 07:31:29'),
(6, 1, 'Which CSS display value places elements in a flexible layout?', '2026-01-15 07:32:01'),
(7, 1, 'Which symbol is used for comments in JavaScript?', '2026-01-15 07:32:43'),
(8, 1, 'Which JavaScript keyword is used to declare a variable?', '2026-01-15 07:33:29'),
(9, 1, 'Which method outputs data to the browser console?', '2026-01-15 07:34:05'),
(10, 1, 'Which operator is used to compare both value and type in JavaScript?', '2026-01-15 07:34:36'),
(11, 1, 'PHP code is executed on the _____.', '2026-01-15 07:35:13'),
(12, 1, 'Which symbol is used to start a variable in PHP?', '2026-01-15 07:35:54'),
(13, 1, 'Which PHP function is used to connect to a MySQL database?', '2026-01-15 07:36:28'),
(14, 1, 'Which SQL command is used to retrieve data from a table?', '2026-01-15 07:37:10'),
(15, 1, 'What does CSS stand for?', '2026-01-15 07:37:53'),
(16, 2, 'Which language is primarily used for Android app development?', '2026-01-15 07:39:24'),
(17, 2, 'Which IDE is officially recommended for Android development?', '2026-01-15 07:39:56'),
(18, 2, 'Which programming language is mainly used for iOS app development?', '2026-01-15 07:40:34'),
(20, 2, 'Which framework allows cross-platform mobile app development?', '2026-01-15 07:41:42'),
(21, 2, 'Flutter apps are written using which language?', '2026-01-15 07:42:09'),
(22, 2, 'Which component represents a screen in Android?', '2026-01-15 07:42:38'),
(23, 2, 'Which layout arranges UI elements vertically or horizontally in Android?', '2026-01-15 07:43:06'),
(24, 2, 'What is APK in Android?', '2026-01-15 07:43:39'),
(25, 2, 'Which tool is used to test iOS apps?', '2026-01-15 07:44:18'),
(26, 2, 'Which lifecycle method is called when an Android activity starts?', '2026-01-15 07:44:53'),
(27, 2, 'React Native uses which language for UI logic?', '2026-01-15 07:45:21'),
(28, 2, 'Which database is commonly used for local storage in Android?', '2026-01-15 07:45:57'),
(29, 2, 'Which mobile OS is developed by Apple?', '2026-01-15 07:46:25'),
(30, 2, 'Which store is used to publish Android apps?', '2026-01-15 07:47:00');

-- --------------------------------------------------------

--
-- Table structure for table `question_options`
--

CREATE TABLE `question_options` (
  `option_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `option_label` char(1) NOT NULL,
  `option_text` text NOT NULL,
  `is_correct` tinyint(1) DEFAULT 0
) ;

--
-- Dumping data for table `question_options`
--

INSERT INTO `question_options` (`option_id`, `question_id`, `option_label`, `option_text`, `is_correct`) VALUES
(1, 1, 'A', 'Hyper Tool Markup Language', 0),
(2, 1, 'B', 'Hyperlinks and Text Markup Language', 0),
(3, 1, 'C', 'Hyper Text Markup Language', 1),
(4, 1, 'D', 'Home Tool Markup Language', 0),
(5, 2, 'A', '<picture>', 0),
(6, 2, 'B', '<image>', 0),
(7, 2, 'C', '<img>', 1),
(8, 2, 'D', '<src>', 0),
(9, 3, 'A', 'font-color', 0),
(10, 3, 'B', 'text-color', 0),
(11, 3, 'C', 'color', 1),
(12, 3, 'D', 'fgcolor', 0),
(13, 4, 'A', '<link>', 0),
(14, 4, 'B', '<href>', 0),
(15, 4, 'C', '<url>', 0),
(16, 4, 'D', '<a>', 1),
(17, 5, 'A', 'margin', 0),
(18, 5, 'B', 'padding', 1),
(19, 5, 'C', 'border', 0),
(20, 5, 'D', 'spacing', 0),
(21, 6, 'A', 'block', 0),
(22, 6, 'B', 'inline', 0),
(23, 6, 'C', 'flex', 1),
(24, 6, 'D', 'float', 0),
(25, 7, 'A', '<!-- -->', 0),
(26, 7, 'B', '//', 1),
(27, 7, 'C', '**', 0),
(28, 7, 'D', '##', 0),
(29, 8, 'A', 'define', 0),
(30, 8, 'B', 'var', 0),
(31, 8, 'C', 'let', 0),
(32, 8, 'D', 'Both B and C', 1),
(33, 9, 'A', 'console.print()', 0),
(34, 9, 'B', 'console.log()', 1),
(35, 9, 'C', 'print.console()', 0),
(36, 9, 'D', 'log.console()', 0),
(37, 10, 'A', '=', 0),
(38, 10, 'B', '==', 0),
(39, 10, 'C', '===', 1),
(40, 10, 'D', '!=', 0),
(41, 11, 'A', 'browser', 0),
(42, 11, 'B', 'server-side', 1),
(43, 11, 'C', 'database', 0),
(44, 11, 'D', 'client-side', 0),
(45, 12, 'A', '#', 0),
(46, 12, 'B', '@', 0),
(47, 12, 'C', '&', 0),
(48, 12, 'D', '$', 1),
(49, 13, 'A', 'mysql_connect()', 0),
(50, 13, 'B', 'mysqli_connect()', 1),
(51, 13, 'C', 'connect_mysql()', 0),
(52, 13, 'D', 'db_connect()', 0),
(53, 14, 'A', 'GET', 0),
(54, 14, 'B', 'FETCH', 0),
(55, 14, 'C', 'SELECT', 1),
(56, 14, 'D', 'EXTRACT', 0),
(57, 15, 'A', 'Creative Style Sheets', 0),
(58, 15, 'B', 'Colorful Style Sheets', 0),
(59, 15, 'C', 'Computer Style Sheets', 0),
(60, 15, 'D', 'Cascading Style Sheets', 1),
(61, 16, 'A', 'Swift', 0),
(62, 16, 'B', 'Kotlin', 1),
(63, 16, 'C', 'Objective-C', 0),
(64, 16, 'D', 'Dart', 0),
(65, 17, 'A', 'Eclipse', 0),
(66, 17, 'B', 'Visual Studio', 0),
(67, 17, 'C', 'Xcode', 0),
(68, 17, 'D', 'Android Studio', 1),
(69, 18, 'A', 'Java', 0),
(70, 18, 'B', 'Kotlin', 0),
(71, 18, 'C', 'Swift', 1),
(72, 18, 'D', 'Dart', 0),
(77, 20, 'A', 'Laravel', 0),
(78, 20, 'B', 'Django', 0),
(79, 20, 'C', 'Flutter', 1),
(80, 20, 'D', 'Bootstrap', 0),
(81, 21, 'A', 'Dart', 1),
(82, 21, 'B', 'JavaScript', 0),
(83, 21, 'C', 'Kotlin', 0),
(84, 21, 'D', 'Swift', 0),
(85, 22, 'A', 'Fragment', 0),
(86, 22, 'B', 'Activity', 1),
(87, 22, 'C', 'Intent', 0),
(88, 22, 'D', 'Service', 0),
(89, 23, 'A', 'RelativeLayout', 0),
(90, 23, 'B', 'ConstraintLayout', 0),
(91, 23, 'C', 'LinearLayout', 1),
(92, 23, 'D', 'FrameLayout', 0),
(93, 24, 'A', 'Android Phone Kit', 0),
(94, 24, 'B', 'Android Package', 1),
(95, 24, 'C', 'Application Programming Kit', 0),
(96, 24, 'D', 'Android Program Kernel', 0),
(97, 25, 'A', 'Android Emulato', 0),
(98, 25, 'B', 'Xcode Simulator', 1),
(99, 25, 'C', 'BlueStacks', 0),
(100, 25, 'D', 'Firebase Console', 0),
(101, 26, 'A', 'onStop()', 0),
(102, 26, 'B', 'onDestroy()', 0),
(103, 26, 'C', 'onCreate()', 1),
(104, 26, 'D', 'onPause()', 0),
(105, 27, 'A', 'Java', 0),
(106, 27, 'B', 'Kotlin', 0),
(107, 27, 'C', 'JavaScript', 1),
(108, 27, 'D', 'Dart', 0),
(109, 28, 'A', 'SQLite', 1),
(110, 28, 'B', 'MongoDB', 0),
(111, 28, 'C', 'MySQL', 0),
(112, 28, 'D', 'Firebase', 0),
(113, 29, 'A', 'Android', 0),
(114, 29, 'B', 'iOS', 1),
(115, 29, 'C', 'Windows Phone', 0),
(116, 29, 'D', 'HarmonyOS', 0),
(117, 30, 'A', 'App Store', 0),
(118, 30, 'B', 'Microsoft Store', 0),
(119, 30, 'C', 'Google Play Store', 1),
(120, 30, 'D', 'Amazon Cloud', 0);

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `rating_id` int(11) NOT NULL,
  `hire_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `review` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `skill_categories`
--

CREATE TABLE `skill_categories` (
  `skill_id` int(11) NOT NULL,
  `skill_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `skill_categories`
--

INSERT INTO `skill_categories` (`skill_id`, `skill_name`, `created_at`) VALUES
(1, 'Web Development', '2026-01-15 07:21:58'),
(2, 'Mobile App Development', '2026-01-15 07:22:26');

-- --------------------------------------------------------

--
-- Table structure for table `skill_courses`
--

CREATE TABLE `skill_courses` (
  `skill_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `skill_courses`
--

INSERT INTO `skill_courses` (`skill_id`, `course_id`) VALUES
(1, 1),
(2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `skill_tests`
--

CREATE TABLE `skill_tests` (
  `test_id` int(11) NOT NULL,
  `skill_id` int(11) NOT NULL,
  `total_questions` int(11) DEFAULT 15,
  `pass_percentage` decimal(5,2) DEFAULT 70.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `test_answers`
--

CREATE TABLE `test_answers` (
  `answer_id` int(11) NOT NULL,
  `attempt_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `selected_option` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `test_attempts`
--

CREATE TABLE `test_attempts` (
  `attempt_id` int(11) NOT NULL,
  `freelancer_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `passed` tinyint(1) DEFAULT NULL,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_reports`
--

CREATE TABLE `user_reports` (
  `report_id` int(11) NOT NULL,
  `reported_user_id` int(11) NOT NULL,
  `reported_user_type` enum('freelancer','client') NOT NULL,
  `reporter_id` int(11) DEFAULT NULL,
  `reporter_type` enum('freelancer','client','admin') DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL,
  `report_reason` text NOT NULL,
  `report_message` text DEFAULT NULL,
  `status` enum('pending','reviewed','resolved') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_reports`
--

INSERT INTO `user_reports` (`report_id`, `reported_user_id`, `reported_user_type`, `reporter_id`, `reporter_type`, `job_id`, `report_reason`, `report_message`, `status`, `created_at`) VALUES
(2, 5, 'freelancer', 2, 'client', 3, 'Poor Work Quality', 'He has no skill.', 'reviewed', '2026-01-19 14:14:52'),
(3, 5, 'freelancer', 3, 'client', 4, 'Unprofessional Behavior', 'Behavior is not so good.', 'resolved', '2026-01-20 07:13:11'),
(4, 3, 'client', 5, 'freelancer', NULL, 'Unclear Requirements', 'He doesn\'t provide clear req.', 'pending', '2026-01-20 07:29:07'),
(7, 3, 'client', 5, 'freelancer', 4, 'Non-Payment', 'I worked for him but didn\'t get any payment.', 'resolved', '2026-01-22 14:17:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`client_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `freelancers`
--
ALTER TABLE `freelancers`
  ADD PRIMARY KEY (`freelancer_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_freelancers_skill` (`skill_id`);

--
-- Indexes for table `freelancer_test_attempts`
--
ALTER TABLE `freelancer_test_attempts`
  ADD PRIMARY KEY (`attempt_id`),
  ADD KEY `skill_id` (`skill_id`);

--
-- Indexes for table `hired_freelancers`
--
ALTER TABLE `hired_freelancers`
  ADD PRIMARY KEY (`hire_id`),
  ADD UNIQUE KEY `unique_job_hire` (`job_id`),
  ADD KEY `freelancer_id` (`freelancer_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`job_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `skill_id` (`skill_id`),
  ADD KEY `hired_freelancer_id` (`hired_freelancer_id`);

--
-- Indexes for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `freelancer_id` (`freelancer_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `job_id` (`job_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `hire_id` (`hire_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `fk_question_skill` (`skill_id`);

--
-- Indexes for table `question_options`
--
ALTER TABLE `question_options`
  ADD PRIMARY KEY (`option_id`),
  ADD KEY `fk_option_question` (`question_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`rating_id`),
  ADD KEY `hire_id` (`hire_id`);

--
-- Indexes for table `skill_categories`
--
ALTER TABLE `skill_categories`
  ADD PRIMARY KEY (`skill_id`),
  ADD UNIQUE KEY `skill_name` (`skill_name`);

--
-- Indexes for table `skill_courses`
--
ALTER TABLE `skill_courses`
  ADD PRIMARY KEY (`skill_id`,`course_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `skill_tests`
--
ALTER TABLE `skill_tests`
  ADD PRIMARY KEY (`test_id`),
  ADD KEY `skill_id` (`skill_id`);

--
-- Indexes for table `test_answers`
--
ALTER TABLE `test_answers`
  ADD PRIMARY KEY (`answer_id`),
  ADD KEY `attempt_id` (`attempt_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `test_attempts`
--
ALTER TABLE `test_attempts`
  ADD PRIMARY KEY (`attempt_id`),
  ADD KEY `freelancer_id` (`freelancer_id`),
  ADD KEY `test_id` (`test_id`);

--
-- Indexes for table `user_reports`
--
ALTER TABLE `user_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD UNIQUE KEY `unique_job_report` (`job_id`,`reported_user_id`,`reporter_id`),
  ADD KEY `reported_user_id` (`reported_user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `client_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `freelancers`
--
ALTER TABLE `freelancers`
  MODIFY `freelancer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `freelancer_test_attempts`
--
ALTER TABLE `freelancer_test_attempts`
  MODIFY `attempt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `hired_freelancers`
--
ALTER TABLE `hired_freelancers`
  MODIFY `hire_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `job_applications`
--
ALTER TABLE `job_applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `question_options`
--
ALTER TABLE `question_options`
  MODIFY `option_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `skill_categories`
--
ALTER TABLE `skill_categories`
  MODIFY `skill_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `skill_tests`
--
ALTER TABLE `skill_tests`
  MODIFY `test_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test_answers`
--
ALTER TABLE `test_answers`
  MODIFY `answer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test_attempts`
--
ALTER TABLE `test_attempts`
  MODIFY `attempt_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_reports`
--
ALTER TABLE `user_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `freelancers`
--
ALTER TABLE `freelancers`
  ADD CONSTRAINT `fk_freelancers_skill` FOREIGN KEY (`skill_id`) REFERENCES `skill_categories` (`skill_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `freelancers_ibfk_1` FOREIGN KEY (`skill_id`) REFERENCES `skill_categories` (`skill_id`);

--
-- Constraints for table `freelancer_test_attempts`
--
ALTER TABLE `freelancer_test_attempts`
  ADD CONSTRAINT `freelancer_test_attempts_ibfk_1` FOREIGN KEY (`skill_id`) REFERENCES `skill_categories` (`skill_id`) ON DELETE CASCADE;

--
-- Constraints for table `hired_freelancers`
--
ALTER TABLE `hired_freelancers`
  ADD CONSTRAINT `hired_freelancers_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`job_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hired_freelancers_ibfk_2` FOREIGN KEY (`freelancer_id`) REFERENCES `freelancers` (`freelancer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hired_freelancers_ibfk_3` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`) ON DELETE CASCADE;

--
-- Constraints for table `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `jobs_ibfk_2` FOREIGN KEY (`skill_id`) REFERENCES `skill_categories` (`skill_id`),
  ADD CONSTRAINT `jobs_ibfk_3` FOREIGN KEY (`hired_freelancer_id`) REFERENCES `freelancers` (`freelancer_id`) ON DELETE SET NULL;

--
-- Constraints for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD CONSTRAINT `job_applications_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`job_id`),
  ADD CONSTRAINT `job_applications_ibfk_2` FOREIGN KEY (`freelancer_id`) REFERENCES `freelancers` (`freelancer_id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`job_id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`hire_id`) REFERENCES `hired_freelancers` (`hire_id`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `fk_question_skill` FOREIGN KEY (`skill_id`) REFERENCES `skill_categories` (`skill_id`) ON DELETE CASCADE;

--
-- Constraints for table `question_options`
--
ALTER TABLE `question_options`
  ADD CONSTRAINT `fk_option_question` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`) ON DELETE CASCADE;

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`hire_id`) REFERENCES `hired_freelancers` (`hire_id`) ON DELETE CASCADE;

--
-- Constraints for table `skill_courses`
--
ALTER TABLE `skill_courses`
  ADD CONSTRAINT `skill_courses_ibfk_1` FOREIGN KEY (`skill_id`) REFERENCES `skill_categories` (`skill_id`),
  ADD CONSTRAINT `skill_courses_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);

--
-- Constraints for table `skill_tests`
--
ALTER TABLE `skill_tests`
  ADD CONSTRAINT `skill_tests_ibfk_1` FOREIGN KEY (`skill_id`) REFERENCES `skill_categories` (`skill_id`);

--
-- Constraints for table `test_answers`
--
ALTER TABLE `test_answers`
  ADD CONSTRAINT `test_answers_ibfk_1` FOREIGN KEY (`attempt_id`) REFERENCES `test_attempts` (`attempt_id`),
  ADD CONSTRAINT `test_answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`);

--
-- Constraints for table `test_attempts`
--
ALTER TABLE `test_attempts`
  ADD CONSTRAINT `test_attempts_ibfk_1` FOREIGN KEY (`freelancer_id`) REFERENCES `freelancers` (`freelancer_id`),
  ADD CONSTRAINT `test_attempts_ibfk_2` FOREIGN KEY (`test_id`) REFERENCES `skill_tests` (`test_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
