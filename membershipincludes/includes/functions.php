<?php
function load_membership_plugins() {
	if ( is_dir( membership_dir('membershipincludes/plugins') ) ) {
		if ( $dh = opendir( membership_dir('membershipincludes/plugins') ) ) {
			$mem_plugins = array ();
			while ( ( $plugin = readdir( $dh ) ) !== false )
				if ( substr( $plugin, -4 ) == '.php' )
					$mem_plugins[] = $plugin;
			closedir( $dh );
			sort( $mem_plugins );
			foreach( $mem_plugins as $mem_plugin )
				include_once( membership_dir('membershipincludes/plugins/' . $mem_plugin) );
		}
	}

	do_action( 'membership_plugins_loaded' );

}

function set_membership_url($base) {

	global $M_membership_url;

	if(defined('WPMU_PLUGIN_URL') && defined('WPMU_PLUGIN_DIR') && file_exists(WPMU_PLUGIN_DIR . '/' . basename($base))) {
		$M_membership_url = trailingslashit(WPMU_PLUGIN_URL);
	} elseif(defined('WP_PLUGIN_URL') && defined('WP_PLUGIN_DIR') && file_exists(WP_PLUGIN_DIR . '/membership/' . basename($base))) {
		$M_membership_url = trailingslashit(WP_PLUGIN_URL . '/membership');
	} else {
		$M_membership_url = trailingslashit(WP_PLUGIN_URL . '/membership');
	}

}

function set_membership_dir($base) {

	global $M_membership_dir;

	if(defined('WPMU_PLUGIN_DIR') && file_exists(WPMU_PLUGIN_DIR . '/' . basename($base))) {
		$M_membership_dir = trailingslashit(WPMU_PLUGIN_DIR);
	} elseif(defined('WP_PLUGIN_DIR') && file_exists(WP_PLUGIN_DIR . '/membership/' . basename($base))) {
		$M_membership_dir = trailingslashit(WP_PLUGIN_DIR . '/membership');
	} else {
		$M_membership_dir = trailingslashit(WP_PLUGIN_DIR . '/membership');
	}


}

function membership_url($extended) {

	global $M_membership_url;

	return $M_membership_url . $extended;

}

function membership_dir($extended) {

	global $M_membership_dir;

	return $M_membership_dir . $extended;


}

function membership_upload_path() {

	// Get the fallback file location first
	$path = trailingslashit(get_option('home')) . get_option('upload_path');
	// if an override exists, then use that.
	$path = get_option('fileupload_url', $path);
	// return whatever we have left.
	return $path;

}

function membership_is_active($userdata, $password) {

	global $wpdb;

	// Checks if this member is an active one.
	if(!empty($userdata) && !is_wp_error($userdata)) {
		$id = $userdata->ID;

		if(get_usermeta($id, $wpdb->prefix . 'membership_active', true) == 'no') {
			return new WP_Error('member_inactive', __('Sorry, this account is not active.', 'membership'));
		}

	}

	return $userdata;

}

add_filter('wp_authenticate_user', 'membership_is_active', 30, 2);

function membership_assign_subscription($user_id) {

	global $M_options;

	if(!empty($M_options['freeusersubscription'])) {
		$member = new M_Membership($user_id);
		if($member) {
			$member->create_subscription($M_options['freeusersubscription']);
		}
	}

}

add_action('user_register', 'membership_assign_subscription', 30);

function membership_db_prefix(&$wpdb, $table, $useprefix = true) {

	if($useprefix) {
		$membership_prefix = 'm_';
	} else {
		$membership_prefix = '';
	}

	if( defined('MEMBERSHIP_GLOBAL_TABLES') && MEMBERSHIP_GLOBAL_TABLES == true ) {
		if(!empty($wpdb->base_prefix)) {
			return $wpdb->base_prefix . $membership_prefix . $table;
		} else {
			return $wpdb->prefix . $membership_prefix . $table;
		}
	} else {
		return $wpdb->prefix . $membership_prefix . $table;
	}

}

function M_can_add_pings() {

	global $M_add_ping;

	return $M_add_ping;
}

function M_set_ping_operations( $pings ) {

	global $M_add_ping;

	$user = wp_get_current_user();

	if(count($pings) >= 2) {
		$M_add_ping = false;
		$user->remove_cap('M_add_ping');
	} else {
		$M_add_ping = true;
		$user->add_cap('M_add_ping');
	}
}

function M_can_add_level() {

	global $M_add_level;

	return $M_add_level;
}

function M_set_level_operations( $levels ) {

	global $M_add_level;

	$user = wp_get_current_user();

	if(count($levels) >= 2) {
		$M_add_level = false;
		$user->remove_cap('M_add_level');
	} else {
		$M_add_level = true;
		$user->add_cap('M_add_level');
	}
}

function M_can_add_sub() {

	global $M_add_subscription;

	return $M_add_subscription;
}

function M_set_sub_operations( $subs ) {

	global $M_add_subscription;

	$user = wp_get_current_user();
	if(count($subs) >= 2) {
		$M_add_subscription = false;
		$user->remove_cap('M_add_subscription');
	} else {
		$M_add_subscription = true;
		$user->add_cap('M_add_subscription');
	}
}

// Template based functions
function current_member() {

	$user = wp_get_current_user();
	$member = new M_Membership( $user->ID );

	if(!empty($member)) {
		return $member;
	} else {
		return false;
	}

}

function current_user_is_member() {

	$user = wp_get_current_user();
	$member = new M_Membership( $user->ID );

	if(!empty($member)) {
		return $member->is_member();
	} else {
		return false;
	}

}

function current_user_has_subscription() {

	$user = wp_get_current_user();
	$member = new M_Membership( $user->ID );

	if(!empty($member)) {
		return $member->has_subscription();
	} else {
		return false;
	}

}

function current_user_on_level( $level_id ) {

	$user = wp_get_current_user();
	$member = new M_Membership( $user->ID );

	if(!empty($member)) {
		return $member->on_level( $level_id, true );
	} else {
		return false;
	}

}

function current_user_on_subscription( $sub_id ) {

	$user = wp_get_current_user();
	$member = new M_Membership( $user->ID );

	if(!empty($member)) {
		return $member->on_sub( $sub_id );
	} else {
		return false;
	}

}

// Functions
if(!function_exists('M_register_rule')) {
	function M_register_rule($rule_name, $class_name, $section) {

		global $M_Rules, $M_SectionRules;

		if(!is_array($M_Rules)) {
			$M_Rules = array();
		}

		if(!is_array($M_SectionRules)) {
			$M_SectionRules = array();
		}

		if(class_exists($class_name)) {
			$M_SectionRules[$section][$rule_name] = $class_name;
			$M_Rules[$rule_name] = $class_name;
		} else {
			return false;
		}

	}
}

function M_remove_old_plugin( $plugins ) {

	if(array_key_exists('membership/membership.php', $plugins) && !in_array('membership.php', (array) array_map('basename', wp_get_active_and_valid_plugins() ))) {
		unset($plugins['membership/membership.php']);
	}

	return $plugins;
}

function get_last_transaction_for_user_and_sub($user_id, $sub_id) {

	global $wpdb;

	$sql = $wpdb->prepare( "SELECT * FROM " . membership_db_prefix($wpdb, 'subscription_transaction') . " WHERE transaction_user_ID = %d and transaction_subscription_ID = %d ORDER BY transaction_stamp DESC LIMIT 0,1", $user_id, $sub_id );

	return $wpdb->get_row( $sql );

}

?>