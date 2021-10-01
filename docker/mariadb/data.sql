--
-- Dumping data for table `applicants_types`
--

INSERT INTO `applicants_types` (`id`, `name`, `grp`) VALUES
('SYS', 'System', ''),
('NGO', 'Non-governmental Organisation', ''),
('PRIV', 'Private Sector', ''),
('GO', 'Governmental Organisation', ''),
('IGO', 'Intergovernmental Organisation', ''),
('PHIL', 'Philantropist', ''),
('CSO', 'Civil Society Organisation', '');


--
-- Dumping data for table `configs`
--

INSERT INTO `configs` (`config_id`, `config_key`, `config_value`) VALUES
(1, 'app_theme', ''),
(2, 'app_title', 'OOC Platform'),
(3, 'app_host', ''),
(4, 'app_description', ''),
(5, 'app_mode', '3'),
(6, 'app_disclaimer', '<h1>Disclaimer</h1>'),
(7, 'scheduler_active', '0'),
(8, 'scheduler_expression', '{\"scheduler-day\":\"1\",\"scheduler-month\":\"1\",\"scheduler-hour\":\"0\",\"scheduler-minute\":\"0\",\"scheduler-weekday\":\"0\",\"scheduler-selector\":\"year\",\"scheduler-expression\":\"0 0 1 1 *\"}'),
(9, 'host_flag', 'PW'),
(10, 'host_country', 'Palau'),
(11, 'host_conf_site', ''),
(12, 'host_conf_start', ''),
(13, 'host_conf_end', ''),
(14, 'host_camp_active', '0');

ALTER TABLE `configs` AUTO_INCREMENT=15;

--
-- Dumping data for table `proposals_themes`
--

INSERT INTO `proposals_themes` (`id`, `name`, `grp`) VALUES
('CC', 'Climate Change', ''),
('MP', 'Marine Pollution', ''),
('MPA', 'Marine Protected Area', ''),
('MS', 'Maritime Security', ''),
('SBE', 'Sustainable Blue Economy', ''),
('SF', 'Sustainable Fisheries', ''),
('FOOC', 'Future Our Ocean Conferences', '');

--
-- Dumping data for table `applicants`
--

INSERT INTO `applicants` (`applicant_id`, `applicant_legal_name`, `applicant_type`, `applicant_street`, `applicant_city`, `applicant_nuts_1`, `applicant_country_code`, `applicant_post_code`, `applicant_web_page`, `applicant_osm_lat`, `applicant_osm_lng`, `applicant_uid`, `applicant_is_host`, `applicant_created`, `applicant_updated`) VALUES
(1, 'System', 'SYS', 'Unknown', 'Unknown', '', 'BE', '0', '', '0.0000', '0.0000', '', 1, '2019-04-30 10:07:03', '2019-04-30 10:07:03'),
(2, 'Hosting Organisation', 'PRIV', '93, Rue Joseph II', 'Brussels', '', 'BE', '1000', '', '50.8449', '4.3757', '', 1, '2019-04-30 10:07:03', '2019-04-30 10:07:03');

ALTER TABLE `applicants` AUTO_INCREMENT=3;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_role`, `user_organisation`, `user_email`, `user_office`, `user_function`, `user_name`, `user_firstname`, `user_phone`, `user_password`, `user_active`, `user_prefs`, `user_activated`, `user_status`, `user_accept_pub`, `user_recovery_code`, `user_recovery_date`, `user_updated`, `user_created`) VALUES
(1, 'system', 1, 'noreply@ooc.com', '', '', '', 'System', '', '54b53072540eeeb8f8e9343e71f28176', 0, NULL, 0, 'A', 0, '', '2019-04-30 10:07:03', '2019-04-30 10:07:03', '2019-04-30 10:07:03'),
(2, 'host', 2, 'admin@ooc.com', 'PTM', 'Developer', 'Admin', 'Admin', '+32496101011', '21232f297a57a5a743894a0e4a801fc3', 1, NULL, 1, 'A', 0, '', '2019-04-30 10:07:03', '2019-04-30 10:07:03', '2019-04-30 10:07:03');
ALTER TABLE `users` AUTO_INCREMENT=3;


