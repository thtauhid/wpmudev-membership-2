<?php

function M_Upgrade($from = false) {

	switch($from) {

		case 1:
		case 2:		M_Alterfor2();
					break;

		case 3:		M_Alterfor3();
					break;

		case 4:
		case 5:		M_Alterfor4();
					break;

		case 6:		M_Alterfor4();
					M_Alterfor5();
					break;

		case 7:		M_Alterfor4();
					M_Alterfor5();
					M_Alterfor6();
					break;

		case false:	M_Createtables();
					break;

		default:	M_Createtables();
					break;
	}

}

function M_Alterfor6() {
	global $wpdb;

	$sql = "ALTER TABLE " . membership_db_prefix($wpdb, 'membership_relationships') . " ADD `usinggateway` varchar(50) NULL DEFAULT 'admin'  AFTER `order_instance`;";
	$wpdb->query( $sql );

	$sql = "ALTER TABLE " . membership_db_prefix($wpdb, 'membership_relationships') . " ADD INDEX  (`user_id`);";
	$wpdb->query( $sql );

	$sql = "ALTER TABLE " . membership_db_prefix($wpdb, 'membership_relationships') . " ADD INDEX  (`sub_id`);";
	$wpdb->query( $sql );

	$sql = "ALTER TABLE " . membership_db_prefix($wpdb, 'membership_relationships') . " ADD INDEX  (`usinggateway`)";;
	$wpdb->query( $sql );

	$sql = "CREATE TABLE IF NOT EXISTS `" . membership_db_prefix($wpdb, 'member_payments') . "` (
	  	`id` bigint(11) NOT NULL auto_increment,
		`member_id` bigint(20) default NULL,
		`sub_id` bigint(20) default NULL,
		`level_id` bigint(20) default NULL,
		`level_order` int(11) default NULL,
		`paymentmade` datetime default NULL,
		`paymentexpires` datetime default NULL,
		PRIMARY KEY  (`id`)
	);";

	$wpdb->query($sql);

}

function M_Alterfor5() {
	global $wpdb;

	$sql = "CREATE TABLE IF NOT EXISTS `" . membership_db_prefix($wpdb, 'pings') . "` (
	  	`id` bigint(20) NOT NULL auto_increment,
		`pingname` varchar(250) default NULL,
		`pinginfo` text,
		`pingtype` varchar(10) default NULL,
		PRIMARY KEY  (`id`)
	);";

	$wpdb->query($sql);

	$sql = "CREATE TABLE IF NOT EXISTS `" . membership_db_prefix($wpdb, 'ping_history') . "` (
	  	`id` bigint(20) NOT NULL auto_increment,
		`ping_id` bigint(20) default NULL,
		`ping_sent` timestamp NULL default NULL,
		`ping_info` text,
		`ping_return` text,
		PRIMARY KEY  (`id`),
		KEY `ping_id` (`ping_id`)
	);";

	$wpdb->query($sql);

	$sql = "CREATE TABLE IF NOT EXISTS `" . membership_db_prefix($wpdb, 'levelmeta') . "` (
	  	`id` bigint(20) NOT NULL auto_increment,
		`level_id` bigint(20) default NULL,
		`meta_key` varchar(250) default NULL,
		`meta_value` text,
		`meta_stamp` timestamp NULL default NULL on update CURRENT_TIMESTAMP,
		PRIMARY KEY  (`id`),
		UNIQUE KEY `level_id` (`level_id`,`meta_key`)
	);";

	$wpdb->query($sql);

	$sql = "CREATE TABLE IF NOT EXISTS `" . membership_db_prefix($wpdb, 'subscriptionmeta') . "` (
	  	`id` bigint(20) NOT NULL auto_increment,
		`sub_id` bigint(20) default NULL,
		`meta_key` varchar(250) default NULL,
		`meta_value` text,
		`meta_stamp` timestamp NULL default NULL on update CURRENT_TIMESTAMP,
		PRIMARY KEY  (`id`),
		UNIQUE KEY `sub_id` (`sub_id`,`meta_key`)
	);";

	$wpdb->query($sql);
}

function M_Alterfor4() {
	global $wpdb;

	$sql = "CREATE TABLE IF NOT EXISTS `" . membership_db_prefix($wpdb, 'urlgroups') . "` (
	  `id` bigint(20) NOT NULL auto_increment,
	  `groupname` varchar(250) default NULL,
	  `groupurls` text,
	  `isregexp` int(11) default '0',
	  `stripquerystring` int(11) default '0',
	  PRIMARY KEY  (`id`)
	);";

	$wpdb->query($sql);

	$sql = "CREATE TABLE IF NOT EXISTS `" . membership_db_prefix($wpdb, 'communications') . "` (
	  `id` bigint(11) NOT NULL auto_increment,
	  `subject` varchar(250) default NULL,
	  `message` text,
	  `periodunit` int(11) default NULL,
	  `periodtype` varchar(5) default NULL,
	  `periodprepost` varchar(5) default NULL,
	  `lastupdated` timestamp NULL default NULL on update CURRENT_TIMESTAMP,
	  `active` int(11) default '0',
	  `periodstamp` bigint(20) default '0',
	  PRIMARY KEY  (`id`)
	);";

	$wpdb->query($sql);
}

function M_Alterfor3() {
	global $wpdb;

	$sql = "RENAME TABLE " . membership_db_prefix($wpdb, 'membership_levels', false) . " TO " . membership_db_prefix($wpdb, 'membership_levels') . ";";
	$wpdb->query($sql);

	$sql = "RENAME TABLE " . membership_db_prefix($wpdb, 'membership_relationships', false) . " TO " . membership_db_prefix($wpdb, 'membership_relationships') . ";";
	$wpdb->query($sql);

	$sql = "RENAME TABLE " . membership_db_prefix($wpdb, 'membership_rules', false) . " TO " . membership_db_prefix($wpdb, 'membership_rules') . ";";
	$wpdb->query($sql);

	$sql = "RENAME TABLE " . membership_db_prefix($wpdb, 'subscriptions', false) . " TO " . membership_db_prefix($wpdb, 'subscriptions') . ";";
	$wpdb->query($sql);

	$sql = "RENAME TABLE " . membership_db_prefix($wpdb, 'subscriptions_levels', false) . " TO " . membership_db_prefix($wpdb, 'subscriptions_levels') . ";";
	$wpdb->query($sql);

	$sql = "RENAME TABLE " . membership_db_prefix($wpdb, 'subscription_transaction', false) . " TO " . membership_db_prefix($wpdb, 'subscription_transaction') . ";";
	$wpdb->query($sql);

}

function M_Alterfor2() {
	global $wpdb;

	$sql = "ALTER TABLE `" . membership_db_prefix($wpdb, 'subscriptions_levels') . "` ADD `level_period_unit` varchar(1) NULL DEFAULT 'd'  AFTER `level_order`;";

	$wpdb->query($sql);
}

function M_Createtables() {

	global $wpdb;

	$sql = "CREATE TABLE IF NOT EXISTS `" . membership_db_prefix($wpdb, 'membership_levels') . "` (
	  `id` bigint(20) NOT NULL auto_increment,
	  `level_title` varchar(250) default NULL,
	  `level_slug` varchar(250) default NULL,
	  `level_active` int(11) default '0',
	  `level_count` bigint(20) default '0',
	  PRIMARY KEY  (`id`)
	);";

	$wpdb->query($sql);

	$sql = "CREATE TABLE IF NOT EXISTS `" . membership_db_prefix($wpdb, 'membership_relationships') . "` (
	  	`rel_id` bigint(20) NOT NULL auto_increment,
		`user_id` bigint(20) default '0',
		`sub_id` bigint(20) default '0',
		`level_id` bigint(20) default '0',
		`startdate` datetime default NULL,
		`updateddate` datetime default NULL,
		`expirydate` datetime default NULL,
		`order_instance` bigint(20) default '0',
		`usinggateway` varchar(50) default 'admin',
		PRIMARY KEY  (`rel_id`),
		KEY `user_id` (`user_id`),
		KEY `sub_id` (`sub_id`),
		KEY `usinggateway` (`usinggateway`)
	);";

	$wpdb->query($sql);

	$sql = "CREATE TABLE IF NOT EXISTS `" . membership_db_prefix($wpdb, 'membership_rules') . "` (
	  `level_id` bigint(20) NOT NULL default '0',
	  `rule_ive` varchar(20) NOT NULL default '',
	  `rule_area` varchar(20) NOT NULL default '',
	  `rule_value` text,
	  `rule_order` int(11) default '0',
	  PRIMARY KEY  (`level_id`,`rule_ive`,`rule_area`),
	  KEY `rule_area` (`rule_area`),
	  KEY `rule_ive` (`rule_ive`)
	);";

	$wpdb->query($sql);

	$sql = "CREATE TABLE IF NOT EXISTS `" . membership_db_prefix($wpdb, 'subscriptions') . "` (
	  `id` bigint(20) NOT NULL auto_increment,
	  `sub_name` varchar(200) default NULL,
	  `sub_active` int(11) default '0',
	  `sub_public` int(11) default '0',
	  `sub_count` bigint(20) default '0',
	  `sub_description` text,
	  PRIMARY KEY  (`id`)
	);";

	$wpdb->query($sql);

	$sql = "CREATE TABLE IF NOT EXISTS `" . membership_db_prefix($wpdb, 'subscriptions_levels') . "` (
	  	`sub_id` bigint(20) default NULL,
		`level_id` bigint(20) default NULL,
		`level_period` int(11) default NULL,
		`sub_type` varchar(20) default NULL,
		`level_price` int(11) default '0',
		`level_currency` varchar(5) default NULL,
		`level_order` bigint(20) default '0',
		`level_period_unit` varchar(1) default 'd',
		KEY `sub_id` (`sub_id`),
	 	KEY `level_id` (`level_id`)
	);";

	$wpdb->query($sql);

	$sql = "CREATE TABLE IF NOT EXISTS `" . membership_db_prefix($wpdb, 'subscription_transaction') . "` (
	  `transaction_ID` bigint(20) unsigned NOT NULL auto_increment,
	  `transaction_subscription_ID` bigint(20) NOT NULL default '0',
	  `transaction_user_ID` bigint(20) NOT NULL default '0',
	  `transaction_sub_ID` bigint(20) default '0',
	  `transaction_paypal_ID` varchar(30) default NULL,
	  `transaction_payment_type` varchar(20) default NULL,
	  `transaction_stamp` bigint(35) NOT NULL default '0',
	  `transaction_total_amount` bigint(20) default NULL,
	  `transaction_currency` varchar(35) default NULL,
	  `transaction_status` varchar(35) default NULL,
	  `transaction_duedate` date default NULL,
	  `transaction_gateway` varchar(50) default NULL,
	  `transaction_note` text,
	  `transaction_expires` datetime default NULL,
	  PRIMARY KEY  (`transaction_ID`),
	  KEY `transaction_gateway` (`transaction_gateway`),
	  KEY `transaction_subscription_ID` (`transaction_subscription_ID`)
	);";

	$wpdb->query($sql);

	$sql = "CREATE TABLE IF NOT EXISTS `" . membership_db_prefix($wpdb, 'urlgroups') . "` (
	  `id` bigint(20) NOT NULL auto_increment,
	  `groupname` varchar(250) default NULL,
	  `groupurls` text,
	  `isregexp` int(11) default '0',
	  `stripquerystring` int(11) default '0',
	  PRIMARY KEY  (`id`)
	);";

	$wpdb->query($sql);

	$sql = "CREATE TABLE IF NOT EXISTS `" . membership_db_prefix($wpdb, 'communications') . "` (
	  `id` bigint(11) NOT NULL auto_increment,
	  `subject` varchar(250) default NULL,
	  `message` text,
	  `periodunit` int(11) default NULL,
	  `periodtype` varchar(5) default NULL,
	  `periodprepost` varchar(5) default NULL,
	  `lastupdated` timestamp NULL default NULL on update CURRENT_TIMESTAMP,
	  `active` int(11) default '0',
	  `periodstamp` bigint(20) default '0',
	  PRIMARY KEY  (`id`)
	);";

	$wpdb->query($sql);

	$sql = "CREATE TABLE IF NOT EXISTS `" . membership_db_prefix($wpdb, 'pings') . "` (
	  	`id` bigint(20) NOT NULL auto_increment,
		`pingname` varchar(250) default NULL,
		`pingurl` varchar(250) default NULL,
		`pinginfo` text,
		`pingtype` varchar(10) default NULL,
		PRIMARY KEY  (`id`)
	);";

	$wpdb->query($sql);

	$sql = "CREATE TABLE IF NOT EXISTS `" . membership_db_prefix($wpdb, 'ping_history') . "` (
	  	`id` bigint(20) NOT NULL auto_increment,
		`ping_id` bigint(20) default NULL,
		`ping_sent` timestamp NULL default NULL,
		`ping_info` text,
		`ping_return` text,
		PRIMARY KEY  (`id`),
		KEY `ping_id` (`ping_id`)
	);";

	$wpdb->query($sql);

	$sql = "CREATE TABLE IF NOT EXISTS `" . membership_db_prefix($wpdb, 'levelmeta') . "` (
	  	`id` bigint(20) NOT NULL auto_increment,
		`level_id` bigint(20) default NULL,
		`meta_key` varchar(250) default NULL,
		`meta_value` text,
		`meta_stamp` timestamp NULL default NULL on update CURRENT_TIMESTAMP,
		PRIMARY KEY  (`id`),
		UNIQUE KEY `level_id` (`level_id`,`meta_key`)
	);";

	$wpdb->query($sql);

	$sql = "CREATE TABLE IF NOT EXISTS `" . membership_db_prefix($wpdb, 'subscriptionmeta') . "` (
	  	`id` bigint(20) NOT NULL auto_increment,
		`sub_id` bigint(20) default NULL,
		`meta_key` varchar(250) default NULL,
		`meta_value` text,
		`meta_stamp` timestamp NULL default NULL on update CURRENT_TIMESTAMP,
		PRIMARY KEY  (`id`),
		UNIQUE KEY `sub_id` (`sub_id`,`meta_key`)
	);";

	$wpdb->query($sql);

	$sql = "CREATE TABLE IF NOT EXISTS `" . membership_db_prefix($wpdb, 'member_payments') . "` (
	  	`id` bigint(11) NOT NULL auto_increment,
		`member_id` bigint(20) default NULL,
		`sub_id` bigint(20) default NULL,
		`level_id` bigint(20) default NULL,
		`level_order` int(11) default NULL,
		`paymentmade` datetime default NULL,
		`paymentexpires` datetime default NULL,
		PRIMARY KEY  (`id`)
	);";

	$wpdb->query($sql);

	do_action( 'membership_create_new_tables' );
}

?>