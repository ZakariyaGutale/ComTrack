-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 20, 2018 at 12:03 PM
-- Server version: 5.5.59-0+deb8u1
-- PHP Version: 5.6.33-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

SET character_set_server = 'utf8mb4';
SET collation_server = 'utf8mb4_general_ci';

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `dg_mare`
--

-- --------------------------------------------------------

--
-- Table structure for table `applicants`
--

CREATE TABLE IF NOT EXISTS `applicants` (
  `applicant_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `applicant_legal_name` varchar(128) NOT NULL,
  `applicant_type` varchar(16) NULL,
  `applicant_street` varchar(128) NOT NULL DEFAULT '',
  `applicant_city` varchar(32) NOT NULL DEFAULT '',
  `applicant_nuts_1` varchar(6) NOT NULL DEFAULT '',
  `applicant_country_code` varchar(8) NOT NULL DEFAULT '',
  `applicant_post_code` varchar(8) NOT NULL DEFAULT '',
  `applicant_web_page` varchar(256) DEFAULT NULL DEFAULT '',
  `applicant_osm_lat` decimal(7,4) DEFAULT '0',
  `applicant_osm_lng` decimal(7,4) DEFAULT '0',
  `applicant_uid` varchar(60) NOT NULL DEFAULT '',
  `applicant_is_host` tinyint(1) NOT NULL DEFAULT '0',
  `applicant_created` datetime DEFAULT NULL,
  `applicant_updated` datetime NOT NULL,
  PRIMARY KEY (`applicant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `applicants_types`
--

CREATE TABLE IF NOT EXISTS `applicants_types` (
  `id` varchar(32) NOT NULL,
  `name` varchar(64) NOT NULL,
  `grp` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `proposals`
--

CREATE TABLE IF NOT EXISTS `proposals` (
  `proposal_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `proposal_theme` varchar(16) NOT NULL,
  `proposal_year` smallint(6) NOT NULL,
  `proposal_language` varchar(32) NOT NULL DEFAULT 'English',
  `proposal_announcer` varchar(128) NOT NULL,
  `proposal_title` varchar(1024) NOT NULL,
  `proposal_abstract` text NOT NULL DEFAULT '',
  `proposal_website` varchar(2048) NOT NULL DEFAULT '',
  `proposal_budget` decimal(15,2) NOT NULL,
  `proposal_status` varchar(32) NOT NULL DEFAULT 'draft',
  `proposal_ontrack` tinyint(1) NOT NULL DEFAULT '0',
  `proposal_deadline` date,
  `proposal_area_active` tinyint(1) NOT NULL DEFAULT '0',
  `proposal_area` int(11) NOT NULL DEFAULT '0',
  `proposal_completion` tinyint(3) DEFAULT '0',
  `proposal_impact` text NOT NULL DEFAULT '',
  `proposal_host` bigint(20) DEFAULT NULL,
  `proposal_schedule` date,
  `proposal_updated` datetime,
  `proposal_created` datetime,
  PRIMARY KEY (`proposal_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `proposals_applicants`
--

CREATE TABLE IF NOT EXISTS `proposals_applicants` (
  `pa_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `fk_proposal_id` bigint(20) NOT NULL,
  `fk_applicant_id` bigint(20) NOT NULL,
  PRIMARY KEY (`pa_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `proposals_sites`
--

CREATE TABLE IF NOT EXISTS `proposals_sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_proposal_id` bigint(20) NOT NULL,
  `name` varchar(256) NOT NULL DEFAULT '',
  `lat` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `lng` decimal(10,5) NOT NULL DEFAULT '0.00000',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `proposals_themes`
--

CREATE TABLE IF NOT EXISTS `proposals_themes` (
  `id` varchar(16) NOT NULL,
  `name` varchar(64) NOT NULL,
  `grp` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_role` varchar(16) NOT NULL DEFAULT 'poc',
  `user_organisation` bigint(20) DEFAULT '0',
  `user_email` varchar(128) NOT NULL DEFAULT '',
  `user_office` varchar(128) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `user_function` varchar(128) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `user_name` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `user_firstname` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `user_phone` varchar(32) NOT NULL DEFAULT '',
  `user_password` varchar(60) NOT NULL DEFAULT '',
  `user_active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `user_prefs` text CHARACTER SET utf8,
  `user_activated` tinyint(1) NOT NULL DEFAULT '0',
  `user_status` varchar(1) NOT NULL DEFAULT '',
  `user_accept_pub` tinyint(1) NOT NULL DEFAULT '0',
  `user_recovery_code` varchar(60) NOT NULL DEFAULT '',
  `user_recovery_date` datetime,
  `user_updated` datetime NOT NULL,
  `user_created` datetime NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--

CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) DEFAULT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `configs`
--

CREATE TABLE IF NOT EXISTS `configs` (
  `config_id` int(11) NOT NULL,
  `config_key` varchar(32) NOT NULL,
  `config_value` varchar(4096) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
--
-- Table structure for table `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
  `id` bigint(20) NOT NULL,
  `date` datetime NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `proposal_id` bigint(20) NOT NULL,
  `status` varchar(32) NOT NULL,
  `comment` text NOT NULL,
  `detail` varchar(256) NOT NULL,
  `icon` varchar(50) NOT NULL DEFAULT '',
  `class` varchar(50) NOT NULL DEFAULT '',
  `is_public` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE IF NOT EXISTS `countries` (
  `country_id` bigint(20) NOT NULL,
  `country_name` varchar(256) NOT NULL,
  `country_alpha_2` varchar(2) NOT NULL,
  `country_alpha_3` varchar(3) NOT NULL,
  `country_code` int(10) NOT NULL,
  `country_iso_3166_2` varchar(20) NOT NULL,
  `country_region` varchar(256),
  `country_sub_region` varchar(256),
  `country_intermediate_region` varchar(256),
  `country_region_code` int(10),
  `country_sub_region_code` int(10),
  `country_intermediate_region_code` int(10),
  PRIMARY KEY (`country_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `external_links` (
  `link_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `link_description` varchar(255) NOT NULL,
  `link_url` varchar(2048) NOT NULL DEFAULT '',
  `proposal_id` bigint(20) NOT NULL,
  PRIMARY KEY (`link_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `campaigns` (
  `camp_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `camp_start` varchar(256) DEFAULT NULL,
  `camp_end` varchar(256) DEFAULT NULL,
  `camp_new_target`smallint(6) DEFAULT NULL,
  `camp_update_target`smallint(6) DEFAULT NULL,
  `existing_commitments` smallint(6) DEFAULT NULL,
  `updated_commitments` smallint(6) DEFAULT NULL,
  `closed_commitments` smallint(6) DEFAULT NULL,
  `new_commitments` smallint(6) DEFAULT NULL,
  `draft_commitments` smallint(6) DEFAULT NULL,
  `approved_commitments` smallint(6) DEFAULT NULL,
  `statistics` varchar(4096) DEFAULT NULL,
  `camp_is_active` tinyint(1) DEFAULT 0 NOT NULL,
  `camp_is_finished` tinyint(1) DEFAULT 0 NOT NULL,
  `camp_created` datetime DEFAULT NULL,
  `camp_updated` datetime NOT NULL,
  PRIMARY KEY (`camp_id`)
)  ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `external_links`
--

-- --------------------------------------------------------
--
-- Indexes for table `ci_sessions`
--
ALTER TABLE `ci_sessions`
 ADD PRIMARY KEY (`session_id`), ADD KEY `last_activity` (`last_activity`);

--
-- Indexes for table `configs`
--
ALTER TABLE `configs`
 ADD PRIMARY KEY (`config_id`), ADD UNIQUE KEY `Keys` (`config_key`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
 ADD KEY `date` (`date`);
 --- ADD KEY `proposal_id` (`proposal_id`), ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
 ADD KEY `country_name` (`country_name`),
 ADD KEY `country_alpha_2` (`country_alpha_2`);
 --- ADD KEY `proposal_id` (`proposal_id`), ADD KEY `user_id` (`user_id`);
 
--
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD KEY `uacc_group_fk` (`user_organisation`), ADD KEY `uacc_email` (`user_email`), ADD KEY `uacc_username` (`user_name`);
 
--
-- Indexes for table `applicants`
-- 
ALTER TABLE `applicants`
 ADD FULLTEXT KEY `applicant_search` (`applicant_legal_name`);
 
-- --------------------------------------------------------

ALTER TABLE `proposals_applicants`
	ADD FOREIGN KEY (`fk_applicant_id`)
	REFERENCES applicants (`applicant_id`)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;

ALTER TABLE `users`
	ADD FOREIGN KEY (`user_organisation`)
	REFERENCES applicants (`applicant_id`)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;

ALTER TABLE `applicants`
	ADD FOREIGN KEY (`applicant_type`)
	REFERENCES applicants_types (`id`)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;

ALTER TABLE `proposals_applicants`
	ADD FOREIGN KEY (`fk_proposal_id`)
	REFERENCES proposals (`proposal_id`)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;

ALTER TABLE `proposals_sites`
	ADD FOREIGN KEY (`fk_proposal_id`)
	REFERENCES proposals (`proposal_id`)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;

ALTER TABLE `proposals`
	ADD FOREIGN KEY (`proposal_theme`)
	REFERENCES proposals_themes (`id`)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;

ALTER TABLE `logs`
  ADD FOREIGN KEY (`user_id`)
  REFERENCES users (`user_id`)
  ON UPDATE RESTRICT
	ON DELETE RESTRICT
;

ALTER TABLE `logs`
  ADD FOREIGN KEY (`proposal_id`)
  REFERENCES proposals (`proposal_id`)
  ON UPDATE RESTRICT
	ON DELETE RESTRICT
;

ALTER TABLE `external_links`
	ADD FOREIGN KEY (`proposal_id`)
	REFERENCES proposals (`proposal_id`)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;

-- --------------------------------------------------------

--
-- AUTO_INCREMENT for dumped tables
--


-- AUTO_INCREMENT for table `configs`
--
ALTER TABLE `configs`
MODIFY `config_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;

-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;

ALTER TABLE `countries`
MODIFY `country_id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;

--
ALTER TABLE `proposals_applicants`
MODIFY `pa_id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `proposals_sites`
--
ALTER TABLE `proposals_sites`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

