-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for osx10.10 (x86_64)
--
-- Host: localhost    Database: ccst_docrequest
-- ------------------------------------------------------
-- Server version	10.4.28-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `announcements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('announcement','transaction_days') NOT NULL,
  `content` text NOT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `published_by` bigint(20) unsigned DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `announcements_type_unique` (`type`),
  KEY `announcements_published_by_foreign` (`published_by`),
  CONSTRAINT `announcements_published_by_foreign` FOREIGN KEY (`published_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcements`
--

LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
INSERT INTO `announcements` VALUES (1,'announcement','wala paso buaks',1,11,'2026-04-15 15:42:36','2026-03-20 03:34:19','2026-04-16 20:25:39'),(2,'transaction_days','No transaction day changes at this time.',0,NULL,NULL,'2026-03-20 03:34:19','2026-03-20 03:34:19');
/*!40000 ALTER TABLE `announcements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appointments`
--

DROP TABLE IF EXISTS `appointments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appointments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `document_request_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `time_slot_id` bigint(20) unsigned NOT NULL,
  `appointment_date` date NOT NULL,
  `status` enum('scheduled','completed','missed','cancelled') NOT NULL DEFAULT 'scheduled',
  `claimed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `appointments_document_request_id_foreign` (`document_request_id`),
  KEY `appointments_user_id_foreign` (`user_id`),
  KEY `appointments_time_slot_id_foreign` (`time_slot_id`),
  CONSTRAINT `appointments_document_request_id_foreign` FOREIGN KEY (`document_request_id`) REFERENCES `document_requests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `appointments_time_slot_id_foreign` FOREIGN KEY (`time_slot_id`) REFERENCES `time_slots` (`id`),
  CONSTRAINT `appointments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appointments`
--

LOCK TABLES `appointments` WRITE;
/*!40000 ALTER TABLE `appointments` DISABLE KEYS */;
INSERT INTO `appointments` VALUES (4,94,19,3,'2026-04-23','missed',NULL,'2026-04-15 17:00:11','2026-04-16 03:49:43'),(6,96,20,2,'2026-04-29','completed',NULL,'2026-04-16 03:47:40','2026-04-16 03:49:34'),(7,98,21,4,'2026-04-22','completed',NULL,'2026-04-16 20:24:28','2026-04-16 20:27:13'),(8,99,22,1,'2026-04-23','completed',NULL,'2026-04-16 20:39:04','2026-04-16 20:39:51');
/*!40000 ALTER TABLE `appointments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('ccst-docrequest-cache-evoltiglao@gmail.com|127.0.0.1','i:1;',1775219301),('ccst-docrequest-cache-evoltiglao@gmail.com|127.0.0.1:timer','i:1775219301;',1775219301),('ccst-docrequest-cache-student@ccst.edu.ph|127.0.0.1','i:2;',1776301245),('ccst-docrequest-cache-student@ccst.edu.ph|127.0.0.1:timer','i:1776301245;',1776301245);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_request_items`
--

DROP TABLE IF EXISTS `document_request_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_request_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `document_request_id` bigint(20) unsigned NOT NULL,
  `document_type_id` bigint(20) unsigned NOT NULL,
  `copies` int(11) NOT NULL,
  `assessment_year` varchar(20) DEFAULT NULL,
  `semester` varchar(20) DEFAULT NULL,
  `fee` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_request_items_document_request_id_foreign` (`document_request_id`),
  KEY `document_request_items_document_type_id_foreign` (`document_type_id`),
  CONSTRAINT `document_request_items_document_request_id_foreign` FOREIGN KEY (`document_request_id`) REFERENCES `document_requests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_request_items_document_type_id_foreign` FOREIGN KEY (`document_type_id`) REFERENCES `document_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_request_items`
--

LOCK TABLES `document_request_items` WRITE;
/*!40000 ALTER TABLE `document_request_items` DISABLE KEYS */;
INSERT INTO `document_request_items` VALUES (9,7,2,1,'A.Y. 2025-2026','4th Grading',75.00,'2026-03-27 04:18:13','2026-03-27 04:18:13'),(10,8,2,1,'A.Y. 2025-2026','2nd Grading',75.00,'2026-03-27 04:19:49','2026-03-27 04:19:49'),(11,9,2,1,'A.Y. 2025-2026','2nd Grading',75.00,'2026-03-27 04:30:52','2026-03-27 04:30:52'),(12,10,2,1,'A.Y. 2025-2026',NULL,75.00,'2026-03-27 04:37:41','2026-03-27 04:37:41'),(13,11,2,1,'A.Y. 2025-2026',NULL,75.00,'2026-03-27 04:37:52','2026-03-27 04:37:52'),(14,12,2,1,'A.Y. 2025-2026',NULL,75.00,'2026-03-27 05:07:22','2026-03-27 05:07:22'),(15,13,2,1,'A.Y. 2025-2026','2nd Grading',75.00,'2026-03-27 05:07:42','2026-03-27 05:07:42'),(16,13,3,1,NULL,NULL,50.00,'2026-03-27 05:07:42','2026-03-27 05:07:42'),(17,14,2,1,'A.Y. 2024-2025','1st Grading',75.00,'2026-03-27 05:22:04','2026-03-27 05:22:04'),(18,15,1,1,'A.Y. 2025-2026',NULL,80.00,'2026-03-27 05:25:52','2026-03-27 05:25:52'),(67,62,3,1,NULL,NULL,50.00,'2026-04-02 09:18:25','2026-04-02 09:18:25'),(68,63,3,1,NULL,NULL,50.00,'2026-04-02 09:24:40','2026-04-02 09:24:40'),(69,64,1,1,'A.Y. 2025-2026','2nd Grading',80.00,'2026-04-02 09:26:41','2026-04-02 09:26:41'),(70,65,1,1,'A.Y. 2025-2026','1st Grading',80.00,'2026-04-02 10:03:17','2026-04-02 10:03:17'),(71,66,1,1,'A.Y. 2025-2026','1st Grading',80.00,'2026-04-02 10:11:09','2026-04-02 10:11:09'),(72,67,1,1,'A.Y. 2025-2026','1st Grading',80.00,'2026-04-02 10:12:22','2026-04-02 10:12:22'),(73,68,2,1,'A.Y. 2025-2026','1st Grading',75.00,'2026-04-02 10:18:17','2026-04-02 10:18:17'),(74,69,4,1,NULL,NULL,70.00,'2026-04-02 10:19:19','2026-04-02 10:19:19'),(75,70,4,1,NULL,NULL,70.00,'2026-04-02 10:27:19','2026-04-02 10:27:19'),(76,71,4,1,NULL,NULL,70.00,'2026-04-02 10:35:12','2026-04-02 10:35:12'),(77,72,4,1,NULL,NULL,70.00,'2026-04-02 10:41:56','2026-04-02 10:41:56'),(78,73,4,1,NULL,NULL,70.00,'2026-04-02 10:47:33','2026-04-02 10:47:33'),(79,74,4,1,NULL,NULL,70.00,'2026-04-02 10:47:49','2026-04-02 10:47:49'),(80,75,3,1,NULL,NULL,50.00,'2026-04-02 10:57:17','2026-04-02 10:57:17'),(81,76,4,1,NULL,NULL,70.00,'2026-04-02 11:03:41','2026-04-02 11:03:41'),(82,77,2,1,'A.Y. 2025-2026','1st Grading',75.00,'2026-04-02 11:04:47','2026-04-02 11:04:47'),(83,78,5,2,NULL,NULL,50.00,'2026-04-02 11:21:01','2026-04-02 11:21:01'),(84,79,4,1,NULL,NULL,70.00,'2026-04-02 12:22:12','2026-04-02 12:22:12'),(85,80,3,1,NULL,NULL,50.00,'2026-04-02 12:31:28','2026-04-02 12:31:28'),(86,80,4,1,NULL,NULL,70.00,'2026-04-02 12:31:28','2026-04-02 12:31:28'),(87,81,3,1,NULL,NULL,50.00,'2026-04-02 12:45:56','2026-04-02 12:45:56'),(88,82,1,1,'A.Y. 2025-2026','1st Grading',80.00,'2026-04-02 12:59:50','2026-04-02 12:59:50'),(94,87,3,1,NULL,NULL,50.00,'2026-04-03 04:00:23','2026-04-03 04:00:23'),(95,88,1,1,'A.Y. 2025-2026','2nd Grading',80.00,'2026-04-03 06:32:23','2026-04-03 06:32:23'),(96,88,2,1,'A.Y. 2025-2026','1st Grading',75.00,'2026-04-03 06:32:23','2026-04-03 06:32:23'),(97,89,4,1,NULL,NULL,70.00,'2026-04-03 06:38:21','2026-04-03 06:38:21'),(98,89,5,1,NULL,NULL,50.00,'2026-04-03 06:38:21','2026-04-03 06:38:21'),(99,90,3,1,NULL,NULL,50.00,'2026-04-03 06:52:32','2026-04-03 06:52:32'),(100,90,5,1,NULL,NULL,50.00,'2026-04-03 06:52:32','2026-04-03 06:52:32'),(101,91,2,1,'A.Y. 2025-2026','1st Grading',75.00,'2026-04-03 06:52:56','2026-04-03 06:52:56'),(102,91,3,1,NULL,NULL,50.00,'2026-04-03 06:52:56','2026-04-03 06:52:56'),(103,92,1,1,'A.Y. 2025-2026','2nd Grading',80.00,'2026-04-03 07:16:59','2026-04-03 07:16:59'),(104,92,4,1,NULL,NULL,70.00,'2026-04-03 07:16:59','2026-04-03 07:16:59'),(105,93,3,1,NULL,NULL,50.00,'2026-04-03 07:17:35','2026-04-03 07:17:35'),(106,94,1,1,'A.Y. 2025-2026','2nd Grading',80.00,'2026-04-03 07:18:03','2026-04-03 07:18:03'),(107,95,1,1,'A.Y. 2025-2026','2nd Grading',80.00,'2026-04-15 19:56:29','2026-04-15 19:56:29'),(108,95,2,1,'A.Y. 2025-2026','3rd Grading',75.00,'2026-04-15 19:56:29','2026-04-15 19:56:29'),(109,96,3,1,NULL,NULL,50.00,'2026-04-16 03:38:54','2026-04-16 03:38:54'),(110,97,2,1,'A.Y. 2025-2026','2nd Grading',75.00,'2026-04-16 20:17:09','2026-04-16 20:17:09'),(111,97,3,1,NULL,NULL,50.00,'2026-04-16 20:17:09','2026-04-16 20:17:09'),(112,97,5,1,NULL,NULL,50.00,'2026-04-16 20:17:09','2026-04-16 20:17:09'),(113,98,3,1,NULL,NULL,50.00,'2026-04-16 20:22:29','2026-04-16 20:22:29'),(114,98,5,2,NULL,NULL,50.00,'2026-04-16 20:22:29','2026-04-16 20:22:29'),(115,99,3,2,NULL,NULL,50.00,'2026-04-16 20:35:33','2026-04-16 20:35:33'),(116,99,5,1,NULL,NULL,50.00,'2026-04-16 20:35:33','2026-04-16 20:35:33');
/*!40000 ALTER TABLE `document_request_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_requests`
--

DROP TABLE IF EXISTS `document_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reference_number` varchar(30) DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `student_number` varchar(50) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `course_program` varchar(255) NOT NULL,
  `year_level` varchar(20) NOT NULL,
  `section` varchar(50) NOT NULL,
  `total_fee` decimal(10,2) NOT NULL,
  `payment_method` enum('gcash','bank_transfer','cash') DEFAULT NULL,
  `status` enum('pending','payment_method_set','payment_uploaded','payment_verified','payment_rejected','processing','ready_for_pickup','received','cancelled') NOT NULL DEFAULT 'pending',
  `remarks` text DEFAULT NULL,
  `claiming_number` varchar(20) DEFAULT NULL,
  `appointment_id` bigint(20) unsigned DEFAULT NULL,
  `processed_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `document_requests_reference_number_unique` (`reference_number`),
  KEY `document_requests_user_id_foreign` (`user_id`),
  KEY `document_requests_processed_by_foreign` (`processed_by`),
  KEY `document_requests_appointment_id_foreign` (`appointment_id`),
  CONSTRAINT `document_requests_appointment_id_foreign` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `document_requests_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `document_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_requests`
--

LOCK TABLES `document_requests` WRITE;
/*!40000 ALTER TABLE `document_requests` DISABLE KEYS */;
INSERT INTO `document_requests` VALUES (7,'DQST-2026-00007',8,'05-9000','Kellychen Aniate','09123456789','STEM','Grade 11','STEM-1A',75.00,NULL,'pending',NULL,NULL,NULL,NULL,'2026-03-27 04:18:13','2026-03-27 04:18:13'),(8,'DQST-2026-00008',8,'05-9000','Kellychen Aniate','09123456789','STEM','Grade 11','STEM-1B',75.00,NULL,'pending',NULL,NULL,NULL,NULL,'2026-03-27 04:19:49','2026-03-27 04:19:49'),(9,'DQST-2026-00009',8,'05-9000','Kellychen Aniate','09123456789','STEM','Grade 11','STEM-1B',75.00,NULL,'pending',NULL,NULL,NULL,NULL,'2026-03-27 04:30:52','2026-03-27 04:30:52'),(10,'DQST-2026-00010',8,'05-9000','Kellychen Aniate','09123456789','STEM','Grade 11','STEM-1B',75.00,NULL,'pending',NULL,NULL,NULL,NULL,'2026-03-27 04:37:41','2026-03-27 04:37:41'),(11,'DQST-2026-00011',8,'05-9000','Kellychen Aniate','09123456789','STEM','Grade 11','STEM-1B',75.00,NULL,'pending',NULL,NULL,NULL,NULL,'2026-03-27 04:37:52','2026-03-27 04:37:52'),(12,'DQST-2026-00012',8,'05-9000','Kellychen Aniate','09123456789','STEM','Grade 11','STEM-1C',75.00,NULL,'pending',NULL,NULL,NULL,NULL,'2026-03-27 05:07:22','2026-03-27 05:07:22'),(13,'DQST-2026-00013',8,'05-9000','Kellychen Aniate','09123456789','STEM','Grade 11','STEM-1C',125.00,'bank_transfer','payment_verified',NULL,NULL,NULL,NULL,'2026-03-27 05:07:41','2026-04-03 06:18:09'),(14,'DQST-2026-00014',8,'05-9000','Kellychen Aniate','09123456789','STEM','Grade 11','STEM-1B',75.00,NULL,'pending',NULL,NULL,NULL,NULL,'2026-03-27 05:22:04','2026-03-27 05:22:04'),(15,'DQST-2026-00015',8,'05-9000','Kellychen Aniate','09123456789','STEM','Grade 11','STEM-1B',80.00,NULL,'pending',NULL,NULL,NULL,NULL,'2026-03-27 05:25:52','2026-03-27 05:25:52'),(62,'DQST-2026-00062',10,'2024-00001','Juan dela Cruz','09171234567','ICT','Grade 12','ICT-2B',50.00,NULL,'pending',NULL,NULL,NULL,NULL,'2026-04-02 09:18:25','2026-04-02 09:18:25'),(63,'DQST-2026-00063',10,'2024-00001','Juan dela Cruz','09171234567','ICT','Grade 12','ICT-2B',50.00,'cash','payment_verified',NULL,NULL,NULL,NULL,'2026-04-02 09:24:40','2026-04-03 07:01:31'),(64,'DQST-2026-00064',10,'2024-00001','Juan dela Cruz','09171234567','ICT','Grade 12','ICT-2B',80.00,NULL,'pending',NULL,NULL,NULL,NULL,'2026-04-02 09:26:41','2026-04-02 09:26:41'),(65,'DQST-2026-00065',10,'2024-00001','Juan dela Cruz','09171234567','ICT','Grade 12','ICT-2B',80.00,NULL,'pending',NULL,NULL,NULL,NULL,'2026-04-02 10:03:17','2026-04-02 10:03:17'),(66,'DQST-2026-00066',10,'2024-00001','Juan dela Cruz','09171234567','ICT','Grade 12','ICT-2B',80.00,'gcash','payment_verified',NULL,NULL,NULL,NULL,'2026-04-02 10:11:09','2026-04-03 06:01:51'),(67,'DQST-2026-00067',10,'2024-00001','Juan dela Cruz','09171234567','ICT','Grade 12','ICT-2B',80.00,NULL,'pending',NULL,NULL,NULL,NULL,'2026-04-02 10:12:22','2026-04-02 10:12:22'),(68,'DQST-2026-00068',10,'2024-00001','Juan dela Cruz','09171234567','ICT','Grade 12','ICT-2A',75.00,NULL,'pending',NULL,NULL,NULL,NULL,'2026-04-02 10:18:17','2026-04-02 10:18:17'),(69,'DQST-2026-00069',10,'2024-00001','Juan dela Cruz','09171234567','ICT','Grade 12','ICT-2A',70.00,NULL,'pending',NULL,NULL,NULL,NULL,'2026-04-02 10:19:19','2026-04-02 10:19:19'),(70,'DQST-2026-00070',10,'2024-00001','Juan dela Cruz','09171234567','ICT','Grade 12','ICT-2B',70.00,'cash','payment_verified',NULL,NULL,NULL,NULL,'2026-04-02 10:27:19','2026-04-03 06:17:45'),(71,'DQST-2026-00071',10,'2024-00001','Juan dela Cruz','09171234567','ICT','Grade 12','ICT-2A',70.00,NULL,'pending',NULL,NULL,NULL,NULL,'2026-04-02 10:35:12','2026-04-02 10:35:12'),(72,'DQST-2026-00072',10,'2024-00001','Juan dela Cruz','09171234567','ICT','Grade 12','ICT-2A',70.00,'gcash','payment_rejected',NULL,NULL,NULL,NULL,'2026-04-02 10:41:56','2026-04-03 06:07:50'),(73,'DQST-2026-00073',10,'2024-00001','Juan dela Cruz','09171234567','ICT','Grade 12','ICT-2A',70.00,NULL,'cancelled',NULL,NULL,NULL,NULL,'2026-04-02 10:47:33','2026-04-02 12:50:53'),(74,'DQST-2026-00074',10,'2024-00001','Juan dela Cruz','09171234567','ICT','Grade 12','ICT-2A',70.00,'cash','payment_verified',NULL,NULL,NULL,NULL,'2026-04-02 10:47:49','2026-04-03 06:17:55'),(75,'DQST-2026-00075',10,'2024-00001','Juan dela Cruz','09171234567','ICT','Grade 12','ICT-2B',50.00,'gcash','processing',NULL,NULL,NULL,NULL,'2026-04-02 10:57:17','2026-04-15 16:59:08'),(76,'DQST-2026-00076',10,'2024-00001','Juan dela Cruz','09171234567','ICT','Grade 12','ICT-2C',70.00,'cash','payment_verified',NULL,NULL,NULL,NULL,'2026-04-02 11:03:41','2026-04-03 06:29:59'),(77,'DQST-2026-00077',10,'2024-00001','Juan dela Cruz','09171234567','ICT','Grade 12','ICT-2C',75.00,'gcash','payment_rejected',NULL,NULL,NULL,NULL,'2026-04-02 11:04:47','2026-04-03 06:07:11'),(78,'DQST-2026-00078',10,'2024-00001','Juan dela Cruz','09171234567','ICT','Grade 12','ICT-2A',100.00,'gcash','payment_rejected',NULL,NULL,NULL,NULL,'2026-04-02 11:21:01','2026-04-03 06:01:44'),(79,'DQST-2026-00079',10,'2024-00001','Juan dela Cruz','09171234567','ICT','Grade 12','ICT-2B',70.00,'cash','payment_verified',NULL,NULL,NULL,NULL,'2026-04-02 12:22:12','2026-04-03 06:17:17'),(80,'DQST-2026-00080',10,'2024-00001','Juan dela Cruz','09171234567','ICT','Grade 12','ICT-2B',120.00,'bank_transfer','payment_rejected',NULL,NULL,NULL,NULL,'2026-04-02 12:31:28','2026-04-03 06:01:32'),(81,'DQST-2026-00081',10,'2024-00001','Juan dela Cruz','09171234567','ICT','Grade 12','ICT-2A',50.00,'cash','payment_verified',NULL,NULL,NULL,NULL,'2026-04-02 12:45:56','2026-04-03 06:51:42'),(82,'DQST-2026-00082',10,'2024-00001','Juan dela Cruz','09171234569','ICT','Grade 12','ICT-2B',80.00,'cash','payment_verified',NULL,NULL,NULL,NULL,'2026-04-02 12:59:50','2026-04-03 05:34:33'),(87,'DQST-2026-00087',15,'05-8532','Kyle Carlo Sufrir Potestad','0943 567 9876','STEM','Grade 12','STEM-2B',50.00,NULL,'pending',NULL,NULL,NULL,NULL,'2026-04-03 04:00:23','2026-04-03 04:00:23'),(88,'DQST-2026-00088',18,'05-3245','Eryza C. Galang','0912 182 1234','ICT','Grade 12','ICT-12A',155.00,'gcash','payment_rejected',NULL,NULL,NULL,NULL,'2026-04-03 06:32:23','2026-04-03 06:33:16'),(89,'DQST-2026-00089',15,'05-8532','Kyle Carlo Sufrir Potestad','0943 567 9876','STEM','Grade 12','STEM-12B',120.00,'gcash','payment_rejected',NULL,NULL,NULL,NULL,'2026-04-03 06:38:21','2026-04-03 06:38:53'),(90,'DQST-2026-00090',18,'05-3245','Eryza C. Galang','0912 182 1234','ICT','Grade 12','ICT-12A',100.00,'gcash','payment_rejected',NULL,NULL,NULL,NULL,'2026-04-03 06:52:32','2026-04-03 06:56:43'),(91,'DQST-2026-00091',18,'05-3245','Eryza C. Galang','0912 182 1234','ICT','Grade 12','ICT-12A',125.00,'bank_transfer','payment_rejected',NULL,NULL,NULL,NULL,'2026-04-03 06:52:56','2026-04-03 06:53:31'),(92,'DQST-2026-00092',19,'05-8959','Karylle Mendiola Viray','09929363432','ICT','Grade 11','ICT-11A',150.00,'gcash','payment_rejected',NULL,NULL,NULL,NULL,'2026-04-03 07:16:59','2026-04-03 07:19:10'),(93,'DQST-2026-00093',19,'05-8959','Karylle Mendiola Viray','09929363432','ICT','Grade 11','ICT-11A',50.00,'bank_transfer','payment_verified',NULL,NULL,NULL,NULL,'2026-04-03 07:17:35','2026-04-03 07:18:58'),(94,'DQST-2026-00094',19,'05-8959','Karylle Mendiola Viray','09929363432','ICT','Grade 11','ICT-11A',80.00,'cash','ready_for_pickup',NULL,'CLM-69E034',NULL,NULL,'2026-04-03 07:18:03','2026-04-15 16:59:25'),(95,'DQST-2026-00095',18,'05-3245','Eryza C. Galang','0912 182 1234','ICT','Grade 12','ICT-12A',155.00,NULL,'pending',NULL,NULL,NULL,NULL,'2026-04-15 19:56:29','2026-04-15 19:56:29'),(96,'DQST-2026-00096',20,'123456789','Evol Cortez Tiglao','0939 124 6857','ICT','Grade 12','ICT-12B',50.00,'gcash','ready_for_pickup',NULL,'CLM-69E0CB',NULL,NULL,'2026-04-16 03:38:54','2026-04-16 03:45:15'),(97,'DQST-2026-00097',21,'05-8713','Nathaniel Del Rosario Deligero','09932526700','ICT','Grade 12','ICT-12A',175.00,'gcash','payment_rejected',NULL,NULL,NULL,NULL,'2026-04-16 20:17:09','2026-04-16 20:18:48'),(98,'DQST-2026-00098',21,'05-8713','Nathaniel Del Rosario Deligero','09932526700','ICT','Grade 12','ICT-12A',150.00,'bank_transfer','ready_for_pickup',NULL,'CLM-69E1B5',NULL,NULL,'2026-04-16 20:22:29','2026-04-16 20:24:00'),(99,'DQST-2026-00099',22,'05-00-2344','Minji M. Potestad','0912 456 5678','HUMSS','Grade 11','HUMSS-11D',150.00,'cash','ready_for_pickup',NULL,'CLM-69E1B9',NULL,NULL,'2026-04-16 20:35:33','2026-04-16 20:38:27');
/*!40000 ALTER TABLE `document_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_types`
--

DROP TABLE IF EXISTS `document_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) NOT NULL,
  `fee` decimal(8,2) NOT NULL,
  `has_school_year` tinyint(1) NOT NULL DEFAULT 0,
  `processing_days` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `document_types_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_types`
--

LOCK TABLES `document_types` WRITE;
/*!40000 ALTER TABLE `document_types` DISABLE KEYS */;
INSERT INTO `document_types` VALUES (1,'Registration Form','REG',80.00,1,3,'Official registration form for the current school year.',1,'2026-04-02 07:18:06','2026-04-02 07:18:06'),(2,'Certificate of Grades','COG',75.00,1,3,'Official record of grades for a specific semester.',1,'2026-04-02 07:18:06','2026-04-02 07:18:06'),(3,'Certificate of Enrollment','COE',50.00,0,2,'Certifies that the student is currently enrolled.',1,'2026-04-02 07:18:06','2026-04-02 07:18:06'),(4,'Transcript of Records','TOR',70.00,0,7,'Complete academic record from Grade 11 to Grade 12.',1,'2026-04-02 07:18:06','2026-04-02 07:18:06'),(5,'Good Moral Certificate','CGMC',50.00,0,2,'Certificate of good moral character issued by the school.',1,'2026-04-02 07:18:06','2026-04-02 07:18:06');
/*!40000 ALTER TABLE `document_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_01_01_000001_create_document_types_table',1),(5,'2025_01_01_000002_create_document_requests_table',1),(6,'2025_01_01_000003_create_document_request_items_table',1),(7,'2025_01_01_000004_create_payment_proofs_table',1),(8,'2025_01_01_000005_create_official_receipts_table',1),(9,'2025_01_01_000006_create_payment_settings_table',1),(10,'2025_01_01_000007_create_time_slots_table',1),(11,'2025_01_01_000008_create_appointments_table',1),(12,'2025_01_01_000009_add_appointment_foreign_key_to_document_requests',1),(13,'2025_01_01_000010_create_status_logs_table',1),(14,'2025_01_01_000011_create_announcements_table',1),(15,'2025_01_01_000012_create_notifications_table',1),(16,'2026_04_03_115043_add_name_fields_to_users_table',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES ('0167cf80-389a-4f9e-a1c1-2f4f3971edeb','App\\Notifications\\SystemNotification','App\\Models\\User',7,'{\"message\":\"Your pickup appointment has been scheduled for May 6, 2026 at 9:00 AM \\u2013 10:00 AM. Please bring your claiming number: CLM-E212A2\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-03 10:57:39\"}','2026-04-03 04:18:43','2026-04-03 02:57:39','2026-04-03 04:18:43'),('01ee4656-b85f-417e-9911-dc2fc76ec3a7','App\\Notifications\\SystemNotification','App\\Models\\User',20,'{\"message\":\"\\u2705 Your payment for request DQST-2026-00096 has been verified. Your documents are now being processed.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-16 11:42:42\"}',NULL,'2026-04-16 03:42:42','2026-04-16 03:42:42'),('02bb40b2-af4c-4830-bf44-20882b39caba','App\\Notifications\\SystemNotification','App\\Models\\User',19,'{\"message\":\"\\u2705 Your payment for request DQST-2026-00093 has been verified. Your documents are now being processed.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-03 15:18:58\"}',NULL,'2026-04-03 07:18:58','2026-04-03 07:18:58'),('06559a33-2b75-4fa4-9082-d1c3ad912447','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"\\u2705 Cash payment method has been deactivated.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/cashier\\/settings\",\"type\":\"system\",\"time\":\"2026-04-16 01:04:12\"}','2026-04-15 17:09:34','2026-04-15 17:04:12','2026-04-15 17:09:34'),('07046ffc-5a68-4b78-96bd-6553ffe54566','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"Request DQST-2026-00073 has been cancelled.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-02 20:50:53\"}','2026-04-02 13:01:18','2026-04-02 12:50:53','2026-04-02 13:01:18'),('07071c0b-3492-4249-92d4-ccebbf15a727','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"\\ud83d\\udccb Your request DQST-2026-00075 is now being processed. We\'ll notify you when it\'s ready for pickup.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-16 00:59:08\"}',NULL,'2026-04-15 16:59:08','2026-04-15 16:59:08'),('0782572d-9065-4950-8140-463f79aa2e6c','App\\Notifications\\SystemNotification','App\\Models\\User',13,'{\"message\":\"Bank Transfer selected as your payment method. Please proceed to upload your proof of payment.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/86\\/upload\",\"type\":\"system\",\"time\":\"2026-04-03 10:46:22\"}',NULL,'2026-04-03 02:46:22','2026-04-03 02:46:22'),('079b4486-0c5f-40e1-aac3-dc0ab905e8b3','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"Over-the-Counter Cash selected as your payment method. Please visit the cashier office to pay in person.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/81\\/upload\",\"type\":\"system\",\"time\":\"2026-04-02 20:46:19\"}','2026-04-02 13:01:18','2026-04-02 12:46:19','2026-04-02 13:01:18'),('07f14a80-f690-446f-b340-e8ff65df2ae2','App\\Notifications\\SystemNotification','App\\Models\\User',19,'{\"message\":\"\\u274c Your payment proof for request DQST-2026-00092 was rejected. Reason: testing. Please re-upload a valid proof.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/92\\/upload\",\"type\":\"system\",\"time\":\"2026-04-03 15:19:10\"}',NULL,'2026-04-03 07:19:10','2026-04-03 07:19:10'),('0e83f403-6ec8-43c5-bec9-185b569f7643','App\\Notifications\\SystemNotification','App\\Models\\User',19,'{\"message\":\"Payment proof uploaded successfully! The cashier will verify it soon.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-03 15:17:50\"}',NULL,'2026-04-03 07:17:50','2026-04-03 07:17:50'),('0f735b35-b715-496d-a45b-f163fabeadb4','App\\Notifications\\SystemNotification','App\\Models\\User',21,'{\"message\":\"Bank Transfer selected as your payment method. Please proceed to upload your proof of payment.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/98\\/upload\",\"type\":\"system\",\"time\":\"2026-04-17 04:22:35\"}','2026-04-16 20:25:01','2026-04-16 20:22:35','2026-04-16 20:25:01'),('1008b2bb-6657-4466-8249-9e45adc005d7','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"\\u2705 Payment settings for Bdo have been updated.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/cashier\\/settings\",\"type\":\"system\",\"time\":\"2026-04-16 01:09:30\"}','2026-04-15 17:09:34','2026-04-15 17:09:30','2026-04-15 17:09:34'),('1178ae9c-6846-4f8b-a7a4-1f1779f8da5a','App\\Notifications\\SystemNotification','App\\Models\\User',13,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00085. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/85\",\"type\":\"system\",\"time\":\"2026-04-03 10:45:51\"}',NULL,'2026-04-03 02:45:51','2026-04-03 02:45:51'),('121ab6c2-ccf5-411f-b740-93358399baa7','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00068. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/68\",\"type\":\"system\"}','2026-04-02 13:01:18','2026-04-02 11:36:34','2026-04-02 13:01:18'),('13d2b92b-4a00-43e1-90b4-d12055df6b28','App\\Notifications\\SystemNotification','App\\Models\\User',18,'{\"message\":\"Payment proof uploaded successfully! The cashier will verify it soon.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-03 14:32:48\"}',NULL,'2026-04-03 06:32:48','2026-04-03 06:32:48'),('15ce7c9c-93b7-4930-ab86-005a09de4e69','App\\Notifications\\SystemNotification','App\\Models\\User',21,'{\"message\":\"Payment proof uploaded successfully! The cashier will verify it soon.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-17 04:22:42\"}','2026-04-16 20:25:01','2026-04-16 20:22:42','2026-04-16 20:25:01'),('167d14d0-bf8f-446e-bd92-d6ff69cb7fe7','App\\Notifications\\SystemNotification','App\\Models\\User',19,'{\"message\":\"Your password has been changed successfully. Please use your new password next time you log in.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/account\",\"type\":\"system\",\"time\":\"2026-04-03 15:16:40\"}','2026-04-03 07:17:05','2026-04-03 07:16:40','2026-04-03 07:17:05'),('1899a7ff-103a-463d-b096-842bc3cb00fb','App\\Notifications\\SystemNotification','App\\Models\\User',7,'{\"message\":\"Your pickup appointment has been scheduled for April 16, 2026 at 8:00 AM \\u2013 9:00 AM. Please bring your claiming number: CLM-E212A2\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-02 21:29:38\"}','2026-04-03 04:18:43','2026-04-02 13:29:38','2026-04-03 04:18:43'),('19cbd86e-e9d2-40cc-a27a-aa97d204c155','App\\Notifications\\SystemNotification','App\\Models\\User',7,'{\"message\":\"Request DQST-2026-00060 has been cancelled.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-02 21:07:02\"}','2026-04-03 04:18:43','2026-04-02 13:07:02','2026-04-03 04:18:43'),('1a962c61-5a7b-4a81-af6f-2c9bb135bb30','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"Test notification for cashier\",\"url\":\"\\/cashier\\/dashboard\",\"type\":\"system\",\"time\":\"2026-04-03 14:37:09\"}','2026-04-03 07:19:28','2026-04-03 06:37:09','2026-04-03 07:19:28'),('1ae1133e-6fc7-44ac-bfa2-49f9d6ddad72','App\\Notifications\\SystemNotification','App\\Models\\User',18,'{\"message\":\"Your profile photo has been updated successfully.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/account\",\"type\":\"system\",\"time\":\"2026-04-16 03:55:48\"}',NULL,'2026-04-15 19:55:48','2026-04-15 19:55:48'),('1d4f5523-2fc4-4edb-aab6-ee0fa22275e5','App\\Notifications\\SystemNotification','App\\Models\\User',18,'{\"message\":\"Payment proof uploaded successfully! The cashier will verify it soon.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-03 14:52:44\"}',NULL,'2026-04-03 06:52:44','2026-04-03 06:52:44'),('224afd48-1f7a-4398-bdbf-8e244f6c876a','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"Over-the-Counter Cash selected as your payment method. Please visit the cashier office to pay in person.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/79\\/upload\",\"type\":\"system\",\"time\":\"2026-04-02 20:22:23\"}','2026-04-02 13:01:18','2026-04-02 12:22:23','2026-04-02 13:01:18'),('22ed9b70-08aa-40dd-9b72-61a120e0109e','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"Over-the-Counter Cash selected as your payment method. Please visit the cashier office to pay in person.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/76\\/upload\",\"type\":\"system\"}','2026-04-02 11:04:19','2026-04-02 11:03:48','2026-04-02 11:04:19'),('2504a9c5-c2fe-4ca5-adc8-25aca7d1fec9','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00069. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/69\",\"type\":\"system\"}','2026-04-02 13:01:18','2026-04-02 11:36:34','2026-04-02 13:01:18'),('2673e919-f23c-4ce0-8b64-8542b9ea69ae','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"Your payment for request DQST-2026-00070 has been verified. Your documents are now being processed.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-03 14:17:45\"}',NULL,'2026-04-03 06:17:45','2026-04-03 06:17:45'),('269f058a-19a2-4966-9734-81cff8b793a0','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"\\u2705 Payment settings for Gcash have been updated.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/cashier\\/settings\",\"type\":\"system\",\"time\":\"2026-04-16 01:03:53\"}','2026-04-15 17:09:34','2026-04-15 17:03:53','2026-04-15 17:09:34'),('2725a062-4923-4521-b257-2a712db772d1','App\\Notifications\\SystemNotification','App\\Models\\User',7,'{\"message\":\"Request DQST-2026-00059 has been cancelled.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-03 10:48:18\"}','2026-04-03 04:18:43','2026-04-03 02:48:18','2026-04-03 04:18:43'),('27608b0d-7d23-4512-a42f-560704f6154d','App\\Notifications\\SystemNotification','App\\Models\\User',21,'{\"message\":\"\\ud83d\\udce6 Your request DQST-2026-00098 is ready for pickup! Your claiming number is: CLM-69E1B5. Please bring this when claiming your documents.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-17 04:24:00\"}','2026-04-16 20:24:17','2026-04-16 20:24:00','2026-04-16 20:24:17'),('2a11f7f1-30eb-4523-b5ac-fb70aca12685','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"\\u2705 Payment settings for Gcash have been updated.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/cashier\\/settings\",\"type\":\"system\",\"time\":\"2026-04-16 01:09:03\"}','2026-04-15 17:09:34','2026-04-15 17:09:03','2026-04-15 17:09:34'),('2a125d71-da41-49fa-90d9-a8ee8125f42b','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"Your payment for request DQST-2026-00074 has been verified. Your documents are now being processed.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-03 14:17:55\"}',NULL,'2026-04-03 06:17:55','2026-04-03 06:17:55'),('2a1541df-8ee0-41fb-b3f3-245fbae2007b','App\\Notifications\\SystemNotification','App\\Models\\User',11,'{\"message\":\"\\u2705 Request DQST-2026-00096 status updated from \'Payment verified\' to \'Processing\'.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/registrar\\/requests\\/96\",\"type\":\"system\",\"time\":\"2026-04-16 11:45:05\"}',NULL,'2026-04-16 03:45:05','2026-04-16 03:45:05'),('2a16ca3d-1b04-49be-8f6a-f96a10e4b90a','App\\Notifications\\SystemNotification','App\\Models\\User',18,'{\"message\":\"GCash selected as your payment method. Please proceed to upload your proof of payment.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/88\\/upload\",\"type\":\"system\",\"time\":\"2026-04-03 14:32:28\"}',NULL,'2026-04-03 06:32:28','2026-04-03 06:32:28'),('2a8ddf47-aedf-492c-92bb-7110357853e5','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00071. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/71\",\"type\":\"system\"}','2026-04-02 13:01:18','2026-04-02 11:36:34','2026-04-02 13:01:18'),('2aafb6ef-40e8-4bb5-b1a3-b67494cc9670','App\\Notifications\\SystemNotification','App\\Models\\User',20,'{\"message\":\"Payment proof uploaded successfully! The cashier will verify it soon.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-16 11:39:35\"}',NULL,'2026-04-16 03:39:35','2026-04-16 03:39:35'),('2c671e28-efef-4f28-8860-84d3fc00c4f8','App\\Notifications\\SystemNotification','App\\Models\\User',22,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00099. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/99\",\"type\":\"system\",\"time\":\"2026-04-17 04:35:33\"}',NULL,'2026-04-16 20:35:33','2026-04-16 20:35:33'),('2ca8bf9e-8122-43b6-936b-1567c226d9bf','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"GCash selected as your payment method. Please proceed to upload your proof of payment.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/77\\/upload\",\"type\":\"system\"}','2026-04-02 13:01:18','2026-04-02 11:04:56','2026-04-02 13:01:18'),('2e989371-a880-4c4a-82bc-bab0ddf6e758','App\\Notifications\\SystemNotification','App\\Models\\User',21,'{\"message\":\"GCash selected as your payment method. Please proceed to upload your proof of payment.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/97\\/upload\",\"type\":\"system\",\"time\":\"2026-04-17 04:17:36\"}','2026-04-16 20:25:01','2026-04-16 20:17:36','2026-04-16 20:25:01'),('2ea59e8d-4c9c-42d7-a5fa-ed262e018cad','App\\Notifications\\SystemNotification','App\\Models\\User',7,'{\"message\":\"Over-the-Counter Cash selected as your payment method. Please visit the cashier office to pay in person.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/83\\/upload\",\"type\":\"system\",\"time\":\"2026-04-02 21:03:53\"}','2026-04-03 04:18:43','2026-04-02 13:03:53','2026-04-03 04:18:43'),('2f799068-3b05-40f3-91e8-813e9d77ac48','App\\Notifications\\SystemNotification','App\\Models\\User',13,'{\"message\":\"GCash selected as your payment method. Please proceed to upload your proof of payment.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/84\\/upload\",\"type\":\"system\",\"time\":\"2026-04-03 10:45:15\"}',NULL,'2026-04-03 02:45:15','2026-04-03 02:45:15'),('330848cd-0a36-4b29-be52-6e7e93f42437','App\\Notifications\\SystemNotification','App\\Models\\User',21,'{\"message\":\"\\ud83d\\udccb Your request DQST-2026-00098 is now being processed. We\'ll notify you when it\'s ready for pickup.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-17 04:23:47\"}','2026-04-16 20:25:01','2026-04-16 20:23:47','2026-04-16 20:25:01'),('3661d2c0-0bb0-45fa-8664-f8dd1240b0b2','App\\Notifications\\SystemNotification','App\\Models\\User',11,'{\"message\":\"\\u2705 Request DQST-2026-00099 status updated from \'Processing\' to \'Ready for pickup\'.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/registrar\\/requests\\/99\",\"type\":\"system\",\"time\":\"2026-04-17 04:38:27\"}',NULL,'2026-04-16 20:38:27','2026-04-16 20:38:27'),('373b80ed-cfb2-43d6-80f8-5001eaf47bc8','App\\Notifications\\SystemNotification','App\\Models\\User',15,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00089. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/89\",\"type\":\"system\",\"time\":\"2026-04-03 14:38:21\"}',NULL,'2026-04-03 06:38:21','2026-04-03 06:38:21'),('3d307ed2-e012-475c-acf1-901e942d21b5','App\\Notifications\\SystemNotification','App\\Models\\User',11,'{\"message\":\"\\u2705 Announcement has been updated successfully.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/registrar\\/dashboard\",\"type\":\"system\",\"time\":\"2026-04-16 11:43:57\"}',NULL,'2026-04-16 03:43:57','2026-04-16 03:43:57'),('3e5603b7-cf47-43da-9357-0c78e624336c','App\\Notifications\\SystemNotification','App\\Models\\User',13,'{\"message\":\"Your profile information has been updated successfully.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/account\",\"type\":\"system\",\"time\":\"2026-04-03 10:47:10\"}',NULL,'2026-04-03 02:47:10','2026-04-03 02:47:10'),('40ab287f-46ee-4fb6-8b43-4ed987eecd6d','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"\\u2705 Payment settings for Bdo have been updated.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/cashier\\/settings\",\"type\":\"system\",\"time\":\"2026-04-16 01:16:47\"}',NULL,'2026-04-15 17:16:47','2026-04-15 17:16:47'),('40d4a388-8d21-489f-8403-d78f6c4a0ede','App\\Notifications\\SystemNotification','App\\Models\\User',15,'{\"message\":\"Payment proof uploaded successfully! The cashier will verify it soon.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-03 14:38:32\"}',NULL,'2026-04-03 06:38:32','2026-04-03 06:38:32'),('41fdad34-007e-4689-a4bb-d91b7095aafb','App\\Notifications\\SystemNotification','App\\Models\\User',22,'{\"message\":\"\\u2705 Your cash payment for request DQST-2026-00099 has been recorded. Your documents are now being processed.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-17 04:37:38\"}',NULL,'2026-04-16 20:37:38','2026-04-16 20:37:38'),('4288ed15-6528-4b26-9010-585e12cdfdfe','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00075. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/75\",\"type\":\"system\"}','2026-04-02 10:57:26','2026-04-02 10:57:17','2026-04-02 10:57:26'),('430047d1-3901-4025-a139-16f54b2d4b0d','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"\\u2705 You verified payment for request DQST-2026-00093 - Amount: \\u20b150.00\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/cashier\\/payments\\/93\",\"type\":\"system\",\"time\":\"2026-04-03 15:18:58\"}','2026-04-03 07:19:28','2026-04-03 07:18:58','2026-04-03 07:19:28'),('467b9822-925f-45c6-ad07-d3ac0ff7ba4f','App\\Notifications\\SystemNotification','App\\Models\\User',11,'{\"message\":\"\\u2705 Request DQST-2026-00075 status updated from \'Payment verified\' to \'Processing\'.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/registrar\\/requests\\/75\",\"type\":\"system\",\"time\":\"2026-04-16 00:59:08\"}',NULL,'2026-04-15 16:59:08','2026-04-15 16:59:08'),('48414553-5163-4122-a666-b144a0279bca','App\\Notifications\\SystemNotification','App\\Models\\User',19,'{\"message\":\"\\ud83d\\udccb Your request DQST-2026-00094 is now being processed. We\'ll notify you when it\'s ready for pickup.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-16 00:57:50\"}',NULL,'2026-04-15 16:57:50','2026-04-15 16:57:50'),('494e53e7-e989-4366-a3fb-e176d51ab50a','App\\Notifications\\SystemNotification','App\\Models\\User',15,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00087. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/87\",\"type\":\"system\",\"time\":\"2026-04-03 12:00:23\"}',NULL,'2026-04-03 04:00:23','2026-04-03 04:00:23'),('4b3940ed-bae8-4d22-ac5a-1aee766c7366','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"Your profile photo has been updated successfully.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/account\",\"type\":\"system\",\"time\":\"2026-04-02 20:59:23\"}','2026-04-02 13:01:18','2026-04-02 12:59:23','2026-04-02 13:01:18'),('4ce34652-5edf-419b-9fdf-ff9bac2b14d8','App\\Notifications\\SystemNotification','App\\Models\\User',11,'{\"message\":\"\\u2705 Request DQST-2026-00098 status updated from \'Processing\' to \'Ready for pickup\'.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/registrar\\/requests\\/98\",\"type\":\"system\",\"time\":\"2026-04-17 04:24:00\"}',NULL,'2026-04-16 20:24:00','2026-04-16 20:24:00'),('4da1f01e-53cf-45e0-82e3-a56bf54cc64d','App\\Notifications\\SystemNotification','App\\Models\\User',7,'{\"message\":\"Your profile photo has been updated successfully.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/account\",\"type\":\"system\",\"time\":\"2026-04-02 21:02:20\"}','2026-04-03 04:18:43','2026-04-02 13:02:20','2026-04-03 04:18:43'),('4dc7aa62-f9ae-48ac-8f17-98cc91b8a42e','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"\\u2705 Your cash payment for request DQST-2026-00063 has been recorded. Your documents are now being processed.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-03 15:01:31\"}',NULL,'2026-04-03 07:01:31','2026-04-03 07:01:31'),('4ecebb35-2344-4a79-9626-d81dce1afd45','App\\Notifications\\SystemNotification','App\\Models\\User',18,'{\"message\":\"\\u274c Your payment proof for request DQST-2026-00091 was rejected. Reason: adhlSFNwlorjqwp TESTING. Please re-upload a valid proof.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/91\\/upload\",\"type\":\"system\",\"time\":\"2026-04-03 14:53:31\"}',NULL,'2026-04-03 06:53:31','2026-04-03 06:53:31'),('50b7763b-d846-4d81-9a09-76feffa21276','App\\Notifications\\SystemNotification','App\\Models\\User',22,'{\"message\":\"Over-the-Counter Cash selected as your payment method. Please visit the cashier office to pay in person.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/99\\/upload\",\"type\":\"system\",\"time\":\"2026-04-17 04:36:41\"}',NULL,'2026-04-16 20:36:41','2026-04-16 20:36:41'),('51835218-b4c5-4159-8e2c-38984d81e1c3','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00072. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/72\",\"type\":\"system\"}','2026-04-02 10:47:19','2026-04-02 10:41:56','2026-04-02 10:47:19'),('546cf830-6cb6-4026-a1aa-b511c24b134d','App\\Notifications\\SystemNotification','App\\Models\\User',19,'{\"message\":\"GCash selected as your payment method. Please proceed to upload your proof of payment.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/92\\/upload\",\"type\":\"system\",\"time\":\"2026-04-03 15:17:11\"}',NULL,'2026-04-03 07:17:11','2026-04-03 07:17:11'),('575e1719-e9f6-4c28-a388-84a34acb5d45','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"Test notification for cashier\",\"url\":\"\\/cashier\\/dashboard\",\"type\":\"system\",\"time\":\"2026-04-03 14:47:36\"}','2026-04-03 06:51:33','2026-04-03 06:47:36','2026-04-03 06:51:33'),('588d1aac-2a7c-11f1-9739-1c36bb2be4fa','App\\Notifications\\RequestSubmittedNotification','App\\Models\\User',4,'{\"title\":\"Request Submitted\",\"detail\":\"DQST-2026-00001 has been received. Awaiting payment.\",\"type\":\"request\"}','2026-03-28 00:02:40','2026-03-28 08:01:37','2026-03-28 00:02:40'),('58971c8c-2a7c-11f1-9739-1c36bb2be4fa','App\\Notifications\\PaymentVerifiedNotification','App\\Models\\User',4,'{\"title\":\"Payment Verified\",\"detail\":\"Your payment for DQST-2026-00001 has been confirmed.\",\"type\":\"payment\"}','2026-03-28 00:02:52','2026-03-28 08:01:37','2026-03-28 00:02:52'),('589965f0-2a7c-11f1-9739-1c36bb2be4fa','App\\Notifications\\PaymentRejectedNotification','App\\Models\\User',4,'{\"title\":\"Payment Rejected\",\"detail\":\"Your proof of payment was rejected. Please re-upload a clearer image.\",\"type\":\"payment\"}','2026-03-28 00:02:50','2026-03-28 08:01:37','2026-03-28 00:02:50'),('589af352-2a7c-11f1-9739-1c36bb2be4fa','App\\Notifications\\RequestProcessingNotification','App\\Models\\User',4,'{\"title\":\"Request Processing\",\"detail\":\"DQST-2026-00001 is now being prepared by the registrar.\",\"type\":\"request\"}','2026-03-28 00:03:41','2026-03-28 08:01:37','2026-03-28 00:03:41'),('589c42de-2a7c-11f1-9739-1c36bb2be4fa','App\\Notifications\\ReadyForPickupNotification','App\\Models\\User',4,'{\"title\":\"Ready for Pickup\",\"detail\":\"Your documents are ready. Claiming number: CLM-A4KZ29\",\"type\":\"request\"}','2026-03-28 00:03:41','2026-03-28 08:01:37','2026-03-28 00:03:41'),('589e4188-2a7c-11f1-9739-1c36bb2be4fa','App\\Notifications\\AppointmentConfirmedNotification','App\\Models\\User',4,'{\"title\":\"Appointment Confirmed\",\"detail\":\"Scheduled for April 5, 2026 — 9:00 AM to 10:00 AM.\",\"type\":\"appointment\"}','2026-03-28 00:03:41','2026-03-28 08:01:37','2026-03-28 00:03:41'),('58a01684-2a7c-11f1-9739-1c36bb2be4fa','App\\Notifications\\AppointmentReminderNotification','App\\Models\\User',4,'{\"title\":\"Appointment Reminder\",\"detail\":\"Reminder: Your pickup appointment is tomorrow, April 5, 2026 at 9:00 AM.\",\"type\":\"appointment\"}','2026-03-28 00:03:41','2026-03-28 08:01:37','2026-03-28 00:03:41'),('5994a96f-14fc-4e49-9bb1-0b0299afa676','App\\Notifications\\SystemNotification','App\\Models\\User',15,'{\"message\":\"\\u274c Your payment proof for request DQST-2026-00089 was rejected. Reason: awrwehfASLFJS;ODFJPA. Please re-upload a valid proof.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/89\\/upload\",\"type\":\"system\",\"time\":\"2026-04-03 14:38:53\"}',NULL,'2026-04-03 06:38:53','2026-04-03 06:38:53'),('5b1b235e-dd2b-4ddf-ba43-b5844d49db72','App\\Notifications\\SystemNotification','App\\Models\\User',7,'{\"message\":\"Your pickup appointment has been scheduled for April 7, 2026 at 9:00 AM \\u2013 10:00 AM. Please bring your claiming number: CLM-E212A2\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-03 10:58:42\"}','2026-04-03 04:18:43','2026-04-03 02:58:42','2026-04-03 04:18:43'),('5b6f9321-20af-44fb-a5de-63ccd826e0fd','App\\Notifications\\SystemNotification','App\\Models\\User',13,'{\"message\":\"Your password has been changed successfully. Please use your new password next time you log in.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/account\",\"type\":\"system\",\"time\":\"2026-04-03 10:47:29\"}',NULL,'2026-04-03 02:47:29','2026-04-03 02:47:29'),('5d299894-4131-4a5b-9979-04e4502ed62a','App\\Notifications\\SystemNotification','App\\Models\\User',13,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00084. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/84\",\"type\":\"system\",\"time\":\"2026-04-03 10:44:55\"}',NULL,'2026-04-03 02:44:55','2026-04-03 02:44:55'),('5dc1de67-8cb8-4ae9-9e5d-b3b56e9e7fc9','App\\Notifications\\SystemNotification','App\\Models\\User',13,'{\"message\":\"Over-the-Counter Cash selected as your payment method. Please visit the cashier office to pay in person.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/85\\/upload\",\"type\":\"system\",\"time\":\"2026-04-03 10:45:56\"}',NULL,'2026-04-03 02:45:56','2026-04-03 02:45:56'),('61cf23aa-8ec9-4a96-95e7-1a95b7579106','App\\Notifications\\SystemNotification','App\\Models\\User',20,'{\"message\":\"Your pickup appointment has been scheduled for April 20, 2026 at 1:00 PM \\u2013 2:00 PM. Please bring your claiming number: CLM-69E0CB\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-16 11:46:55\"}',NULL,'2026-04-16 03:46:55','2026-04-16 03:46:55'),('61f69c62-083d-47cd-8300-0723a071cc51','App\\Notifications\\SystemNotification','App\\Models\\User',11,'{\"message\":\"\\u2705 Request DQST-2026-00096 status updated from \'Processing\' to \'Ready for pickup\'.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/registrar\\/requests\\/96\",\"type\":\"system\",\"time\":\"2026-04-16 11:45:15\"}',NULL,'2026-04-16 03:45:15','2026-04-16 03:45:15'),('62ed95ca-c22a-4919-9e02-3133e57aa165','App\\Notifications\\SystemNotification','App\\Models\\User',21,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00097. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/97\",\"type\":\"system\",\"time\":\"2026-04-17 04:17:09\"}','2026-04-16 20:25:01','2026-04-16 20:17:09','2026-04-16 20:25:01'),('645d3fc1-afc4-4655-9fc6-20d12bbe085e','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"\\u2705 Payment settings for Bdo have been updated.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/cashier\\/settings\",\"type\":\"system\",\"time\":\"2026-04-16 01:16:44\"}',NULL,'2026-04-15 17:16:44','2026-04-15 17:16:44'),('66e15e2b-791f-4e9e-adb8-038a18bd673e','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"GCash selected as your payment method. Please proceed to upload your proof of payment.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/78\\/upload\",\"type\":\"system\"}','2026-04-02 13:01:18','2026-04-02 11:21:26','2026-04-02 13:01:18'),('6774dbbe-4482-4a10-8083-c978c43e8320','App\\Notifications\\SystemNotification','App\\Models\\User',8,'{\"message\":\"Your payment for request DQST-2026-00013 has been verified. Your documents are now being processed.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-03 14:18:09\"}',NULL,'2026-04-03 06:18:09','2026-04-03 06:18:09'),('6a6328a9-1289-4d98-97c0-1214fd81f92f','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00080. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/80\",\"type\":\"system\",\"time\":\"2026-04-02 20:31:28\"}','2026-04-02 13:01:18','2026-04-02 12:31:28','2026-04-02 13:01:18'),('6bb01a99-c43a-4f5b-b669-c1537c210969','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"Payment proof uploaded successfully! The cashier will verify it soon.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\"}','2026-04-02 13:01:18','2026-04-02 11:05:25','2026-04-02 13:01:18'),('6c2e8686-be98-4bae-b63d-d54bcefabcb5','App\\Notifications\\SystemNotification','App\\Models\\User',11,'{\"message\":\"\\u2705 Announcement has been updated successfully.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/registrar\\/dashboard\",\"type\":\"system\",\"time\":\"2026-04-15 23:55:58\"}',NULL,'2026-04-15 15:55:58','2026-04-15 15:55:58'),('70ed2d1c-d121-4ca5-afae-9e71b4d9fee1','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"\\u2705 Payment settings for Gcash have been updated.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/cashier\\/settings\",\"type\":\"system\",\"time\":\"2026-04-16 01:03:55\"}','2026-04-15 17:09:34','2026-04-15 17:03:55','2026-04-15 17:09:34'),('710cef79-6ebf-462a-ad68-c1d813386ade','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"\\u274c You rejected payment for request DQST-2026-00097 - Reason: kulang baliw\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/cashier\\/payments\\/97\",\"type\":\"system\",\"time\":\"2026-04-17 04:18:48\"}',NULL,'2026-04-16 20:18:48','2026-04-16 20:18:48'),('718479a0-f56c-49ce-99ce-a1b8fa1d92ae','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"\\u274c You rejected payment for request DQST-2026-00090 - Reason: testing\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/cashier\\/payments\\/90\",\"type\":\"system\",\"time\":\"2026-04-03 14:56:43\"}','2026-04-03 07:19:28','2026-04-03 06:56:43','2026-04-03 07:19:28'),('72ba945d-152a-4889-b7eb-3766f497f89c','App\\Notifications\\SystemNotification','App\\Models\\User',7,'{\"message\":\"Your appointment scheduled for May 6, 2026 at 9:00 AM \\u2013 10:00 AM has been cancelled. You can book a new appointment anytime.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-03 10:58:19\"}','2026-04-03 04:18:43','2026-04-03 02:58:19','2026-04-03 04:18:43'),('7376a2d9-771b-4ff5-a44f-36189ed77c46','App\\Notifications\\SystemNotification','App\\Models\\User',13,'{\"message\":\"Payment proof uploaded successfully! The cashier will verify it soon.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-03 10:45:33\"}',NULL,'2026-04-03 02:45:33','2026-04-03 02:45:33'),('73b0fa68-277f-4ed8-83cd-2f0cebb49665','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"\\u2705 Your cash payment for request DQST-2026-00076 has been recorded. Your documents are now being processed.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-03 14:29:59\"}',NULL,'2026-04-03 06:29:59','2026-04-03 06:29:59'),('783bf8ae-68e8-4828-8aee-2de8c3da47dc','App\\Notifications\\SystemNotification','App\\Models\\User',20,'{\"message\":\"Your profile photo has been updated successfully.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/account\",\"type\":\"system\",\"time\":\"2026-04-16 11:40:31\"}',NULL,'2026-04-16 03:40:31','2026-04-16 03:40:31'),('78913b6e-99bf-487e-aaba-4a80a08d29fe','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"\\u2705 Payment settings for Gcash have been updated.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/cashier\\/settings\",\"type\":\"system\",\"time\":\"2026-04-16 01:16:20\"}',NULL,'2026-04-15 17:16:20','2026-04-15 17:16:20'),('7b23ba59-5750-4519-b368-e796aa988d62','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"\\ud83d\\udcb0 You marked cash payment as paid for request DQST-2026-00063 - Amount: \\u20b150.00\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/cashier\\/payments\\/63\",\"type\":\"system\",\"time\":\"2026-04-03 15:01:31\"}','2026-04-03 07:19:28','2026-04-03 07:01:31','2026-04-03 07:19:28'),('7bd882a5-9a83-4755-8c89-fded75c992dd','App\\Notifications\\SystemNotification','App\\Models\\User',21,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00098. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/98\",\"type\":\"system\",\"time\":\"2026-04-17 04:22:29\"}','2026-04-16 20:25:01','2026-04-16 20:22:29','2026-04-16 20:25:01'),('7db5cd7b-85eb-4eff-a4cb-5784371ab5ea','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"\\ud83d\\udcb0 You marked cash payment as paid for request DQST-2026-00094 - Amount: \\u20b180.00\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/cashier\\/payments\\/94\",\"type\":\"system\",\"time\":\"2026-04-03 15:18:47\"}','2026-04-03 07:19:28','2026-04-03 07:18:47','2026-04-03 07:19:28'),('7e1af884-eabd-49fa-afaa-4957596b8864','App\\Notifications\\SystemNotification','App\\Models\\User',19,'{\"message\":\"Your profile photo has been updated successfully.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/account\",\"type\":\"system\",\"time\":\"2026-04-03 15:16:11\"}','2026-04-03 07:17:05','2026-04-03 07:16:11','2026-04-03 07:17:05'),('7ee59c9b-c2bf-42dc-9cae-a86577aa0475','App\\Notifications\\SystemNotification','App\\Models\\User',18,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00090. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/90\",\"type\":\"system\",\"time\":\"2026-04-03 14:52:32\"}',NULL,'2026-04-03 06:52:32','2026-04-03 06:52:32'),('827decf1-1837-44ff-9e33-d12a544b17e7','App\\Notifications\\SystemNotification','App\\Models\\User',19,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00092. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/92\",\"type\":\"system\",\"time\":\"2026-04-03 15:16:59\"}','2026-04-03 07:17:05','2026-04-03 07:16:59','2026-04-03 07:17:05'),('845ab11f-3385-4adc-8420-bb3099f0dc79','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00078. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/78\",\"type\":\"system\"}','2026-04-02 13:01:18','2026-04-02 11:21:02','2026-04-02 13:01:18'),('890ee865-79f2-4c8f-8959-dde561a3f31a','App\\Notifications\\SystemNotification','App\\Models\\User',13,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00086. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/86\",\"type\":\"system\",\"time\":\"2026-04-03 10:46:16\"}',NULL,'2026-04-03 02:46:16','2026-04-03 02:46:16'),('893959f3-2b52-4ba3-81a3-b5d50331d31c','App\\Notifications\\SystemNotification','App\\Models\\User',20,'{\"message\":\"Your profile photo has been updated successfully.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/account\",\"type\":\"system\",\"time\":\"2026-04-16 11:40:56\"}',NULL,'2026-04-16 03:40:56','2026-04-16 03:40:56'),('89c95cda-b98b-47b6-ab26-961027c6bc64','App\\Notifications\\SystemNotification','App\\Models\\User',19,'{\"message\":\"Your profile information has been updated successfully.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/account\",\"type\":\"system\",\"time\":\"2026-04-03 15:16:23\"}','2026-04-03 07:17:05','2026-04-03 07:16:23','2026-04-03 07:17:05'),('8a618fa7-3494-46f0-a4be-fa666c574911','App\\Notifications\\SystemNotification','App\\Models\\User',20,'{\"message\":\"GCash selected as your payment method. Please proceed to upload your proof of payment.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/96\\/upload\",\"type\":\"system\",\"time\":\"2026-04-16 11:39:11\"}',NULL,'2026-04-16 03:39:11','2026-04-16 03:39:11'),('8aebcc36-75ba-482b-b78e-0e4d1cb5def6','App\\Notifications\\SystemNotification','App\\Models\\User',18,'{\"message\":\"Bank Transfer selected as your payment method. Please proceed to upload your proof of payment.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/91\\/upload\",\"type\":\"system\",\"time\":\"2026-04-03 14:53:01\"}',NULL,'2026-04-03 06:53:01','2026-04-03 06:53:01'),('8c2f3fc6-4bfd-4d36-bbdc-f2a07bbe8e53','App\\Notifications\\SystemNotification','App\\Models\\User',19,'{\"message\":\"Over-the-Counter Cash selected as your payment method. Please visit the cashier office to pay in person.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/94\\/upload\",\"type\":\"system\",\"time\":\"2026-04-03 15:18:11\"}',NULL,'2026-04-03 07:18:11','2026-04-03 07:18:11'),('8d1a3eaf-8b9e-40aa-b873-138ace483c31','App\\Notifications\\SystemNotification','App\\Models\\User',18,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00091. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/91\",\"type\":\"system\",\"time\":\"2026-04-03 14:52:56\"}',NULL,'2026-04-03 06:52:56','2026-04-03 06:52:56'),('9075d50f-4ce2-4127-97b8-17ab11681d42','App\\Notifications\\SystemNotification','App\\Models\\User',11,'{\"message\":\"\\u2705 Request DQST-2026-00098 status updated from \'Payment verified\' to \'Processing\'.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/registrar\\/requests\\/98\",\"type\":\"system\",\"time\":\"2026-04-17 04:23:47\"}',NULL,'2026-04-16 20:23:47','2026-04-16 20:23:47'),('90c15966-4690-435f-b037-573258a73369','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"GCash selected as your payment method. Please proceed to upload your proof of payment.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/78\\/upload\",\"type\":\"system\"}','2026-04-02 13:01:18','2026-04-02 11:30:03','2026-04-02 13:01:18'),('925cbb25-03b2-45ad-b2d4-0a820c17bfd2','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"\\u2705 Payment settings for Gcash have been updated.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/cashier\\/settings\",\"type\":\"system\",\"time\":\"2026-04-16 01:04:26\"}','2026-04-15 17:09:34','2026-04-15 17:04:26','2026-04-15 17:09:34'),('93278d64-e4de-4ce5-9f82-1f5165974972','App\\Notifications\\SystemNotification','App\\Models\\User',19,'{\"message\":\"Bank Transfer selected as your payment method. Please proceed to upload your proof of payment.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/93\\/upload\",\"type\":\"system\",\"time\":\"2026-04-03 15:17:40\"}',NULL,'2026-04-03 07:17:40','2026-04-03 07:17:40'),('93f0b93f-cea3-4d46-856e-d3dfbb214a19','App\\Notifications\\SystemNotification','App\\Models\\User',18,'{\"message\":\"\\u274c Your payment proof for request DQST-2026-00088 was rejected. Reason: not sufficient. Please re-upload a valid proof.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/88\\/upload\",\"type\":\"system\",\"time\":\"2026-04-03 14:33:16\"}',NULL,'2026-04-03 06:33:16','2026-04-03 06:33:16'),('947a0b60-82b0-458a-a560-150bda45d9df','App\\Notifications\\SystemNotification','App\\Models\\User',19,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00093. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/93\",\"type\":\"system\",\"time\":\"2026-04-03 15:17:35\"}',NULL,'2026-04-03 07:17:35','2026-04-03 07:17:35'),('94d7b50c-a585-4bba-8d95-e8e945207680','App\\Notifications\\SystemNotification','App\\Models\\User',19,'{\"message\":\"Payment proof uploaded successfully! The cashier will verify it soon.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-03 15:17:23\"}',NULL,'2026-04-03 07:17:23','2026-04-03 07:17:23'),('956fb5b4-1d6d-41b8-b0c4-5fe7b0670e3c','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"Payment proof uploaded successfully! The cashier will verify it soon.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\"}','2026-04-02 13:01:18','2026-04-02 11:05:52','2026-04-02 13:01:18'),('968e5b06-7cbc-4d01-b505-d1721ffc0b42','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"\\u2705 You verified payment for request DQST-2026-00098 - Amount: \\u20b1150.00\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/cashier\\/payments\\/98\",\"type\":\"system\",\"time\":\"2026-04-17 04:23:08\"}',NULL,'2026-04-16 20:23:08','2026-04-16 20:23:08'),('96bc18b8-8fbf-4315-9898-1af88b9f3a65','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"\\u2705 Payment settings for Gcash have been updated.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/cashier\\/settings\",\"type\":\"system\",\"time\":\"2026-04-16 01:16:21\"}',NULL,'2026-04-15 17:16:21','2026-04-15 17:16:21'),('981f1f6c-2297-4174-8735-22d5fe7d2e80','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"\\u2705 Payment settings for Bdo have been updated.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/cashier\\/settings\",\"type\":\"system\",\"time\":\"2026-04-16 01:04:02\"}','2026-04-15 17:09:34','2026-04-15 17:04:02','2026-04-15 17:09:34'),('9850b046-3b44-4f13-99d2-248b3ed55a2a','App\\Notifications\\SystemNotification','App\\Models\\User',19,'{\"message\":\"\\ud83d\\udce6 Your request DQST-2026-00094 is ready for pickup! Your claiming number is: CLM-69E034. Please bring this when claiming your documents.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-16 00:59:25\"}',NULL,'2026-04-15 16:59:25','2026-04-15 16:59:25'),('990f96cf-58c5-4f57-9771-54ad58958853','App\\Notifications\\SystemNotification','App\\Models\\User',19,'{\"message\":\"\\u2705 Your cash payment for request DQST-2026-00094 has been recorded. Your documents are now being processed.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-03 15:18:47\"}',NULL,'2026-04-03 07:18:47','2026-04-03 07:18:47'),('9cfe457d-2a7f-4f27-b9cf-4b36291e7b7f','App\\Notifications\\SystemNotification','App\\Models\\User',7,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00083. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/83\",\"type\":\"system\",\"time\":\"2026-04-02 21:03:44\"}','2026-04-03 04:18:43','2026-04-02 13:03:44','2026-04-03 04:18:43'),('9e640903-6c0a-4030-b82d-ea4170058585','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00067. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/67\",\"type\":\"system\"}','2026-04-02 13:01:18','2026-04-02 11:36:34','2026-04-02 13:01:18'),('9f638941-e2a0-4e5d-aa1d-82f339693d17','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"\\u2705 Payment settings for Cash have been updated.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/cashier\\/settings\",\"type\":\"system\",\"time\":\"2026-04-16 01:09:47\"}',NULL,'2026-04-15 17:09:47','2026-04-15 17:09:47'),('9ffdb5d3-b2f4-43e1-8614-a5f3aec75cb6','App\\Notifications\\SystemNotification','App\\Models\\User',11,'{\"message\":\"\\u2705 Announcement has been published.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/registrar\\/dashboard\",\"type\":\"system\",\"time\":\"2026-04-15 23:42:36\"}',NULL,'2026-04-15 15:42:36','2026-04-15 15:42:36'),('a0ccda6d-48f3-4ef7-9378-d479ebc7aeca','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"\\ud83d\\udcb0 You marked cash payment as paid for request DQST-2026-00099 - Amount: \\u20b1150.00\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/cashier\\/payments\\/99\",\"type\":\"system\",\"time\":\"2026-04-17 04:37:38\"}',NULL,'2026-04-16 20:37:38','2026-04-16 20:37:38'),('a1c547ef-723a-4144-99ba-62c774914844','App\\Notifications\\SystemNotification','App\\Models\\User',19,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00094. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/94\",\"type\":\"system\",\"time\":\"2026-04-03 15:18:03\"}',NULL,'2026-04-03 07:18:03','2026-04-03 07:18:03'),('a6e72fc4-a342-4fb1-b892-1a6a124dcb4c','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00073. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/73\",\"type\":\"system\"}','2026-04-02 10:50:40','2026-04-02 10:47:33','2026-04-02 10:50:40'),('a9a16bd0-4842-4baa-a7f8-e7118accbbf0','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"Bank Transfer selected as your payment method. Please proceed to upload your proof of payment.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/80\\/upload\",\"type\":\"system\",\"time\":\"2026-04-02 20:36:52\"}','2026-04-02 13:01:18','2026-04-02 12:36:52','2026-04-02 13:01:18'),('ac023551-a109-4604-a7cd-89b87e0809d6','App\\Notifications\\SystemNotification','App\\Models\\User',18,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00088. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/88\",\"type\":\"system\",\"time\":\"2026-04-03 14:32:23\"}',NULL,'2026-04-03 06:32:23','2026-04-03 06:32:23'),('ad486a53-bc68-4a38-8ebc-d471173f58cc','App\\Notifications\\SystemNotification','App\\Models\\User',13,'{\"message\":\"Your profile photo has been updated successfully.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/account\",\"type\":\"system\",\"time\":\"2026-04-03 10:47:39\"}',NULL,'2026-04-03 02:47:39','2026-04-03 02:47:39'),('ad5f2ccc-53a5-42c3-adda-1fef187dd009','App\\Notifications\\SystemNotification','App\\Models\\User',13,'{\"message\":\"Payment proof uploaded successfully! The cashier will verify it soon.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-03 10:46:32\"}',NULL,'2026-04-03 02:46:32','2026-04-03 02:46:32'),('ae386276-42fb-4ad7-966a-1378da6d653c','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"\\u2705 Cash payment method has been activated.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/cashier\\/settings\",\"type\":\"system\",\"time\":\"2026-04-16 01:04:18\"}','2026-04-15 17:09:34','2026-04-15 17:04:18','2026-04-15 17:09:34'),('ae417d96-7921-489d-bee1-488ebeb0070a','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"\\u2705 Payment settings for Gcash have been updated.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/cashier\\/settings\",\"type\":\"system\",\"time\":\"2026-04-16 01:03:54\"}','2026-04-15 17:09:34','2026-04-15 17:03:54','2026-04-15 17:09:34'),('af032771-fbac-45c1-8fbc-dedb90d044ec','App\\Notifications\\SystemNotification','App\\Models\\User',7,'{\"message\":\"Your appointment scheduled for April 7, 2026 at 9:00 AM \\u2013 10:00 AM has been cancelled. You can book a new appointment anytime.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-03 10:58:45\"}','2026-04-03 04:18:43','2026-04-03 02:58:45','2026-04-03 04:18:43'),('af238dfe-9abc-4005-9fbf-7eefe4aa311c','App\\Notifications\\SystemNotification','App\\Models\\User',19,'{\"message\":\"Your pickup appointment has been scheduled for April 23, 2026 at 10:00 AM \\u2013 11:00 AM. Please bring your claiming number: CLM-69E034\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-16 01:00:11\"}',NULL,'2026-04-15 17:00:11','2026-04-15 17:00:11'),('af3e2a2e-a3cb-455f-9edf-91ab1c1ab419','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"\\u2705 Payment settings for Bdo have been updated.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/cashier\\/settings\",\"type\":\"system\",\"time\":\"2026-04-16 01:04:02\"}','2026-04-15 17:09:34','2026-04-15 17:04:02','2026-04-15 17:09:34'),('b8725d7b-9f86-41a3-ac70-f2429ce56a6f','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00077. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/77\",\"type\":\"system\"}','2026-04-02 13:01:18','2026-04-02 11:04:47','2026-04-02 13:01:18'),('b971e8b7-555b-4ef2-8b48-ab42e17d1706','App\\Notifications\\SystemNotification','App\\Models\\User',11,'{\"message\":\"\\u2705 Request DQST-2026-00094 status updated from \'Payment verified\' to \'Processing\'.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/registrar\\/requests\\/94\",\"type\":\"system\",\"time\":\"2026-04-16 00:57:50\"}',NULL,'2026-04-15 16:57:50','2026-04-15 16:57:50'),('b9bee7d7-e01d-42f5-ad60-4028271a1e02','App\\Notifications\\SystemNotification','App\\Models\\User',18,'{\"message\":\"GCash selected as your payment method. Please proceed to upload your proof of payment.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/90\\/upload\",\"type\":\"system\",\"time\":\"2026-04-03 14:52:36\"}',NULL,'2026-04-03 06:52:36','2026-04-03 06:52:36'),('ba6e9e81-95fe-4d0e-8ae2-dcfc712ee810','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00074. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/74\",\"type\":\"system\"}','2026-04-02 10:50:40','2026-04-02 10:47:49','2026-04-02 10:50:40'),('ba870a0a-c569-4e3e-9c20-eb788008bd90','App\\Notifications\\SystemNotification','App\\Models\\User',18,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00095. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/95\",\"type\":\"system\",\"time\":\"2026-04-16 03:56:29\"}',NULL,'2026-04-15 19:56:29','2026-04-15 19:56:29'),('bb10a0be-3d3d-4d00-9ae7-f8482b4c4396','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"\\u2705 Payment settings for Gcash have been updated.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/cashier\\/settings\",\"type\":\"system\",\"time\":\"2026-04-16 01:20:03\"}',NULL,'2026-04-15 17:20:03','2026-04-15 17:20:03'),('bcb178c5-40ce-4ab8-bbaf-9fece067c164','App\\Notifications\\SystemNotification','App\\Models\\User',18,'{\"message\":\"\\u274c Your payment proof for request DQST-2026-00090 was rejected. Reason: testing. Please re-upload a valid proof.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/90\\/upload\",\"type\":\"system\",\"time\":\"2026-04-03 14:56:43\"}',NULL,'2026-04-03 06:56:43','2026-04-03 06:56:43'),('bd75f82c-fc30-495a-b575-43cc8cd009d5','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00079. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/79\",\"type\":\"system\",\"time\":\"2026-04-02 20:22:13\"}','2026-04-02 13:01:18','2026-04-02 12:22:13','2026-04-02 13:01:18'),('bdea0eb2-bae9-4435-a3dc-b362945da379','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"Payment proof uploaded successfully! The cashier will verify it soon.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\"}','2026-04-02 13:01:18','2026-04-02 11:30:39','2026-04-02 13:01:18'),('bfb991f6-ba34-4e5f-b6a9-618dc8bbbe18','App\\Notifications\\SystemNotification','App\\Models\\User',21,'{\"message\":\"Your profile photo has been updated successfully.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/account\",\"type\":\"system\",\"time\":\"2026-04-17 04:26:20\"}',NULL,'2026-04-16 20:26:20','2026-04-16 20:26:20'),('c0262fb3-96d7-4c5f-a1f1-2fb832227114','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"Over-the-Counter Cash selected as your payment method. Please visit the cashier office to pay in person.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/82\\/upload\",\"type\":\"system\",\"time\":\"2026-04-02 21:00:13\"}','2026-04-02 13:01:18','2026-04-02 13:00:13','2026-04-02 13:01:18'),('c0328b04-6cc0-46f0-a926-b33dfd68fab5','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"Your profile photo has been updated successfully.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/account\",\"type\":\"system\",\"time\":\"2026-04-02 20:53:27\"}','2026-04-02 13:01:18','2026-04-02 12:53:27','2026-04-02 13:01:18'),('c40af0cb-e8e6-4d80-a3fe-98bc811779dd','App\\Notifications\\SystemNotification','App\\Models\\User',20,'{\"message\":\"Your appointment scheduled for April 20, 2026 at 1:00 PM \\u2013 2:00 PM has been cancelled. You can book a new appointment anytime.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-16 11:47:31\"}',NULL,'2026-04-16 03:47:31','2026-04-16 03:47:31'),('c4702744-2cdb-44d3-b863-506b6a83b2d9','App\\Notifications\\SystemNotification','App\\Models\\User',21,'{\"message\":\"\\u2705 Your payment for request DQST-2026-00098 has been verified. Your documents are now being processed.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-17 04:23:08\"}','2026-04-16 20:25:01','2026-04-16 20:23:08','2026-04-16 20:25:01'),('c4ed76ba-b790-4034-8bf2-0c42281ade0f','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"Your payment for request DQST-2026-00079 has been verified. Your documents are now being processed.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-03 14:17:18\"}',NULL,'2026-04-03 06:17:18','2026-04-03 06:17:18'),('c56c4edd-55cd-44f6-9581-7036aaf13002','App\\Notifications\\SystemNotification','App\\Models\\User',20,'{\"message\":\"\\ud83d\\udccb Your request DQST-2026-00096 is now being processed. We\'ll notify you when it\'s ready for pickup.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-16 11:45:05\"}',NULL,'2026-04-16 03:45:05','2026-04-16 03:45:05'),('c7946a4d-879b-4106-bf84-92ab699e60dd','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00082. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/82\",\"type\":\"system\",\"time\":\"2026-04-02 20:59:50\"}','2026-04-02 13:01:18','2026-04-02 12:59:50','2026-04-02 13:01:18'),('cba63233-f452-4a24-80a4-53003cd1e42b','App\\Notifications\\SystemNotification','App\\Models\\User',19,'{\"message\":\"Your profile photo has been updated successfully.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/account\",\"type\":\"system\",\"time\":\"2026-04-03 15:16:02\"}','2026-04-03 07:17:05','2026-04-03 07:16:02','2026-04-03 07:17:05'),('cc8ad51a-167b-4a88-a01f-62b01f5a37b0','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"Payment proof uploaded successfully! The cashier will verify it soon.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-02 20:37:16\"}','2026-04-02 13:01:18','2026-04-02 12:37:16','2026-04-02 13:01:18'),('cc9abf26-1d4e-4af4-ba21-c0784ce96470','App\\Notifications\\SystemNotification','App\\Models\\User',20,'{\"message\":\"Your profile photo has been updated successfully.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/account\",\"type\":\"system\",\"time\":\"2026-04-16 11:40:16\"}',NULL,'2026-04-16 03:40:16','2026-04-16 03:40:16'),('cda3413d-2399-48ab-8442-0035d7900145','App\\Notifications\\SystemNotification','App\\Models\\User',20,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00096. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/96\",\"type\":\"system\",\"time\":\"2026-04-16 11:38:54\"}',NULL,'2026-04-16 03:38:54','2026-04-16 03:38:54'),('ce285da3-60d0-46bb-a0df-ad980a8ce271','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00066. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/66\",\"type\":\"system\"}','2026-04-02 13:01:18','2026-04-02 11:36:34','2026-04-02 13:01:18'),('ce487175-190f-49e5-b091-25b729935fbb','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00081. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/81\",\"type\":\"system\",\"time\":\"2026-04-02 20:45:56\"}','2026-04-02 13:01:18','2026-04-02 12:45:56','2026-04-02 13:01:18'),('ce61d1e8-02e2-462c-aab0-99fb8e562d12','App\\Notifications\\SystemNotification','App\\Models\\User',22,'{\"message\":\"\\ud83d\\udce6 Your request DQST-2026-00099 is ready for pickup! Your claiming number is: CLM-69E1B9. Please bring this when claiming your documents.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-17 04:38:27\"}',NULL,'2026-04-16 20:38:27','2026-04-16 20:38:27'),('ce6ff2de-df53-4b24-839c-c61a3ad02b82','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"\\u2705 You verified payment for request DQST-2026-00096 - Amount: \\u20b150.00\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/cashier\\/payments\\/96\",\"type\":\"system\",\"time\":\"2026-04-16 11:42:42\"}',NULL,'2026-04-16 03:42:42','2026-04-16 03:42:42'),('cf3fda1c-0e0e-4e7c-b04c-64994dec6fbe','App\\Notifications\\SystemNotification','App\\Models\\User',22,'{\"message\":\"\\ud83d\\udccb Your request DQST-2026-00099 is now being processed. We\'ll notify you when it\'s ready for pickup.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-17 04:38:18\"}',NULL,'2026-04-16 20:38:18','2026-04-16 20:38:18'),('d0ab30e9-9fc7-4863-8a1d-f0b1d7ced1ea','App\\Notifications\\SystemNotification','App\\Models\\User',22,'{\"message\":\"Your pickup appointment has been scheduled for April 23, 2026 at 8:00 AM \\u2013 9:00 AM. Please bring your claiming number: CLM-69E1B9\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-17 04:39:04\"}',NULL,'2026-04-16 20:39:04','2026-04-16 20:39:04'),('d263f1ea-9462-4ff8-bb35-355d57586a6c','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"\\u2705 Payment settings for Gcash have been updated.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/cashier\\/settings\",\"type\":\"system\",\"time\":\"2026-04-16 01:03:55\"}','2026-04-15 17:09:34','2026-04-15 17:03:55','2026-04-15 17:09:34'),('d3ce0186-55a3-4dde-b8d6-3990e98b001e','App\\Notifications\\SystemNotification','App\\Models\\User',11,'{\"message\":\"\\u2705 Announcement has been updated successfully.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/registrar\\/dashboard\",\"type\":\"system\",\"time\":\"2026-04-17 04:25:39\"}',NULL,'2026-04-16 20:25:39','2026-04-16 20:25:39'),('d85f9cd3-d96e-462c-9db8-98c9fb4ab4b7','App\\Notifications\\SystemNotification','App\\Models\\User',15,'{\"message\":\"GCash selected as your payment method. Please proceed to upload your proof of payment.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/89\\/upload\",\"type\":\"system\",\"time\":\"2026-04-03 14:38:25\"}',NULL,'2026-04-03 06:38:25','2026-04-03 06:38:25'),('d8ee531e-73ad-4af5-acf7-ee8eba9ce839','App\\Notifications\\SystemNotification','App\\Models\\User',7,'{\"message\":\"Your appointment scheduled for April 16, 2026 at 8:00 AM \\u2013 9:00 AM has been cancelled. You can book a new appointment anytime.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-03 10:57:21\"}','2026-04-03 04:18:43','2026-04-03 02:57:21','2026-04-03 04:18:43'),('df81941b-b859-4e02-9d57-d45ae8852fe9','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00070. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/70\",\"type\":\"system\"}','2026-04-02 13:01:18','2026-04-02 11:36:34','2026-04-02 13:01:18'),('e143a148-9cc1-4c54-9f32-479d5ee2c10a','App\\Notifications\\SystemNotification','App\\Models\\User',18,'{\"message\":\"Payment proof uploaded successfully! The cashier will verify it soon.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-03 14:53:08\"}',NULL,'2026-04-03 06:53:08','2026-04-03 06:53:08'),('e2999f21-af4b-4dd3-987d-0db92b30a99b','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"\\u2705 Payment settings for Gcash have been updated.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/cashier\\/settings\",\"type\":\"system\",\"time\":\"2026-04-16 01:03:49\"}','2026-04-15 17:09:34','2026-04-15 17:03:49','2026-04-15 17:09:34'),('e35131e4-7026-49d4-98db-606c366eafab','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"\\u2705 Payment settings for Gcash have been updated.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/cashier\\/settings\",\"type\":\"system\",\"time\":\"2026-04-16 01:08:54\"}','2026-04-15 17:09:34','2026-04-15 17:08:54','2026-04-15 17:09:34'),('e356a356-cfbe-4501-a664-abdd15e82bac','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"Your request has been submitted! Reference: DQST-2026-00076. Please choose a payment method below.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/76\",\"type\":\"system\"}','2026-04-02 11:04:19','2026-04-02 11:03:41','2026-04-02 11:04:19'),('e4517c33-6270-4043-9dde-a6a88ba68aed','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"\\u2705 Bpi payment method has been activated.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/cashier\\/settings\",\"type\":\"system\",\"time\":\"2026-04-16 01:03:42\"}','2026-04-15 17:09:34','2026-04-15 17:03:42','2026-04-15 17:09:34'),('e71a81a6-8b8f-480b-ae24-6b823895553f','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"\\u274c You rejected payment for request DQST-2026-00092 - Reason: testing\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/cashier\\/payments\\/92\",\"type\":\"system\",\"time\":\"2026-04-03 15:19:10\"}','2026-04-03 07:19:28','2026-04-03 07:19:10','2026-04-03 07:19:28'),('e7e8675d-2dc6-45a5-a6fb-962e787c8854','App\\Notifications\\SystemNotification','App\\Models\\User',11,'{\"message\":\"\\u2705 Request DQST-2026-00099 status updated from \'Payment verified\' to \'Processing\'.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/registrar\\/requests\\/99\",\"type\":\"system\",\"time\":\"2026-04-17 04:38:18\"}',NULL,'2026-04-16 20:38:18','2026-04-16 20:38:18'),('e8f9db95-a5ed-4df4-8d63-1ea35b0fc6f7','App\\Notifications\\SystemNotification','App\\Models\\User',11,'{\"message\":\"\\u2705 Request DQST-2026-00094 status updated from \'Processing\' to \'Ready for pickup\'.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/registrar\\/requests\\/94\",\"type\":\"system\",\"time\":\"2026-04-16 00:59:25\"}',NULL,'2026-04-15 16:59:25','2026-04-15 16:59:25'),('e90ea75f-57b5-45e8-9125-62de580b7b0f','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"\\u2705 Payment settings for Gcash have been updated.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/cashier\\/settings\",\"type\":\"system\",\"time\":\"2026-04-16 01:09:22\"}','2026-04-15 17:09:34','2026-04-15 17:09:22','2026-04-15 17:09:34'),('ebab2f83-3674-4bb6-8368-4700e447f4c3','App\\Notifications\\SystemNotification','App\\Models\\User',12,'{\"message\":\"\\u2705 Payment settings for Gcash have been updated.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/cashier\\/settings\",\"type\":\"system\",\"time\":\"2026-04-16 01:16:28\"}',NULL,'2026-04-15 17:16:28','2026-04-15 17:16:28'),('f1a66272-86ac-457b-9c1f-137ae3d98cf3','App\\Notifications\\SystemNotification','App\\Models\\User',21,'{\"message\":\"Your pickup appointment has been scheduled for April 22, 2026 at 1:00 PM \\u2013 2:00 PM. Please bring your claiming number: CLM-69E1B5\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-17 04:24:28\"}','2026-04-16 20:25:01','2026-04-16 20:24:28','2026-04-16 20:25:01'),('f2941075-e384-4771-b980-777cd2b52f9e','App\\Notifications\\SystemNotification','App\\Models\\User',21,'{\"message\":\"\\u274c Your payment proof for request DQST-2026-00097 was rejected. Reason: kulang baliw. Please re-upload a valid proof.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/requests\\/97\\/upload\",\"type\":\"system\",\"time\":\"2026-04-17 04:18:48\"}','2026-04-16 20:25:01','2026-04-16 20:18:48','2026-04-16 20:25:01'),('f464f7b8-dc8c-49b4-98c5-77305bc44b86','App\\Notifications\\SystemNotification','App\\Models\\User',20,'{\"message\":\"\\ud83d\\udce6 Your request DQST-2026-00096 is ready for pickup! Your claiming number is: CLM-69E0CB. Please bring this when claiming your documents.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-16 11:45:15\"}',NULL,'2026-04-16 03:45:15','2026-04-16 03:45:15'),('fa9d7dc9-5a3b-495a-af57-9102036e4009','App\\Notifications\\SystemNotification','App\\Models\\User',21,'{\"message\":\"Payment proof uploaded successfully! The cashier will verify it soon.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-17 04:18:04\"}','2026-04-16 20:25:01','2026-04-16 20:18:04','2026-04-16 20:25:01'),('fbc64bb4-9c69-4cb6-babf-3d1868cbd983','App\\Notifications\\SystemNotification','App\\Models\\User',10,'{\"message\":\"\\u2705 Your cash payment for request DQST-2026-00081 has been recorded. Your documents are now being processed.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-03 14:51:42\"}',NULL,'2026-04-03 06:51:42','2026-04-03 06:51:42'),('fd9f4920-8c3a-407b-96b4-2b03d9e0568e','App\\Notifications\\SystemNotification','App\\Models\\User',20,'{\"message\":\"Your pickup appointment has been scheduled for April 29, 2026 at 9:00 AM \\u2013 10:00 AM. Please bring your claiming number: CLM-69E0CB\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/history\",\"type\":\"system\",\"time\":\"2026-04-16 11:47:40\"}',NULL,'2026-04-16 03:47:40','2026-04-16 03:47:40');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `official_receipts`
--

DROP TABLE IF EXISTS `official_receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `official_receipts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `receipt_number` varchar(30) DEFAULT NULL,
  `document_request_id` bigint(20) unsigned NOT NULL,
  `payment_method` enum('gcash','bank_transfer','cash') NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `issued_by` bigint(20) unsigned NOT NULL,
  `issued_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `file_path` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `official_receipts_receipt_number_unique` (`receipt_number`),
  KEY `official_receipts_document_request_id_foreign` (`document_request_id`),
  KEY `official_receipts_issued_by_foreign` (`issued_by`),
  CONSTRAINT `official_receipts_document_request_id_foreign` FOREIGN KEY (`document_request_id`) REFERENCES `document_requests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `official_receipts_issued_by_foreign` FOREIGN KEY (`issued_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `official_receipts`
--

LOCK TABLES `official_receipts` WRITE;
/*!40000 ALTER TABLE `official_receipts` DISABLE KEYS */;
/*!40000 ALTER TABLE `official_receipts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_proofs`
--

DROP TABLE IF EXISTS `payment_proofs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_proofs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `document_request_id` bigint(20) unsigned NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `file_size_kb` int(11) DEFAULT NULL,
  `amount_declared` decimal(10,2) DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `is_resubmission` tinyint(1) NOT NULL DEFAULT 0,
  `verified_by` bigint(20) unsigned DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_proofs_document_request_id_foreign` (`document_request_id`),
  KEY `payment_proofs_verified_by_foreign` (`verified_by`),
  CONSTRAINT `payment_proofs_document_request_id_foreign` FOREIGN KEY (`document_request_id`) REFERENCES `document_requests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_proofs_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_proofs`
--

LOCK TABLES `payment_proofs` WRITE;
/*!40000 ALTER TABLE `payment_proofs` DISABLE KEYS */;
INSERT INTO `payment_proofs` VALUES (12,72,'payments/1775155348_10_69ceb8944f3d3.jpeg','_ (3).jpeg',9,70.00,NULL,0,12,'2026-04-03 06:07:50','eashsrh','2026-04-02 10:42:28','2026-04-03 06:07:50'),(13,75,'payments/1775156273_10_69cebc3108f7f.png','ccst-logo.png',118,50.00,NULL,0,NULL,NULL,NULL,'2026-04-02 10:57:53','2026-04-02 10:57:53'),(14,77,'payments/1775156725_10_69cebdf510853.jpeg','locket.jpeg',38,75.00,NULL,0,12,'2026-04-03 06:07:11','Please upload actual receipt','2026-04-02 11:05:25','2026-04-03 06:07:11'),(15,66,'payments/1775156752_10_69cebe1011d9e.jpeg','_ (2).jpeg',51,80.00,NULL,0,NULL,NULL,NULL,'2026-04-02 11:05:52','2026-04-02 11:05:52'),(16,78,'payments/1775158239_10_69cec3dfc1e9a.jpg','whitewp hd.jpg',285,100.00,NULL,0,12,'2026-04-03 06:01:44','di pwede','2026-04-02 11:30:39','2026-04-03 06:01:44'),(17,80,'payments/1775162235_10_69ced37be33e3.jpeg','_ (3).jpeg',17,120.00,NULL,0,12,'2026-04-03 06:01:32','karakul','2026-04-02 12:37:15','2026-04-03 06:01:32'),(20,88,'payments/1775226768_18_69cfcf90d2d0b.jpeg','gcash.jpeg',49,155.00,NULL,0,12,'2026-04-03 06:33:16','not sufficient','2026-04-03 06:32:48','2026-04-03 06:33:16'),(21,89,'payments/1775227112_15_69cfd0e8a19e8.jpeg','gcash.jpeg',49,120.00,NULL,0,12,'2026-04-03 06:38:53','awrwehfASLFJS;ODFJPA','2026-04-03 06:38:32','2026-04-03 06:38:53'),(22,90,'payments/1775227964_18_69cfd43ca3c87.jpeg','gcash.jpeg',49,100.00,NULL,0,12,'2026-04-03 06:56:43','testing','2026-04-03 06:52:44','2026-04-03 06:56:43'),(23,91,'payments/1775227988_18_69cfd454b7740.jpeg','bank-transfer.jpeg',66,125.00,NULL,0,12,'2026-04-03 06:53:31','adhlSFNwlorjqwp TESTING','2026-04-03 06:53:08','2026-04-03 06:53:31'),(24,92,'payments/1775229443_19_69cfda0347d6a.jpeg','gcash.jpeg',49,150.00,NULL,0,12,'2026-04-03 07:19:10','testing','2026-04-03 07:17:23','2026-04-03 07:19:10'),(25,93,'payments/1775229470_19_69cfda1ecfea5.jpeg','bank-transfer.jpeg',66,50.00,NULL,0,12,'2026-04-03 07:18:58',NULL,'2026-04-03 07:17:50','2026-04-03 07:18:58'),(26,96,'payments/1776339575_20_69e0ca7748d16.jpeg','_ (1).jpeg',24,50.00,NULL,0,12,'2026-04-16 03:42:42',NULL,'2026-04-16 03:39:35','2026-04-16 03:42:42'),(27,97,'payments/1776399484_21_69e1b47c09f1a.jpeg','gcash.jpeg',49,175.00,NULL,0,12,'2026-04-16 20:18:48','kulang baliw','2026-04-16 20:18:04','2026-04-16 20:18:48'),(28,98,'payments/1776399762_21_69e1b592d1fdb.jpeg','bank-transfer.jpeg',66,150.00,NULL,0,12,'2026-04-16 20:23:08',NULL,'2026-04-16 20:22:42','2026-04-16 20:23:08');
/*!40000 ALTER TABLE `payment_proofs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_settings`
--

DROP TABLE IF EXISTS `payment_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `method` enum('gcash','bdo','bpi','cash') NOT NULL,
  `account_name` varchar(255) NOT NULL DEFAULT '',
  `account_number` varchar(100) NOT NULL DEFAULT '',
  `bank_name` varchar(100) DEFAULT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `extra_info` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_settings_method_unique` (`method`),
  KEY `payment_settings_updated_by_foreign` (`updated_by`),
  CONSTRAINT `payment_settings_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_settings`
--

LOCK TABLES `payment_settings` WRITE;
/*!40000 ALTER TABLE `payment_settings` DISABLE KEYS */;
INSERT INTO `payment_settings` VALUES (1,'gcash','CCST Registrar','09XX-XXX-jjjjadfsadfasgk',NULL,NULL,NULL,1,12,'2026-03-20 03:34:19','2026-04-15 17:20:03'),(2,'bdo','CCST Registrar','XXXX-XXX','BDO','mabalacat bejsafsa',NULL,1,12,'2026-03-20 03:34:19','2026-04-15 17:16:47'),(3,'bpi','CCST Registrar','XXXX-XXXX-XXXX','BPI',NULL,NULL,1,12,'2026-03-20 03:34:19','2026-04-15 17:03:42'),(4,'cash','fjvjhj','68998-',NULL,NULL,'SNS Building, Ground Floor — Mon to Fri, 8:00 AM to 5:00 PM jjkk',1,12,'2026-03-20 03:34:19','2026-04-15 17:09:47');
/*!40000 ALTER TABLE `payment_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('Vu6CFZfHUQ0XBnkdAiUtoHmQ7yVLSWUBAkt8o9MP',22,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.6 Safari/605.1.15','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiOWVBMlBGd2JqNWtjS2x5VTZLdHVKQ2xSUmZ5T09MQmJGR2lIczZ4SCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9zdHVkZW50L2Rhc2hib2FyZCI7czo1OiJyb3V0ZSI7czoxNzoic3R1ZGVudC5kYXNoYm9hcmQiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyMjt9',1776401138);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `status_logs`
--

DROP TABLE IF EXISTS `status_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `status_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `document_request_id` bigint(20) unsigned NOT NULL,
  `changed_by` bigint(20) unsigned NOT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status_logs_document_request_id_foreign` (`document_request_id`),
  KEY `status_logs_changed_by_foreign` (`changed_by`),
  CONSTRAINT `status_logs_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`),
  CONSTRAINT `status_logs_document_request_id_foreign` FOREIGN KEY (`document_request_id`) REFERENCES `document_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=197 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `status_logs`
--

LOCK TABLES `status_logs` WRITE;
/*!40000 ALTER TABLE `status_logs` DISABLE KEYS */;
INSERT INTO `status_logs` VALUES (9,7,8,NULL,'pending','Request submitted by student.','2026-03-27 04:18:13','2026-03-27 04:18:13'),(10,8,8,NULL,'pending','Request submitted by student.','2026-03-27 04:19:49','2026-03-27 04:19:49'),(11,9,8,NULL,'pending','Request submitted by student.','2026-03-27 04:30:52','2026-03-27 04:30:52'),(12,10,8,NULL,'pending','Request submitted by student.','2026-03-27 04:37:41','2026-03-27 04:37:41'),(13,11,8,NULL,'pending','Request submitted by student.','2026-03-27 04:37:52','2026-03-27 04:37:52'),(14,12,8,NULL,'pending','Request submitted by student.','2026-03-27 05:07:22','2026-03-27 05:07:22'),(15,13,8,NULL,'pending','Request submitted by student.','2026-03-27 05:07:42','2026-03-27 05:07:42'),(16,13,8,'pending','payment_method_set','Payment method set to: bank_transfer','2026-03-27 05:07:51','2026-03-27 05:07:51'),(17,14,8,NULL,'pending','Request submitted by student.','2026-03-27 05:22:04','2026-03-27 05:22:04'),(18,15,8,NULL,'pending','Request submitted by student.','2026-03-27 05:25:52','2026-03-27 05:25:52'),(101,62,10,NULL,'pending','Request submitted by student.','2026-04-02 09:18:25','2026-04-02 09:18:25'),(102,63,10,NULL,'pending','Request submitted by student.','2026-04-02 09:24:40','2026-04-02 09:24:40'),(103,63,10,'pending','payment_method_set','Payment method set to: cash','2026-04-02 09:24:48','2026-04-02 09:24:48'),(104,64,10,NULL,'pending','Request submitted by student.','2026-04-02 09:26:41','2026-04-02 09:26:41'),(105,65,10,NULL,'pending','Request submitted by student.','2026-04-02 10:03:17','2026-04-02 10:03:17'),(106,66,10,NULL,'pending','Request submitted by student.','2026-04-02 10:11:09','2026-04-02 10:11:09'),(107,66,10,'pending','payment_method_set','Payment method set to: gcash','2026-04-02 10:11:24','2026-04-02 10:11:24'),(108,67,10,NULL,'pending','Request submitted by student.','2026-04-02 10:12:22','2026-04-02 10:12:22'),(109,68,10,NULL,'pending','Request submitted by student.','2026-04-02 10:18:17','2026-04-02 10:18:17'),(110,69,10,NULL,'pending','Request submitted by student.','2026-04-02 10:19:19','2026-04-02 10:19:19'),(111,70,10,NULL,'pending','Request submitted by student.','2026-04-02 10:27:19','2026-04-02 10:27:19'),(112,71,10,NULL,'pending','Request submitted by student.','2026-04-02 10:35:12','2026-04-02 10:35:12'),(113,72,10,NULL,'pending','Request submitted by student.','2026-04-02 10:41:56','2026-04-02 10:41:56'),(114,72,10,'pending','payment_method_set','Payment method set to: gcash','2026-04-02 10:42:09','2026-04-02 10:42:09'),(115,72,10,'payment_method_set','payment_uploaded','Student uploaded payment proof.','2026-04-02 10:42:28','2026-04-02 10:42:28'),(116,73,10,NULL,'pending','Request submitted by student.','2026-04-02 10:47:33','2026-04-02 10:47:33'),(117,74,10,NULL,'pending','Request submitted by student.','2026-04-02 10:47:49','2026-04-02 10:47:49'),(118,74,10,'pending','payment_method_set','Payment method set to: cash','2026-04-02 10:48:03','2026-04-02 10:48:03'),(119,70,10,'pending','payment_method_set','Payment method set to: cash','2026-04-02 10:49:22','2026-04-02 10:49:22'),(120,75,10,NULL,'pending','Request submitted by student.','2026-04-02 10:57:17','2026-04-02 10:57:17'),(121,75,10,'pending','payment_method_set','Payment method set to: gcash','2026-04-02 10:57:34','2026-04-02 10:57:34'),(122,75,10,'payment_method_set','payment_uploaded','Student uploaded payment proof.','2026-04-02 10:57:53','2026-04-02 10:57:53'),(123,76,10,NULL,'pending','Request submitted by student.','2026-04-02 11:03:41','2026-04-02 11:03:41'),(124,76,10,'pending','payment_method_set','Payment method set to: cash','2026-04-02 11:03:48','2026-04-02 11:03:48'),(125,77,10,NULL,'pending','Request submitted by student.','2026-04-02 11:04:47','2026-04-02 11:04:47'),(126,77,10,'pending','payment_method_set','Payment method set to: gcash','2026-04-02 11:04:56','2026-04-02 11:04:56'),(127,77,10,'payment_method_set','payment_uploaded','Student uploaded payment proof.','2026-04-02 11:05:25','2026-04-02 11:05:25'),(128,66,10,'payment_method_set','payment_uploaded','Student uploaded payment proof.','2026-04-02 11:05:52','2026-04-02 11:05:52'),(129,78,10,NULL,'pending','Request submitted by student.','2026-04-02 11:21:01','2026-04-02 11:21:01'),(130,78,10,'pending','payment_method_set','Payment method set to: gcash','2026-04-02 11:21:25','2026-04-02 11:21:25'),(131,78,10,'payment_method_set','payment_method_set','Payment method set to: gcash','2026-04-02 11:30:02','2026-04-02 11:30:02'),(132,78,10,'payment_method_set','payment_uploaded','Student uploaded payment proof.','2026-04-02 11:30:39','2026-04-02 11:30:39'),(133,79,10,NULL,'pending','Request submitted by student.','2026-04-02 12:22:12','2026-04-02 12:22:12'),(134,79,10,'pending','payment_method_set','Payment method set to: cash','2026-04-02 12:22:23','2026-04-02 12:22:23'),(135,80,10,NULL,'pending','Request submitted by student.','2026-04-02 12:31:28','2026-04-02 12:31:28'),(136,80,10,'pending','payment_method_set','Payment method set to: bank_transfer','2026-04-02 12:36:52','2026-04-02 12:36:52'),(137,80,10,'payment_method_set','payment_uploaded','Student uploaded payment proof.','2026-04-02 12:37:15','2026-04-02 12:37:15'),(138,81,10,NULL,'pending','Request submitted by student.','2026-04-02 12:45:56','2026-04-02 12:45:56'),(139,81,10,'pending','payment_method_set','Payment method set to: cash','2026-04-02 12:46:19','2026-04-02 12:46:19'),(140,73,10,'pending','cancelled','Request cancelled by student.','2026-04-02 12:50:53','2026-04-02 12:50:53'),(141,82,10,NULL,'pending','Request submitted by student.','2026-04-02 12:59:50','2026-04-02 12:59:50'),(142,82,10,'pending','payment_method_set','Payment method set to: cash','2026-04-02 13:00:13','2026-04-02 13:00:13'),(155,87,15,NULL,'pending','Request submitted by student.','2026-04-03 04:00:23','2026-04-03 04:00:23'),(156,88,18,NULL,'pending','Request submitted by student.','2026-04-03 06:32:23','2026-04-03 06:32:23'),(157,88,18,'pending','payment_method_set','Payment method set to: gcash','2026-04-03 06:32:28','2026-04-03 06:32:28'),(158,88,18,'payment_method_set','payment_uploaded','Student uploaded payment proof.','2026-04-03 06:32:48','2026-04-03 06:32:48'),(159,89,15,NULL,'pending','Request submitted by student.','2026-04-03 06:38:21','2026-04-03 06:38:21'),(160,89,15,'pending','payment_method_set','Payment method set to: gcash','2026-04-03 06:38:25','2026-04-03 06:38:25'),(161,89,15,'payment_method_set','payment_uploaded','Student uploaded payment proof.','2026-04-03 06:38:32','2026-04-03 06:38:32'),(162,90,18,NULL,'pending','Request submitted by student.','2026-04-03 06:52:32','2026-04-03 06:52:32'),(163,90,18,'pending','payment_method_set','Payment method set to: gcash','2026-04-03 06:52:36','2026-04-03 06:52:36'),(164,90,18,'payment_method_set','payment_uploaded','Student uploaded payment proof.','2026-04-03 06:52:44','2026-04-03 06:52:44'),(165,91,18,NULL,'pending','Request submitted by student.','2026-04-03 06:52:56','2026-04-03 06:52:56'),(166,91,18,'pending','payment_method_set','Payment method set to: bank_transfer','2026-04-03 06:53:01','2026-04-03 06:53:01'),(167,91,18,'payment_method_set','payment_uploaded','Student uploaded payment proof.','2026-04-03 06:53:08','2026-04-03 06:53:08'),(168,92,19,NULL,'pending','Request submitted by student.','2026-04-03 07:16:59','2026-04-03 07:16:59'),(169,92,19,'pending','payment_method_set','Payment method set to: gcash','2026-04-03 07:17:11','2026-04-03 07:17:11'),(170,92,19,'payment_method_set','payment_uploaded','Student uploaded payment proof.','2026-04-03 07:17:23','2026-04-03 07:17:23'),(171,93,19,NULL,'pending','Request submitted by student.','2026-04-03 07:17:35','2026-04-03 07:17:35'),(172,93,19,'pending','payment_method_set','Payment method set to: bank_transfer','2026-04-03 07:17:40','2026-04-03 07:17:40'),(173,93,19,'payment_method_set','payment_uploaded','Student uploaded payment proof.','2026-04-03 07:17:50','2026-04-03 07:17:50'),(174,94,19,NULL,'pending','Request submitted by student.','2026-04-03 07:18:03','2026-04-03 07:18:03'),(175,94,19,'pending','payment_method_set','Payment method set to: cash','2026-04-03 07:18:11','2026-04-03 07:18:11'),(176,94,11,'payment_verified','processing','Status updated by registrar.','2026-04-15 16:57:50','2026-04-15 16:57:50'),(177,75,11,'payment_verified','processing','Status updated by registrar.','2026-04-15 16:59:08','2026-04-15 16:59:08'),(178,94,11,'processing','ready_for_pickup','Status updated by registrar.','2026-04-15 16:59:25','2026-04-15 16:59:25'),(179,95,18,NULL,'pending','Request submitted by student.','2026-04-15 19:56:29','2026-04-15 19:56:29'),(180,96,20,NULL,'pending','Request submitted by student.','2026-04-16 03:38:54','2026-04-16 03:38:54'),(181,96,20,'pending','payment_method_set','Payment method set to: gcash','2026-04-16 03:39:11','2026-04-16 03:39:11'),(182,96,20,'payment_method_set','payment_uploaded','Student uploaded payment proof.','2026-04-16 03:39:35','2026-04-16 03:39:35'),(183,96,11,'payment_verified','processing','Status updated by registrar.','2026-04-16 03:45:05','2026-04-16 03:45:05'),(184,96,11,'processing','ready_for_pickup','Status updated by registrar.','2026-04-16 03:45:15','2026-04-16 03:45:15'),(185,97,21,NULL,'pending','Request submitted by student.','2026-04-16 20:17:09','2026-04-16 20:17:09'),(186,97,21,'pending','payment_method_set','Payment method set to: gcash','2026-04-16 20:17:36','2026-04-16 20:17:36'),(187,97,21,'payment_method_set','payment_uploaded','Student uploaded payment proof.','2026-04-16 20:18:04','2026-04-16 20:18:04'),(188,98,21,NULL,'pending','Request submitted by student.','2026-04-16 20:22:29','2026-04-16 20:22:29'),(189,98,21,'pending','payment_method_set','Payment method set to: bank_transfer','2026-04-16 20:22:35','2026-04-16 20:22:35'),(190,98,21,'payment_method_set','payment_uploaded','Student uploaded payment proof.','2026-04-16 20:22:42','2026-04-16 20:22:42'),(191,98,11,'payment_verified','processing','Status updated by registrar.','2026-04-16 20:23:47','2026-04-16 20:23:47'),(192,98,11,'processing','ready_for_pickup','Status updated by registrar.','2026-04-16 20:24:00','2026-04-16 20:24:00'),(193,99,22,NULL,'pending','Request submitted by student.','2026-04-16 20:35:33','2026-04-16 20:35:33'),(194,99,22,'pending','payment_method_set','Payment method set to: cash','2026-04-16 20:36:41','2026-04-16 20:36:41'),(195,99,11,'payment_verified','processing','Status updated by registrar.','2026-04-16 20:38:18','2026-04-16 20:38:18'),(196,99,11,'processing','ready_for_pickup','Status updated by registrar.','2026-04-16 20:38:27','2026-04-16 20:38:27');
/*!40000 ALTER TABLE `status_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `time_slots`
--

DROP TABLE IF EXISTS `time_slots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `time_slots` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(100) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `max_capacity` int(11) NOT NULL DEFAULT 5,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `time_slots`
--

LOCK TABLES `time_slots` WRITE;
/*!40000 ALTER TABLE `time_slots` DISABLE KEYS */;
INSERT INTO `time_slots` VALUES (1,'8:00 AM – 9:00 AM','08:00:00','09:00:00',5,1,'2026-04-02 07:18:06','2026-04-02 07:18:06'),(2,'9:00 AM – 10:00 AM','09:00:00','10:00:00',5,1,'2026-04-02 07:18:06','2026-04-02 07:18:06'),(3,'10:00 AM – 11:00 AM','10:00:00','11:00:00',5,1,'2026-04-02 07:18:06','2026-04-02 07:18:06'),(4,'1:00 PM – 2:00 PM','13:00:00','14:00:00',5,1,'2026-04-02 07:18:06','2026-04-02 07:18:06'),(5,'2:00 PM – 3:00 PM','14:00:00','15:00:00',5,1,'2026-04-02 07:18:06','2026-04-02 07:18:06'),(6,'3:00 PM – 4:00 PM','15:00:00','16:00:00',5,1,'2026-04-02 07:18:06','2026-04-16 03:50:25');
/*!40000 ALTER TABLE `time_slots` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','registrar','cashier') NOT NULL,
  `student_number` varchar(50) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `strand` varchar(100) DEFAULT NULL,
  `grade_level` varchar(20) DEFAULT NULL,
  `section` varchar(50) DEFAULT NULL,
  `profile_photo` varchar(500) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (8,'Kellychen',NULL,'Aniate','Kellychen Aniate','kellychensicat@gmail.com',NULL,'$2y$12$7GmUdZjYhZ60Zv746T2vUOsrIqRXNB8woo07Ahi/6PIiiqwbydCjO','student','05-9000','09123456789',NULL,'STEM','Grade 11','Gosling',NULL,NULL,'2026-03-27 04:16:58','2026-04-03 04:08:28'),(10,'Juan','dela','Cruz','Juan dela Cruz','student@ccst.edu.ph',NULL,'$2y$12$wDPtZ2ccNTV29b8K3fRrNOEqp//qbsFUMcgX3CZArPMTXfh9dDMEK','student','2024-00001','09171234569','Dau, Mabalacat City, Pampanga','ICT','Grade 12','Diligence','photos/user_10_1775163563.jpeg',NULL,'2026-04-02 07:18:05','2026-04-03 04:08:28'),(11,'Maria',NULL,'Santos','Maria Santos','registrar@ccst.edu.ph',NULL,'$2y$12$AZrJGKu5LIoUJzkW/sq6Ce2mqMKTEyEeA3hbrcOn5i2mR8zRtwfs.','registrar',NULL,'09181234567',NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-02 07:18:06','2026-04-03 04:08:28'),(12,'Pedro',NULL,'Reyes','Pedro Reyes','cashier@ccst.edu.ph',NULL,'$2y$12$Y3O6CDzKMifiFC5XVE2U2.TXSm5/P1yYidWzlgrhVThhAdeaDqMDa','cashier',NULL,'09191234567',NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-02 07:18:06','2026-04-03 04:08:28'),(15,'Kyle','Carlo Sufrir','Potestad','Kyle Carlo Sufrir Potestad','kylecarlo@gmail.com',NULL,'$2y$12$B0SZED6BU1zHuRfMcggDVem1R2sZKCzUDQCowU/LC8842wBZ0rSWq','student','05-8532','0943 567 9876',NULL,'STEM','Grade 12','STEM-12B',NULL,NULL,'2026-04-03 03:56:44','2026-04-03 04:08:28'),(16,'Evol Drey','Cortez','Tiglao','Evol Drey Cortez Tiglao','evoltiglao@gmail.com',NULL,'$2y$12$ufAl5Rfqfxj9afG4Tavo7OAG1r.uTJu17KzRahR923teRaQb5ReOa','student','05-1111','0923 456 7865',NULL,'ICT','Grade 12','ICT-12A',NULL,NULL,'2026-04-03 04:14:46','2026-04-03 04:14:46'),(17,'Kellychen','Sicat','Aniate',NULL,'kellychen@gmail.com',NULL,'cashier123','cashier',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(18,'Eryza','C.','Galang','Eryza C. Galang','eryzagalang@gmail.com',NULL,'$2y$12$urbRO2u0cLiyZMtNk10A3ubH8WYyHUyoanvIVKrIBasG9bso8wG1a','student','05-3245','0912 182 1234',NULL,'ICT','Grade 12','ICT-12A','photos/user_18_1776311748.jpeg',NULL,'2026-04-03 06:32:07','2026-04-15 19:55:48'),(19,'Karylle','Mendiola','Viray','Karylle Mendiola Viray','karylleviray111@gmail.com',NULL,'$2y$12$KBRGd9GyTNqxiYcNZvguhuGLEqs15SXA3w1VZc6io.34HFQ0qWCqy','student','05-8959','09929363432','Sindalan, San Fernando','ICT','Grade 11','ICT-11A','photos/user_19_1775229371.jpeg',NULL,'2026-04-03 07:15:37','2026-04-03 07:16:40'),(20,'Evol','Cortez','Tiglao','Evol Cortez Tiglao','tiglaoevol@gmail.com',NULL,'$2y$12$zn76vzJQ8iuGRausMEiKO.92plWWKxNTQMUxQbAkyY1Xy5kR7Z6sS','student','123456789','0939 124 6857',NULL,'ICT','Grade 12','ICT-12B','photos/user_20_1776339655.jpeg',NULL,'2026-04-16 03:37:23','2026-04-16 03:40:55'),(21,'Nathaniel','Del Rosario','Deligero','Nathaniel Del Rosario Deligero','nathery214@gmail.com',NULL,'$2y$12$Wab7Zv8ggLzRl9dkmKvCruRpYiYe.U.G3aPa17Y0zV4fNHVPWv8p6','student','05-8713','09932526700',NULL,'ICT','Grade 12','ICT-12A','photos/user_21_1776399980.jpeg',NULL,'2026-04-16 20:16:20','2026-04-16 20:26:20'),(22,'Minji','M.','Potestad','Minji M. Potestad','minjipotestad@gmail.com',NULL,'$2y$12$I49L.quEIwhaBpWb/Z29zeKqulIsFPlmmrsDwktkY6lcW9cibLZSK','student','05-00-2344','0912 456 5678',NULL,'HUMSS','Grade 11','HUMSS-11D',NULL,NULL,'2026-04-16 20:34:32','2026-04-16 20:34:32');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-19 14:02:23
