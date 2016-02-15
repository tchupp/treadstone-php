# ************************************************************
# Sequel Pro SQL dump
# Version 4500
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.7.10)
# Database: theochup_treadstone_dev
# Generation Time: 2016-02-15 18:09:52 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table treadstone_authority
# ------------------------------------------------------------

DROP TABLE IF EXISTS `treadstone_authority`;

CREATE TABLE `treadstone_authority` (
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `treadstone_authority` WRITE;
/*!40000 ALTER TABLE `treadstone_authority` DISABLE KEYS */;

INSERT INTO `treadstone_authority` (`name`)
VALUES
	('ROLE_ADMIN'),
	('ROLE_USER');

/*!40000 ALTER TABLE `treadstone_authority` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table treadstone_course
# ------------------------------------------------------------

DROP TABLE IF EXISTS `treadstone_course`;

CREATE TABLE `treadstone_course` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `section_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_course_user_section` (`user_id`,`section_id`),
  KEY `fk_course_section_id` (`section_id`),
  CONSTRAINT `fk_course_section_id` FOREIGN KEY (`section_id`) REFERENCES `treadstone_section` (`id`),
  CONSTRAINT `fk_course_user_id` FOREIGN KEY (`user_id`) REFERENCES `treadstone_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `treadstone_course` WRITE;
/*!40000 ALTER TABLE `treadstone_course` DISABLE KEYS */;

INSERT INTO `treadstone_course` (`id`, `user_id`, `section_id`)
VALUES
	(1,2,1),
	(2,2,2),
	(3,2,3),
	(4,2,4),
	(5,2,5),
	(7,2,6),
	(8,2,7),
	(9,2,8);

/*!40000 ALTER TABLE `treadstone_course` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table treadstone_day
# ------------------------------------------------------------

DROP TABLE IF EXISTS `treadstone_day`;

CREATE TABLE `treadstone_day` (
  `name` varchar(50) NOT NULL DEFAULT '',
  `number` int(1) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `treadstone_day` WRITE;
/*!40000 ALTER TABLE `treadstone_day` DISABLE KEYS */;

INSERT INTO `treadstone_day` (`name`, `number`)
VALUES
	('Friday',6),
	('Monday',2),
	('Saturday',7),
	('Sunday',1),
	('Thursday',5),
	('Tuesday',3),
	('Wednesday',4);

/*!40000 ALTER TABLE `treadstone_day` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table treadstone_section
# ------------------------------------------------------------

DROP TABLE IF EXISTS `treadstone_section`;

CREATE TABLE `treadstone_section` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subject_id` bigint(20) unsigned NOT NULL,
  `semester_id` bigint(20) unsigned NOT NULL,
  `section_number` int(2) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_section_subject_semester_number` (`subject_id`,`semester_id`,`section_number`),
  KEY `fk_section_semester_id` (`semester_id`),
  CONSTRAINT `fk_section_semester_id` FOREIGN KEY (`semester_id`) REFERENCES `treadstone_semester` (`id`),
  CONSTRAINT `fk_section_subject_id` FOREIGN KEY (`subject_id`) REFERENCES `treadstone_subject` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `treadstone_section` WRITE;
/*!40000 ALTER TABLE `treadstone_section` DISABLE KEYS */;

INSERT INTO `treadstone_section` (`id`, `subject_id`, `semester_id`, `section_number`)
VALUES
	(5,13,1,2),
	(6,14,1,1),
	(7,15,1,4),
	(8,16,1,5),
	(1,17,2,2),
	(2,18,2,1),
	(3,19,2,1),
	(4,20,2,1);

/*!40000 ALTER TABLE `treadstone_section` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table treadstone_section_time
# ------------------------------------------------------------

DROP TABLE IF EXISTS `treadstone_section_time`;

CREATE TABLE `treadstone_section_time` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `section_id` bigint(20) unsigned NOT NULL,
  `day_name` varchar(50) NOT NULL DEFAULT '',
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_section_time` (`section_id`,`day_name`,`start_time`,`end_time`),
  KEY `fk_section_time_day_name` (`day_name`),
  CONSTRAINT `fk_section_time_day_name` FOREIGN KEY (`day_name`) REFERENCES `treadstone_day` (`name`),
  CONSTRAINT `fk_section_time_section_id` FOREIGN KEY (`section_id`) REFERENCES `treadstone_section` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `treadstone_section_time` WRITE;
/*!40000 ALTER TABLE `treadstone_section_time` DISABLE KEYS */;

INSERT INTO `treadstone_section_time` (`id`, `section_id`, `day_name`, `start_time`, `end_time`)
VALUES
	(1,1,'Monday','15:00:00','16:20:00'),
	(2,1,'Wednesday','15:00:00','16:20:00'),
	(4,2,'Thursday','10:20:00','11:40:00'),
	(3,2,'Tuesday','10:20:00','11:40:00'),
	(6,3,'Thursday','12:40:00','14:00:00'),
	(5,3,'Tuesday','12:40:00','14:00:00'),
	(7,4,'Monday','12:40:00','14:00:00'),
	(8,4,'Wednesday','12:40:00','14:00:00'),
	(11,5,'Thursday','12:40:00','14:30:00'),
	(10,5,'Tuesday','12:40:00','14:30:00'),
	(16,6,'Friday','09:10:00','10:00:00'),
	(12,6,'Monday','09:10:00','10:00:00'),
	(15,6,'Thursday','09:10:00','10:00:00'),
	(13,6,'Tuesday','09:10:00','10:00:00'),
	(14,6,'Wednesday','09:10:00','10:00:00'),
	(19,7,'Friday','11:30:00','12:20:00'),
	(17,7,'Monday','11:30:00','12:20:00'),
	(20,7,'Thursday','15:00:00','17:50:00'),
	(18,7,'Wednesday','11:30:00','12:20:00'),
	(24,8,'Friday','10:20:00','11:10:00'),
	(21,8,'Monday','10:20:00','11:10:00'),
	(25,8,'Monday','15:00:00','17:50:00'),
	(23,8,'Wednesday','10:20:00','11:10:00');

/*!40000 ALTER TABLE `treadstone_section_time` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table treadstone_semester
# ------------------------------------------------------------

DROP TABLE IF EXISTS `treadstone_semester`;

CREATE TABLE `treadstone_semester` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(4) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_semester_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `treadstone_semester` WRITE;
/*!40000 ALTER TABLE `treadstone_semester` DISABLE KEYS */;

INSERT INTO `treadstone_semester` (`id`, `code`, `name`)
VALUES
	(1,'SS16','Spring Semester 2016'),
	(2,'FS15','Fall Semester 2015');

/*!40000 ALTER TABLE `treadstone_semester` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table treadstone_subject
# ------------------------------------------------------------

DROP TABLE IF EXISTS `treadstone_subject`;

CREATE TABLE `treadstone_subject` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(8) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_subject_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `treadstone_subject` WRITE;
/*!40000 ALTER TABLE `treadstone_subject` DISABLE KEYS */;

INSERT INTO `treadstone_subject` (`id`, `code`, `name`)
VALUES
	(1,'ECE 302','Electronic Circuits'),
	(2,'ECE 303','Electronics Laboratory'),
	(3,'ECE 305','Electromagnetic Fields and Waves I'),
	(4,'ECE 313','Control Systems'),
	(5,'ECE 320','Energy Conversion and Power Electronics'),
	(6,'ECE 331','Microprocessors and Digital Systems'),
	(7,'ECE 345','Electronic Instrumentation and Systems'),
	(8,'ECE 366','Introduction to Signal Processing'),
	(9,'ECE 390','Ethics, Professionalism and Contemporary Issues'),
	(10,'CSE 320','Computer Organization and Architecture'),
	(11,'CSE 331','Algorithms and Data Structures'),
	(12,'CSE 335','Object-oriented Software Design'),
	(13,'IAH 241A','Music and Society in the Modern World'),
	(14,'GRM 101','Elementary German I'),
	(15,'ECE 410','VLSI Design'),
	(16,'ECE 480','Senior Design'),
	(17,'CSE 410','Operating Systems'),
	(18,'CSE 422','Computer Networks'),
	(19,'CSE 450','Translation of Programming Languages'),
	(20,'CSE 476','Mobile Application Development');

/*!40000 ALTER TABLE `treadstone_subject` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table treadstone_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `treadstone_user`;

CREATE TABLE `treadstone_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `password_hash` varchar(60) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `activated` int(1) NOT NULL,
  `activation_key` varchar(20) DEFAULT NULL,
  `reset_key` varchar(20) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `reset_date` timestamp NULL DEFAULT NULL,
  `last_modified_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_login` (`login`),
  UNIQUE KEY `uq_user_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `treadstone_user` WRITE;
/*!40000 ALTER TABLE `treadstone_user` DISABLE KEYS */;

INSERT INTO `treadstone_user` (`id`, `login`, `password_hash`, `first_name`, `last_name`, `email`, `activated`, `activation_key`, `reset_key`, `created_date`, `reset_date`, `last_modified_date`)
VALUES
	(2,'chuppthe','$2a$10$c110a6fb7f49b251605b3u0Q5o3v8YRMCvJIMJMsqhCCKsNmNP6fS','Theo','Chupp','chuppthe@msu.edu',1,'',NULL,'2016-01-26 21:14:38',NULL,NULL),
	(5,'tclchiam','$2a$10$d4f569b720c1c86135c2buUr6RW1jpNzes70WFfkGHdQ4bMpukKUC','Theo','Chupp','tclchiam@gmail.com',1,'','','2016-02-15 13:02:40',NULL,NULL);

/*!40000 ALTER TABLE `treadstone_user` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table treadstone_user_authority
# ------------------------------------------------------------

DROP TABLE IF EXISTS `treadstone_user_authority`;

CREATE TABLE `treadstone_user_authority` (
  `user_id` bigint(20) unsigned NOT NULL,
  `authority_name` varchar(50) NOT NULL,
  PRIMARY KEY (`user_id`,`authority_name`),
  KEY `fk_user_authority_authority_name` (`authority_name`),
  CONSTRAINT `fk_user_authority_authority_name` FOREIGN KEY (`authority_name`) REFERENCES `treadstone_authority` (`name`),
  CONSTRAINT `fk_user_authority_user_id` FOREIGN KEY (`user_id`) REFERENCES `treadstone_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `treadstone_user_authority` WRITE;
/*!40000 ALTER TABLE `treadstone_user_authority` DISABLE KEYS */;

INSERT INTO `treadstone_user_authority` (`user_id`, `authority_name`)
VALUES
	(2,'ROLE_USER'),
	(5,'ROLE_USER');

/*!40000 ALTER TABLE `treadstone_user_authority` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
