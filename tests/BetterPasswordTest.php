<?php

if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
require_once(dirname(__FILE__).DS.'..'.DS.'better-password.php');

/**
 * BetterPassword test
 *
 * @version 0.1
 * @author Ezra Pool <ezra@tsdme.nl>
 */
class BetterPasswordTest extends PHPUnit_Framework_TestCase {

	/**
	 *
	 * @global MockWpdb $wpdb
	 */
	public function setUp() {
		global $wpdb;
		$wpdb = $this->getMock('wpdb', array('update', 'query'));
		$wpdb->users = 'wp_users';
	}
	/**
	 * Test the functionality of better_check_password
	 *
	 * @covers better_check_password
	 * @verison 0.1
	 * @since 0.1
	 * @author Ezra Pool <ezra@tsdme.nl>
	 * @global MockWpdb $wpdb
	 */
	public function test_better_check_password() {
		global $wpdb;
		
		$wpdb->expects($this->atLeastOnce())
			 ->method('update')
			 ->will($this->returnValue(true));
		
		$this->assertTrue(better_check_password(false, 'thisisatest', '$2y$10$SYPLzZzQdO2jac5tkaiirOmc0f0zv/ro41Yoo709e2Axz9owuE5KS', 0));
		$this->assertFalse(better_check_password(false, 'thisisthewrongpassword', '$2y$10$SYPLzZzQdO2jac5tkaiirOmc0f0zv/ro41Yoo709e2Axz9owuE5KS', 0));

		//If first argument is true (already verified), the function should call wpdb::update and simply return true;
		$this->assertTrue(better_check_password(true, 'thisisatest', '$2y$10$SYPLzZzQdO2jac5tkaiirOmc0f0zv/ro41Yoo709e2Axz9owuE5KS', 0));
	}

	/**
	 * Test the functionality of wp_hash_password
	 *
	 * @covers wp_hash_password
	 * @version 0.1
	 * @since 0.1
	 * @author Ezra Pool <ezra@tsdme.nl>
	 */
	public function test_wp_hash_password() {
		$this->assertNotEmpty(wp_hash_password('thisisatest'));
	}

	/**
	 * Test the functionality of better_password_activate
	 *
	 * @covers better_password_activate
	 * @version 0.1
	 * @since 0.1
	 * @author Ezra Pool <ezra@tsdme.nl>
	 * @global MockWpdb $wpdb
	 */
	public function test_better_password_activate() {
		global $wpdb;
		
		$wpdb->expects($this->once())
			 ->method('query')
			 ->will($this->returnValue(true));
		
		$this->assertEmpty(better_password_activate());
	}
}

/* Wordpress API functions that the code relies on, sadly, I don't know how to mock standard functions, so I simply create stubs here */
function add_filter($name, $callback, $priority, $args) { return true; }
function register_activation_hook($file, $callback){ return true; }
function wp_cache_delete($user_id, $name) { return true; }

?>
