<?php
/*
Plugin Name: Better Password
Plugin URI:
Description: Changes from using PHPass to using password_* functions. Updates all passwords automatically on first logon and when PASSWORD_DEFAULT algorithm changes.
Version: 0.1
Author: Ezra Pool <ezra@tsdme.nl>
Author URI:
License: Copyrighted Ezra Pool
*/

require_once 'vendor/password_compat/lib/password.php';

/**
 * Validate the users password and update the password if needed.
 *
 * DO NOT CALL DIRECTLY, USED AS FILTER FOR: check_password
 *
 * @global wpdb $wpdb
 * @param boolean $check
 * @param string $password
 * @param string $hash
 * @param int $user_id
 * @return boolean
 *
 * @version 0.1
 * @since 0.1
 * @author Ezra Pool <ezra@tsdme.nl>
 */
function better_check_password($check, $password, $hash, $user_id) {
	global $wpdb;
	
	if ($check) {
		/* If already logged in, probably still using Phpass format. */
		$hash = password_hash($password,  PASSWORD_DEFAULT);

		/* Store new hash in db */
		$wpdb->update($wpdb->users, array('user_pass' => $hash, 'user_activation_key' => ''), array('ID' => $user_id) );
		wp_cache_delete($user_id, 'users');
		
		return true;
	}

	/* Verify the password using password_verify and update when it needs a rehash. */
	if (password_verify($password, $hash)) {
		if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
			$hash = password_hash($password,  PASSWORD_DEFAULT);
			
			/* Store new hash in db */
			$wpdb->update($wpdb->users, array('user_pass' => $hash, 'user_activation_key' => ''), array('ID' => $user_id) );
			wp_cache_delete($user_id, 'users');
		}

		return true;
	}

	return false;
}
add_filter('check_password', 'better_check_password', 0, 4);

if ( !function_exists('wp_hash_password') ) :
/**
 * Create a hash (encrypt) of a plain text password.
 *
 * @uses password_hash
 * @param string $password Plain text user password to hash
 * @return string The hash string of the password
 *
 * @since 0.1
 * @version 0.1
 * @author Ezra Pool <ezra@tsdme.nl>
 */
function wp_hash_password($password) {
	return password_hash($password,  PASSWORD_DEFAULT);
}
endif;

/**
 * Updates the Wordpress Databases users table to widen the user_pass column to 255 characters.
 * @global wpdb $wpdb
 */
function better_password_activate(){
	global $wpdb;

	//	PHP >= 5.3.7
	if (version_compare(PHP_VERSION, '5.3.7') < 0) {
		die('Sorry, I need at least PHP 5.3.7.');
	}

	$wpdb->query("ALTER TABLE `{$wpdb->users}` CHANGE COLUMN `user_pass` `user_pass` VARCHAR(255) NOT NULL DEFAULT ''");
}
register_activation_hook( __FILE__, 'better_password_activate' );

?>