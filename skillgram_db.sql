-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 24, 2025 at 06:25 PM
-- Server version: 8.3.0
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `skillgram_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `achievements`
--

DROP TABLE IF EXISTS `achievements`;
CREATE TABLE IF NOT EXISTS `achievements` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `badge_name` varchar(100) NOT NULL,
  `badge_desc` text,
  `icon` varchar(255) DEFAULT NULL,
  `condition_type` varchar(50) DEFAULT NULL,
  `condition_value` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `achievements`
--

INSERT INTO `achievements` (`id`, `badge_name`, `badge_desc`, `icon`, `condition_type`, `condition_value`) VALUES
(1, 'First Problem', 'Solved your very first problem!', '✅', 'problems_solved', 1),
(2, 'Novice Coder', 'Solved 10 problems!', '💻', 'problems_solved', 10),
(3, 'XP Hunter', 'Earned 100 XP', '⚡', 'xp', 100),
(4, '1 Week Streak', 'Logged in 7 days in a row', '🔥', 'streak', 7),
(5, '3 Day Streak', 'Logged in for 3 days in a row!', '🔥', 'streak', 3),
(6, '10 Day Streak', 'Logged in for 10 days in a row!', '🔥', 'streak', 10),
(7, '30 Day Streak', 'Logged in for 30 days in a row!', '🔥', 'streak', 30);

-- --------------------------------------------------------

--
-- Table structure for table `challenge_submissions`
--

DROP TABLE IF EXISTS `challenge_submissions`;
CREATE TABLE IF NOT EXISTS `challenge_submissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `challenge_id` int UNSIGNED NOT NULL,
  `user_id` int NOT NULL,
  `code` mediumtext NOT NULL,
  `language` varchar(50) DEFAULT 'C++',
  `status` enum('Pending','Accepted','Wrong Answer','Error') DEFAULT 'Pending',
  `submitted_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_submissions_ch` (`challenge_id`),
  KEY `idx_submissions_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `challenge_tests`
--

DROP TABLE IF EXISTS `challenge_tests`;
CREATE TABLE IF NOT EXISTS `challenge_tests` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `challenge_id` int UNSIGNED NOT NULL,
  `input` text NOT NULL,
  `expected_output` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `challenge_id` (`challenge_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `challenge_tests`
--

INSERT INTO `challenge_tests` (`id`, `challenge_id`, `input`, `expected_output`) VALUES
(1, 6, 'nums=2 7 11 15\r\ntarget=9', '0 1'),
(2, 11, 'nums=2 7 11 15\\ntarget=9', '0 1');

-- --------------------------------------------------------

--
-- Table structure for table `coding_challenges`
--

DROP TABLE IF EXISTS `coding_challenges`;
CREATE TABLE IF NOT EXISTS `coding_challenges` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `difficulty` enum('Easy','Medium','Hard') NOT NULL,
  `sample_input` text,
  `sample_output` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `coding_challenges`
--

INSERT INTO `coding_challenges` (`id`, `title`, `description`, `difficulty`, `sample_input`, `sample_output`, `created_at`) VALUES
(6, 'Two Sum [Python]', 'Given an array and target, print indices of two numbers that add up to target.', 'Easy', 'nums=2 7 11 15, target=9', '0 1', '2025-09-10 13:28:42'),
(11, 'Two Sum [Python]', 'Return indices of two numbers adding up to target.', 'Easy', 'nums=2 7 11 15, target=9', '0 1', '2025-09-10 13:40:15'),
(12, 'Valid Parentheses [Python]', 'Check if parentheses string is valid.', 'Easy', '()[]{}', 'Yes', '2025-09-10 13:40:15'),
(13, 'Merge Intervals [Python]', 'Merge overlapping intervals.', 'Medium', '[[1,3],[2,6],[8,10],[15,18]]', '[[1,6],[8,10],[15,18]]', '2025-09-10 13:40:15'),
(14, 'Two Sum [C++]', 'Return indices of two numbers adding up to target.', 'Easy', 'nums=2 7 11 15, target=9', '0 1', '2025-09-10 13:40:15'),
(15, 'Fast IO [C++]', 'Read input fast and print sum.', 'Medium', '3\\n5 7 9', '21', '2025-09-10 13:40:15'),
(16, 'STL Map Practice [C++]', 'Count frequencies using std::map.', 'Medium', 'a b a c a b', 'a:3 b:2 c:1', '2025-09-10 13:40:15'),
(17, 'Two Sum [Java]', 'Return indices of two numbers adding up to target.', 'Easy', 'nums=2 7 11 15, target=9', '0 1', '2025-09-10 13:40:15'),
(18, 'Java Streams Basics [Java]', 'Use streams to process a list.', 'Medium', '1 2 3 4', '2 4', '2025-09-10 13:40:15'),
(19, 'OOP Inheritance [Java]', 'Demonstrate simple inheritance and method override.', 'Medium', 'Dog->Animal', 'Woof', '2025-09-10 13:40:15');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
CREATE TABLE IF NOT EXISTS `courses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `level` varchar(50) NOT NULL,
  `modules` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `title`, `description`, `level`, `modules`) VALUES
(1, 'DSA', 'DSA 0 to zero ', 'moderate', 5);

-- --------------------------------------------------------

--
-- Table structure for table `discussions`
--

DROP TABLE IF EXISTS `discussions`;
CREATE TABLE IF NOT EXISTS `discussions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_disc_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `discussions`
--

INSERT INTO `discussions` (`id`, `user_id`, `title`, `body`, `created_at`) VALUES
(1, 3, 'Help me in DSA', 'I want a code of bubble sort', '2025-08-24 21:40:23'),
(2, 3, 'hey', 'hey hey hey...', '2025-09-10 16:05:00'),
(3, 3, 'use w3school for dsa', '', '2025-09-10 16:05:43');

-- --------------------------------------------------------

--
-- Table structure for table `followers`
--

DROP TABLE IF EXISTS `followers`;
CREATE TABLE IF NOT EXISTS `followers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `follower_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `follower_id` (`follower_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `followers`
--

INSERT INTO `followers` (`id`, `user_id`, `follower_id`) VALUES
(1, 1, 2),
(2, 1, 3),
(3, 3, 2),
(4, 3, 2),
(5, 1, 5),
(6, 2, 5),
(7, 5, 2),
(8, 1, 1),
(9, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

DROP TABLE IF EXISTS `likes`;
CREATE TABLE IF NOT EXISTS `likes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id`, `post_id`, `user_id`, `created_at`) VALUES
(2, 6, 3, '2025-09-10 08:49:59'),
(6, 3, 3, '2025-09-10 10:41:43');

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

DROP TABLE IF EXISTS `notes`;
CREATE TABLE IF NOT EXISTS `notes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `subject` varchar(150) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `content` mediumtext,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notes_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`id`, `user_id`, `subject`, `title`, `content`, `file_path`, `created_at`) VALUES
(1, 3, 'DSA', 'DSA Patterns Cheatsheet', 'Quick reference for common DSA patterns like sliding window, two pointers, recursion.', 'https://www.geeksforgeeks.org/', '2025-08-25 19:11:55'),
(2, 3, 'JavaScript', 'JS Essentials', 'Notes covering scopes, closures, async programming, and DOM manipulation.', 'https://developer.mozilla.org/en-US/docs/Web/JavaScript/Guide', '2025-08-25 19:11:55'),
(3, 3, 'SQL', 'SQL Joins Guide', 'Visual guide for INNER JOIN, LEFT JOIN, RIGHT JOIN, and FULL OUTER JOIN.', 'https://www.w3schools.com/sql/sql_join.asp', '2025-08-25 19:11:55'),
(4, 3, 'CSS', 'Flex & Grid Notes', 'Tips and examples for creating responsive layouts using CSS Flexbox and Grid.', 'https://css-tricks.com/snippets/css/complete-guide-grid/', '2025-08-25 19:11:55'),
(5, 3, 'Python', 'Python Tricks', 'Useful Python tricks and snippets for problem-solving.', 'https://realpython.com/', '2025-08-25 19:11:55');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
CREATE TABLE IF NOT EXISTS `posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `content`, `image`, `created_at`, `title`) VALUES
(1, 3, 'DSA is best for you....', NULL, '2025-08-24 07:36:21', ' Learing DSA'),
(3, 5, 'heyy', NULL, '2025-08-24 12:32:10', 'heyy');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
CREATE TABLE IF NOT EXISTS `projects` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `tags` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `type` varchar(50) NOT NULL DEFAULT 'typing',
  `required_xp` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `title`, `description`, `tags`, `created_at`, `type`, `required_xp`) VALUES
(1, 'Portfolio Website', 'A personal portfolio website to showcase projects and skills.', 'web,html,css,js', '2025-08-25 18:42:19', 'typing', 0),
(2, 'ToDo App', 'A simple task management app with add, edit, and delete functionality.', 'javascript,web,productivity', '2025-08-25 18:42:19', 'memory', 0),
(3, 'Chat Application', 'A real-time chat app using WebSocket and Node.js.', 'nodejs,chat,realtime', '2025-08-25 18:42:19', 'memory', 0),
(4, 'Weather App', 'An app that shows current weather information using API.', 'api,web,js', '2025-08-25 18:42:19', 'typing', 0),
(5, 'Blog Platform', 'A blogging platform with user authentication and CRUD operations.', 'php,web,database', '2025-08-25 18:42:19', 'typing', 0);

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

DROP TABLE IF EXISTS `quizzes`;
CREATE TABLE IF NOT EXISTS `quizzes` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `topic` varchar(100) NOT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `difficulty` enum('Easy','Medium','Hard') NOT NULL DEFAULT 'Easy',
  PRIMARY KEY (`id`),
  KEY `idx_quizzes_creator` (`created_by`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `title`, `topic`, `created_by`, `created_at`, `difficulty`) VALUES
(13, 'Python Basics', 'Python', 3, '2025-09-10 13:28:42', 'Easy'),
(14, 'C++ Fundamentals', 'C++', 3, '2025-09-10 13:28:42', 'Medium'),
(15, 'Java OOP', 'Java', 3, '2025-09-10 13:28:42', 'Medium'),
(16, 'Python Basics Quiz [Python]', 'Python', 3, '2025-09-10 13:40:15', 'Easy'),
(17, 'Templates & STL Quiz [C++]', 'C++', 3, '2025-09-10 13:40:15', 'Medium'),
(18, 'Java OOP Quiz [Java]', 'Java', 3, '2025-09-10 13:40:15', 'Medium');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempts`
--

DROP TABLE IF EXISTS `quiz_attempts`;
CREATE TABLE IF NOT EXISTS `quiz_attempts` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `quiz_id` int UNSIGNED NOT NULL,
  `user_id` int NOT NULL,
  `score` int NOT NULL,
  `attempted_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_qatt_quiz` (`quiz_id`),
  KEY `idx_qatt_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `quiz_attempts`
--

INSERT INTO `quiz_attempts` (`id`, `quiz_id`, `user_id`, `score`, `attempted_at`) VALUES
(1, 16, 3, 1, '2025-09-10 14:01:08'),
(2, 15, 3, 2, '2025-09-10 14:01:45');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_questions`
--

DROP TABLE IF EXISTS `quiz_questions`;
CREATE TABLE IF NOT EXISTS `quiz_questions` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `quiz_id` int UNSIGNED NOT NULL,
  `question` text NOT NULL,
  `option_a` text NOT NULL,
  `option_b` text NOT NULL,
  `option_c` text NOT NULL,
  `option_d` text NOT NULL,
  `correct_option` enum('A','B','C','D') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_qq_quiz` (`quiz_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `quiz_questions`
--

INSERT INTO `quiz_questions` (`id`, `quiz_id`, `question`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`) VALUES
(13, 13, 'What is the output of len(\"hello\")?', '4', '5', '6', 'Error', 'B'),
(14, 13, 'Which keyword defines a function?', 'func', 'def', 'fn', 'function', 'B'),
(15, 13, 'Which is a list?', '{1,2,3}', '(1,2,3)', '[1,2,3]', '<1,2,3>', 'C'),
(16, 14, 'Which header includes cout?', '<stdio.h>', '<iostream>', '<string>', '<vector>', 'B'),
(17, 14, 'What is 0-based indexing?', 'Starts at 1', 'Starts at 0', 'Starts at -1', 'None', 'B'),
(18, 15, 'Which keyword prevents inheritance?', 'static', 'final', 'const', 'private', 'B'),
(19, 15, 'Which is not OOP pillar?', 'Encapsulation', 'Abstraction', 'Compilation', 'Polymorphism', 'C'),
(20, 16, 'len(\"hello\") = ?', '4', '5', '6', 'Error', 'B'),
(21, 17, 'Which header has vector?', '<array>', '<vector>', '<list>', '<map>', 'B'),
(22, 18, 'Keyword to prevent inheritance?', 'static', 'final', 'const', 'private', 'B');

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

DROP TABLE IF EXISTS `resources`;
CREATE TABLE IF NOT EXISTS `resources` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `link` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`id`, `title`, `link`, `category`, `created_at`) VALUES
(1, 'DSA Cheatsheet (PDF)', 'https://example.com/dsa-cheatsheet.pdf', 'DSA', '2025-08-25 19:03:55'),
(2, 'JavaScript Essentials (PDF)', 'https://example.com/js-essentials.pdf', 'JavaScript', '2025-08-25 19:03:55'),
(3, 'SQL Joins Visual Guide', 'https://www.w3schools.com/sql/sql_join.asp', 'SQL', '2025-08-25 19:03:55'),
(4, 'CSS Flex & Grid Cards', 'https://css-tricks.com/snippets/css/complete-guide-grid/', 'CSS', '2025-08-25 19:03:55'),
(5, 'Python Basics', 'https://example.com/python-basics.pdf', 'Python', '2025-08-25 19:03:55');

-- --------------------------------------------------------

--
-- Table structure for table `roadmaps`
--

DROP TABLE IF EXISTS `roadmaps`;
CREATE TABLE IF NOT EXISTS `roadmaps` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `roadmaps`
--

INSERT INTO `roadmaps` (`id`, `title`, `description`, `created_at`) VALUES
(1, 'Frontend Development', 'Learn HTML, CSS, JavaScript, and build responsive web interfaces.', '2025-08-25 19:04:07'),
(2, 'Backend Development', 'Learn server-side programming and databases.', '2025-08-25 19:04:07'),
(3, 'Data Structures & Algorithms', 'Master DSA concepts and problem-solving skills.', '2025-08-25 19:04:07');

-- --------------------------------------------------------

--
-- Table structure for table `roadmap_steps`
--

DROP TABLE IF EXISTS `roadmap_steps`;
CREATE TABLE IF NOT EXISTS `roadmap_steps` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `roadmap_id` int UNSIGNED NOT NULL,
  `step_number` int UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` text,
  PRIMARY KEY (`id`),
  KEY `idx_steps_roadmap` (`roadmap_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `roadmap_steps`
--

INSERT INTO `roadmap_steps` (`id`, `roadmap_id`, `step_number`, `title`, `content`) VALUES
(1, 1, 1, 'Learn HTML', 'Understand the structure of web pages, tags, and forms.'),
(2, 1, 2, 'Learn CSS', 'Style your pages using CSS, Flexbox, and Grid.'),
(3, 1, 3, 'Learn JavaScript', 'Add interactivity using DOM, events, and functions.'),
(4, 2, 1, 'Learn PHP', 'Server-side scripting language for web apps.'),
(5, 2, 2, 'Learn MySQL', 'Database management and SQL queries.'),
(6, 2, 3, 'Learn REST APIs', 'Create APIs to communicate between frontend and backend.'),
(7, 3, 1, 'Arrays & Strings', 'Basic data structures and operations.'),
(8, 3, 2, 'Linked Lists', 'Singly and doubly linked lists.'),
(9, 3, 3, 'Trees & Graphs', 'Binary trees, traversals, and graph algorithms.');

-- --------------------------------------------------------

--
-- Table structure for table `typing_sentences`
--

DROP TABLE IF EXISTS `typing_sentences`;
CREATE TABLE IF NOT EXISTS `typing_sentences` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sentence` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `profile_pic` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'default.jpg',
  `bio` varchar(255) DEFAULT 0xF09F8CB8204254656368204353452053747564656E74207C20F09F9A80204C6561726E696E672057656220446576207C20F09F92A12053686172696E67206461696C792070726F6772657373,
  `xp` int DEFAULT '0',
  `streak` int DEFAULT '0',
  `problems_solved` int DEFAULT '0',
  `last_login` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `profile_pic`, `bio`, `xp`, `streak`, `problems_solved`, `last_login`) VALUES
(1, 'Lacota Gomez', 'baqequda@mailinator.com', '$2y$10$lL.85WkVPrawc8FyZTDmVuzAwSAixCcZXbdj0aa1.gzLaK.OZICcq', '2025-08-24 06:59:37', 'default.jpg', '🌸 BTech CSE Student | 🚀 Learning Web Dev | 💡 Sharing daily progress', 0, 0, 0, NULL),
(2, 'Melinda Price', 'xudigeder@mailinator.com', '$2y$10$PgW5.C8.Jo766PVcLeJoQeApt.9CcYnI/oejk3pRZ.DfnIBVgo8C6', '2025-08-24 07:00:21', 'default.jpg', '🌸 BTech CSE Student | 🚀 Learning Web Dev | 💡 Sharing daily progress', 0, 0, 0, NULL),
(3, 'Tanya Maheshwari', 'tanya.maheshwari15nov@gmail.com', '$2y$10$D8FRjGYuZ67l/J3LqV8eauF.gTYRdX2EZLS8jxjkYrRlzaCxpuBvq', '2025-08-24 07:01:58', 'profile_3.jpeg', '🌸 BTech CSE Student |', 32, 20, 23, '2025-09-10'),
(5, 'agrima_maheshwari', 'agrima@gmail.com', '$2y$10$i1e2RV.y3Ih5KqTDV6F3d.YTao6uJP33XjWJMohLKYWQLzvMRnGZu', '2025-08-24 12:30:25', 'default.jpg', '🌸 BTech CSE Student | 🚀 Learning Web Dev | 💡 Sharing daily progress', 0, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_achievements`
--

DROP TABLE IF EXISTS `user_achievements`;
CREATE TABLE IF NOT EXISTS `user_achievements` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `achievement_id` int UNSIGNED NOT NULL,
  `earned_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_user_ach` (`user_id`,`achievement_id`),
  KEY `idx_ua_user` (`user_id`),
  KEY `idx_ua_ach` (`achievement_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_achievements`
--

INSERT INTO `user_achievements` (`id`, `user_id`, `achievement_id`, `earned_at`) VALUES
(2, 3, 1, '2025-08-25 20:57:07'),
(3, 3, 2, '2025-08-25 20:57:07'),
(4, 3, 4, '2025-08-25 21:20:43'),
(5, 3, 5, '2025-08-25 21:20:43'),
(6, 3, 6, '2025-08-25 21:20:43');

-- --------------------------------------------------------

--
-- Table structure for table `user_skills`
--

DROP TABLE IF EXISTS `user_skills`;
CREATE TABLE IF NOT EXISTS `user_skills` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `language` varchar(50) DEFAULT NULL,
  `score` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_skills`
--

INSERT INTO `user_skills` (`id`, `user_id`, `language`, `score`) VALUES
(1, 1, 'HTML', 85),
(2, 1, 'CSS', 78),
(3, 1, 'JavaScript', 92),
(4, 1, 'PHP', 70),
(5, 1, 'Python', 88),
(16, 3, 'Java', 65),
(15, 3, 'C++', 75),
(14, 3, 'Python', 85);

-- --------------------------------------------------------

--
-- Table structure for table `user_stats`
--

DROP TABLE IF EXISTS `user_stats`;
CREATE TABLE IF NOT EXISTS `user_stats` (
  `user_id` int UNSIGNED NOT NULL,
  `xp` int DEFAULT '0',
  `streak` int DEFAULT '0',
  `last_play` date DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `challenge_submissions`
--
ALTER TABLE `challenge_submissions`
  ADD CONSTRAINT `fk_sub_ch` FOREIGN KEY (`challenge_id`) REFERENCES `coding_challenges` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sub_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `discussions`
--
ALTER TABLE `discussions`
  ADD CONSTRAINT `fk_disc_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `fk_notes_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `fk_quiz_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD CONSTRAINT `fk_qatt_quiz` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_qatt_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD CONSTRAINT `fk_qq_quiz` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `roadmap_steps`
--
ALTER TABLE `roadmap_steps`
  ADD CONSTRAINT `fk_steps_roadmap` FOREIGN KEY (`roadmap_id`) REFERENCES `roadmaps` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_achievements`
--
ALTER TABLE `user_achievements`
  ADD CONSTRAINT `fk_ua_ach` FOREIGN KEY (`achievement_id`) REFERENCES `achievements` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ua_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
